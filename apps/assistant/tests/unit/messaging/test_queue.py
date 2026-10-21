import asyncio
import json
from collections.abc import AsyncGenerator
from contextlib import AbstractContextManager
from typing import Any
from unittest.mock import AsyncMock, MagicMock, patch

import pytest

from app.messaging.queue import (
    cancel_request,
    consume_subscription,
    finish_processing,
    is_processing_request,
    load_response,
    publish_chunk,
    register_request,
    renew_processing,
    save_response,
    subscribe_stream,
    wait_for_cancellation,
)


def _make_redis_client(eval_return: object = 0) -> AsyncMock:
    r = AsyncMock()
    r.eval = AsyncMock(return_value=eval_return)
    r.get = AsyncMock(return_value=None)
    r.setex = AsyncMock()
    r.publish = AsyncMock()
    r.exists = AsyncMock(return_value=0)
    r.aclose = AsyncMock()
    r.__aenter__ = AsyncMock(return_value=r)
    r.__aexit__ = AsyncMock(return_value=False)

    return r


def _patch_client(r: AsyncMock) -> AbstractContextManager[Any]:
    return patch("app.messaging.queue._make_client", return_value=r)


@pytest.mark.asyncio
async def test_register_request_returns_zero_when_slot_claimed() -> None:
    r = _make_redis_client(eval_return=0)

    with _patch_client(r):
        pos = await register_request({"request_id": "r1", "conversation_id": "c1"})

    assert pos == 0


@pytest.mark.asyncio
async def test_register_request_returns_position_when_queued() -> None:
    r = _make_redis_client(eval_return=2)

    with _patch_client(r):
        pos = await register_request({"request_id": "r2", "conversation_id": "c1"})

    assert pos == 2


@pytest.mark.asyncio
async def test_is_processing_request_true() -> None:
    r = _make_redis_client()
    r.get = AsyncMock(return_value="req-1")

    with _patch_client(r):
        result = await is_processing_request("req-1")

    assert result is True


@pytest.mark.asyncio
async def test_is_processing_request_false() -> None:
    r = _make_redis_client()
    r.get = AsyncMock(return_value="req-99")

    with _patch_client(r):
        result = await is_processing_request("req-1")

    assert result is False


@pytest.mark.asyncio
async def test_renew_processing_returns_true() -> None:
    r = _make_redis_client(eval_return=1)

    with _patch_client(r):
        result = await renew_processing("req-1")

    assert result is True


@pytest.mark.asyncio
async def test_renew_processing_returns_false() -> None:
    r = _make_redis_client(eval_return=0)

    with _patch_client(r):
        result = await renew_processing("req-1")

    assert result is False


@pytest.mark.asyncio
async def test_finish_processing_returns_none_when_no_next() -> None:
    r = _make_redis_client(eval_return=None)

    with _patch_client(r):
        result = await finish_processing("req-1")

    assert result is None


@pytest.mark.asyncio
async def test_finish_processing_returns_next_payload() -> None:
    payload = {"request_id": "req-2", "conversation_id": "c1"}
    r = _make_redis_client(eval_return=json.dumps(payload))

    with _patch_client(r):
        result = await finish_processing("req-1")

    assert result == payload


@pytest.mark.asyncio
async def test_cancel_request_returns_true() -> None:
    r = _make_redis_client(eval_return=1)

    with _patch_client(r):
        result = await cancel_request("req-1")

    assert result is True


@pytest.mark.asyncio
async def test_cancel_request_returns_false_when_zero() -> None:
    r = _make_redis_client(eval_return=0)

    with _patch_client(r):
        result = await cancel_request("req-1")

    assert result is False


@pytest.mark.asyncio
async def test_save_and_load_response() -> None:
    r = _make_redis_client()
    r.get = AsyncMock(return_value="stored content")

    with _patch_client(r):
        await save_response("req-1", "stored content")
        content = await load_response("req-1")

    assert content == "stored content"


@pytest.mark.asyncio
async def test_publish_chunk_calls_publish() -> None:
    r = _make_redis_client()

    with _patch_client(r):
        await publish_chunk("conv-1", "req-1", '{"data":"ok"}')

    r.publish.assert_called_once_with("ai:stream:conv-1:req-1", '{"data":"ok"}')


@pytest.mark.asyncio
async def test_wait_for_cancellation_returns_when_key_exists() -> None:
    mock_pubsub = AsyncMock()
    mock_pubsub.subscribe = AsyncMock()
    mock_pubsub.unsubscribe = AsyncMock()
    mock_pubsub.aclose = AsyncMock()

    mock_r = AsyncMock()
    mock_r.exists = AsyncMock(return_value=1)
    mock_r.pubsub = MagicMock(return_value=mock_pubsub)
    mock_r.aclose = AsyncMock()

    with _patch_client(mock_r):
        await wait_for_cancellation("req-1")

    mock_pubsub.subscribe.assert_called_once()


@pytest.mark.asyncio
async def test_subscribe_stream_delegates_to_consume() -> None:
    done_msg = json.dumps({"success": True, "data": {"done": True}})

    async def fake_listen() -> AsyncGenerator[dict[str, Any], None]:
        yield {"type": "message", "data": done_msg}

    mock_pubsub = AsyncMock()
    mock_pubsub.subscribe = AsyncMock()
    mock_pubsub.listen = fake_listen
    mock_pubsub.unsubscribe = AsyncMock()
    mock_pubsub.aclose = AsyncMock()

    mock_r = AsyncMock()
    mock_r.pubsub = MagicMock(return_value=mock_pubsub)
    mock_r.aclose = AsyncMock()

    with _patch_client(mock_r):
        chunks = [c async for c in subscribe_stream("conv-1", "req-1")]

    assert len(chunks) == 1


@pytest.mark.asyncio
async def test_wait_for_cancellation_receives_message() -> None:
    message = {"type": "message", "data": "1"}
    non_message = {"type": "subscribe", "data": 1}

    async def fake_listen() -> AsyncGenerator[dict[str, Any], None]:
        yield non_message
        yield message

    mock_pubsub = AsyncMock()
    mock_pubsub.subscribe = AsyncMock()
    mock_pubsub.listen = fake_listen
    mock_pubsub.unsubscribe = AsyncMock()
    mock_pubsub.aclose = AsyncMock()

    mock_r = AsyncMock()
    mock_r.exists = AsyncMock(return_value=0)
    mock_r.pubsub = MagicMock(return_value=mock_pubsub)
    mock_r.aclose = AsyncMock()

    with _patch_client(mock_r):
        await wait_for_cancellation("req-1")

    mock_pubsub.subscribe.assert_called_once()


@pytest.mark.asyncio
async def test_consume_subscription_non_dict_inner_breaks() -> None:
    data_with_string_inner = json.dumps({"success": True, "data": "not a dict"})

    async def fake_listen() -> AsyncGenerator[dict[str, Any], None]:
        yield {"type": "message", "data": data_with_string_inner}

    mock_pubsub = AsyncMock()
    mock_pubsub.listen = fake_listen
    mock_pubsub.unsubscribe = AsyncMock()
    mock_pubsub.aclose = AsyncMock()

    mock_r = AsyncMock()
    mock_r.aclose = AsyncMock()

    chunks = [c async for c in consume_subscription(mock_r, mock_pubsub, "conv-1", "req-1")]

    assert len(chunks) == 1
    assert json.loads(chunks[0])["data"] == "not a dict"


@pytest.mark.asyncio
async def test_consume_subscription_breaks_on_json_decode_error() -> None:
    async def fake_listen() -> AsyncGenerator[dict[str, Any], None]:
        yield {"type": "message", "data": "not-json{{{{"}

    mock_pubsub = AsyncMock()
    mock_pubsub.listen = fake_listen
    mock_pubsub.unsubscribe = AsyncMock()
    mock_pubsub.aclose = AsyncMock()

    mock_r = AsyncMock()
    mock_r.aclose = AsyncMock()

    chunks = [c async for c in consume_subscription(mock_r, mock_pubsub, "conv-1", "req-1")]

    assert len(chunks) == 1
    assert chunks[0] == "not-json{{{{"


@pytest.mark.asyncio
async def test_consume_subscription_yields_done_and_stops() -> None:
    done_msg = json.dumps({"success": True, "data": {"done": True}})

    messages: list[dict[str, Any]] = [
        {"type": "subscribe", "data": 1},
        {"type": "message", "data": done_msg},
    ]

    async def fake_listen() -> AsyncGenerator[dict[str, Any], None]:
        for m in messages:
            yield m

    mock_pubsub = AsyncMock()
    mock_pubsub.listen = fake_listen
    mock_pubsub.unsubscribe = AsyncMock()
    mock_pubsub.aclose = AsyncMock()

    mock_r = AsyncMock()
    mock_r.aclose = AsyncMock()

    chunks = [c async for c in consume_subscription(mock_r, mock_pubsub, "conv-1", "req-1")]

    assert len(chunks) == 1
    assert json.loads(chunks[0])["data"]["done"] is True


@pytest.mark.asyncio
async def test_consume_subscription_yields_timeout_error() -> None:
    async def fake_listen() -> AsyncGenerator[dict[str, Any], None]:
        await asyncio.Event().wait()
        yield {"type": "message", "data": "{}"}

    mock_pubsub = AsyncMock()
    mock_pubsub.listen = fake_listen
    mock_pubsub.unsubscribe = AsyncMock()
    mock_pubsub.aclose = AsyncMock()

    mock_r = AsyncMock()
    mock_r.aclose = AsyncMock()

    with patch("app.messaging.queue._STREAM_TIMEOUT", 0):
        chunks = [c async for c in consume_subscription(mock_r, mock_pubsub, "conv-1", "req-1")]

    assert len(chunks) == 1

    data = json.loads(chunks[0])

    assert data["success"] is False
