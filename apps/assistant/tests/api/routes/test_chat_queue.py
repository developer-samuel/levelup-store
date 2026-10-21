import json
from collections.abc import AsyncGenerator
from unittest.mock import AsyncMock, patch

import pytest
from httpx import AsyncClient

from app.routers.chat_queue import queued_sse


@pytest.mark.asyncio
async def test_reattach_no_rabbitmq_returns_done(client: AsyncClient) -> None:
    with patch("app.routers.chat_queue.settings") as s:
        s.rabbitmq_url = None

        response = await client.get("/chat/reattach/conv-1/req-1")

    assert response.status_code == 200
    assert b"done" in response.content


@pytest.mark.asyncio
async def test_reattach_not_processing_no_saved_returns_done(client: AsyncClient) -> None:
    with (
        patch("app.routers.chat_queue.settings") as s,
        patch(
            "app.routers.chat_queue.is_processing_request",
            new_callable=AsyncMock,
            return_value=False,
        ),
        patch("app.routers.chat_queue.load_response", new_callable=AsyncMock, return_value=None),
    ):
        s.rabbitmq_url = "amqp://guest:guest@localhost/"

        response = await client.get("/chat/reattach/conv-1/req-1")

    assert response.status_code == 200
    assert b"done" in response.content


@pytest.mark.asyncio
async def test_reattach_not_processing_with_saved_replays(client: AsyncClient) -> None:
    with (
        patch("app.routers.chat_queue.settings") as s,
        patch(
            "app.routers.chat_queue.is_processing_request",
            new_callable=AsyncMock,
            return_value=False,
        ),
        patch(
            "app.routers.chat_queue.load_response",
            new_callable=AsyncMock,
            return_value="Saved reply",
        ),
    ):
        s.rabbitmq_url = "amqp://guest:guest@localhost/"

        response = await client.get("/chat/reattach/conv-1/req-1")

    assert response.status_code == 200
    assert b"Saved reply" in response.content


@pytest.mark.asyncio
async def test_cancel_no_rabbitmq_returns_false(client: AsyncClient) -> None:
    with patch("app.routers.chat_queue.settings") as s:
        s.rabbitmq_url = None

        response = await client.post("/chat/requests/req-1/cancel")

    assert response.status_code == 200
    assert response.json()["data"]["cancelled"] is False


@pytest.mark.asyncio
async def test_cancel_with_rabbitmq_returns_result(client: AsyncClient) -> None:
    with (
        patch("app.routers.chat_queue.settings") as s,
        patch("app.routers.chat_queue.cancel_request", new_callable=AsyncMock, return_value=True),
    ):
        s.rabbitmq_url = "amqp://guest:guest@localhost/"

        response = await client.post("/chat/requests/req-1/cancel")

    assert response.status_code == 200
    assert response.json()["data"]["cancelled"] is True


@pytest.mark.asyncio
async def test_reattach_processing_streams_chunks(client: AsyncClient) -> None:
    done_msg = json.dumps({"success": True, "data": {"done": True}})

    async def fake_subscribe(conv_id: str, req_id: str) -> AsyncGenerator[str, None]:
        yield done_msg

    with (
        patch("app.routers.chat_queue.settings") as s,
        patch(
            "app.routers.chat_queue.is_processing_request",
            new_callable=AsyncMock,
            return_value=True,
        ),
        patch("app.routers.chat_queue.subscribe_stream", side_effect=fake_subscribe),
    ):
        s.rabbitmq_url = "amqp://guest:guest@localhost/"

        response = await client.get("/chat/reattach/conv-1/req-1")

    assert response.status_code == 200
    assert b"done" in response.content


@pytest.mark.asyncio
async def test_queued_sse_slot_claimed_yields_thinking() -> None:
    done_msg = json.dumps({"success": True, "data": {"done": True}})
    mock_r = AsyncMock()
    mock_pubsub = AsyncMock()

    async def fake_consume(
        r: object,
        pubsub: object,
        conv_id: str,
        req_id: str,
    ) -> AsyncGenerator[str, None]:
        yield done_msg

    with (
        patch(
            "app.routers.chat_queue.open_subscription",
            new_callable=AsyncMock,
            return_value=(mock_r, mock_pubsub),
        ),
        patch("app.routers.chat_queue.register_request", new_callable=AsyncMock, return_value=0),
        patch("app.routers.chat_queue.publish_request", new_callable=AsyncMock),
        patch("app.routers.chat_queue.consume_subscription", side_effect=fake_consume),
    ):
        chunks = [c async for c in queued_sse("hello", "conv-1", "req-1")]

    assert any("thinking" in c for c in chunks)


@pytest.mark.asyncio
async def test_queued_sse_queued_position_yields_queued_event() -> None:
    done_msg = json.dumps({"success": True, "data": {"done": True}})
    mock_r = AsyncMock()
    mock_pubsub = AsyncMock()

    async def fake_consume(
        r: object,
        pubsub: object,
        conv_id: str,
        req_id: str,
    ) -> AsyncGenerator[str, None]:
        yield done_msg

    with (
        patch(
            "app.routers.chat_queue.open_subscription",
            new_callable=AsyncMock,
            return_value=(mock_r, mock_pubsub),
        ),
        patch("app.routers.chat_queue.register_request", new_callable=AsyncMock, return_value=2),
        patch("app.routers.chat_queue.consume_subscription", side_effect=fake_consume),
    ):
        chunks = [c async for c in queued_sse("hello", "conv-1", "req-1")]

    assert any("queued" in c for c in chunks)


@pytest.mark.asyncio
async def test_queued_sse_cancelled_position_yields_done() -> None:
    done_msg = json.dumps({"success": True, "data": {"done": True}})
    mock_r = AsyncMock()
    mock_pubsub = AsyncMock()

    async def fake_consume(
        r: object,
        pubsub: object,
        conv_id: str,
        req_id: str,
    ) -> AsyncGenerator[str, None]:
        yield done_msg

    with (
        patch(
            "app.routers.chat_queue.open_subscription",
            new_callable=AsyncMock,
            return_value=(mock_r, mock_pubsub),
        ),
        patch("app.routers.chat_queue.register_request", new_callable=AsyncMock, return_value=-1),
        patch("app.routers.chat_queue.publish_chunk", new_callable=AsyncMock),
        patch("app.routers.chat_queue.consume_subscription", side_effect=fake_consume),
    ):
        chunks = [c async for c in queued_sse("hello", "conv-1", "req-1")]

    assert any("done" in c for c in chunks)
