import asyncio
import json
from collections.abc import AsyncGenerator
from unittest.mock import AsyncMock, patch

import pytest

from jobs.worker import _maintain_processing_lease, _process, _stream_response


async def _block_forever(_: str) -> None:
    await asyncio.Event().wait()


@pytest.mark.asyncio
async def test_stream_response_publishes_tokens() -> None:
    async def fake_stream(msg: str, conv_id: str) -> AsyncGenerator[str, None]:
        yield json.dumps({"success": True, "data": {"token": "Hello"}})
        yield json.dumps({"success": True, "data": {"done": True}})

    with (
        patch("jobs.worker.stream_chat", side_effect=fake_stream),
        patch("jobs.worker.publish_chunk", new_callable=AsyncMock) as mock_publish,
        patch("jobs.worker.save_response", new_callable=AsyncMock) as mock_save,
    ):
        await _stream_response("req-1", "conv-1", "hello")

    assert mock_publish.call_count == 2

    mock_save.assert_called_once_with("req-1", "Hello")


@pytest.mark.asyncio
async def test_process_skips_stale_request() -> None:
    with (
        patch("jobs.worker.is_processing_request", new_callable=AsyncMock, return_value=False),
        patch("jobs.worker.publish_chunk", new_callable=AsyncMock) as mock_publish,
    ):
        await _process("req-1", "conv-1", "hello")

    mock_publish.assert_called_once()
    chunk = json.loads(mock_publish.call_args[0][2])

    assert chunk["success"] is False


@pytest.mark.asyncio
async def test_process_streams_and_finishes() -> None:
    async def fake_stream(msg: str, conv_id: str) -> AsyncGenerator[str, None]:
        yield json.dumps({"success": True, "data": {"done": True}})

    with (
        patch("jobs.worker.is_processing_request", new_callable=AsyncMock, return_value=True),
        patch("jobs.worker.stream_chat", side_effect=fake_stream),
        patch("jobs.worker.publish_chunk", new_callable=AsyncMock),
        patch("jobs.worker.save_response", new_callable=AsyncMock),
        patch(
            "jobs.worker.wait_for_cancellation", new_callable=AsyncMock, side_effect=_block_forever
        ),
        patch("jobs.worker.finish_processing", new_callable=AsyncMock, return_value=None),
        patch("jobs.worker.renew_processing", new_callable=AsyncMock, return_value=True),
    ):
        await _process("req-1", "conv-1", "hello")


@pytest.mark.asyncio
async def test_process_handles_exception() -> None:
    async def broken_stream(msg: str, conv_id: str) -> AsyncGenerator[str, None]:
        raise RuntimeError("boom")
        yield

    with (
        patch("jobs.worker.is_processing_request", new_callable=AsyncMock, return_value=True),
        patch("jobs.worker.stream_chat", side_effect=broken_stream),
        patch("jobs.worker.publish_chunk", new_callable=AsyncMock) as mock_publish,
        patch("jobs.worker.save_response", new_callable=AsyncMock),
        patch(
            "jobs.worker.wait_for_cancellation", new_callable=AsyncMock, side_effect=_block_forever
        ),
        patch("jobs.worker.finish_processing", new_callable=AsyncMock, return_value=None),
        patch("jobs.worker.renew_processing", new_callable=AsyncMock, return_value=True),
    ):
        await _process("req-1", "conv-1", "hello")

    last_chunk = json.loads(mock_publish.call_args[0][2])

    assert last_chunk["success"] is False


@pytest.mark.asyncio
async def test_process_hands_off_next_request() -> None:
    async def fake_stream(msg: str, conv_id: str) -> AsyncGenerator[str, None]:
        yield json.dumps({"success": True, "data": {"done": True}})

    next_req = {"request_id": "req-2", "conversation_id": "conv-1", "message": "follow up"}

    with (
        patch("jobs.worker.is_processing_request", new_callable=AsyncMock, return_value=True),
        patch("jobs.worker.stream_chat", side_effect=fake_stream),
        patch("jobs.worker.publish_chunk", new_callable=AsyncMock),
        patch("jobs.worker.save_response", new_callable=AsyncMock),
        patch(
            "jobs.worker.wait_for_cancellation", new_callable=AsyncMock, side_effect=_block_forever
        ),
        patch("jobs.worker.finish_processing", new_callable=AsyncMock, return_value=next_req),
        patch("jobs.worker.renew_processing", new_callable=AsyncMock, return_value=True),
        patch("jobs.worker.publish_request", new_callable=AsyncMock) as mock_publish_req,
    ):
        await _process("req-1", "conv-1", "hello")

    mock_publish_req.assert_called_once_with("req-2", "conv-1", "follow up")


@pytest.mark.asyncio
async def test_maintain_processing_lease_stops_when_renew_false() -> None:
    with patch("jobs.worker.renew_processing", new_callable=AsyncMock, return_value=False):
        with patch("asyncio.sleep", new_callable=AsyncMock):
            await _maintain_processing_lease("req-1")


@pytest.mark.asyncio
async def test_stream_response_no_tokens_skips_save() -> None:
    async def fake_stream(msg: str, conv_id: str) -> AsyncGenerator[str, None]:
        yield '{"success": true, "data": {"done": true}}'

    with (
        patch("jobs.worker.stream_chat", side_effect=fake_stream),
        patch("jobs.worker.publish_chunk", new_callable=AsyncMock),
        patch("jobs.worker.save_response", new_callable=AsyncMock) as mock_save,
    ):
        await _stream_response("req-1", "conv-1", "hello")

    mock_save.assert_not_called()


@pytest.mark.asyncio
async def test_process_cancel_path() -> None:
    async def fake_stream(msg: str, conv_id: str) -> AsyncGenerator[str, None]:
        await asyncio.sleep(9999)
        yield ""

    async def instant_cancel(request_id: str) -> None:
        return

    with (
        patch("jobs.worker.is_processing_request", new_callable=AsyncMock, return_value=True),
        patch("jobs.worker.stream_chat", side_effect=fake_stream),
        patch("jobs.worker.publish_chunk", new_callable=AsyncMock) as mock_publish,
        patch("jobs.worker.save_response", new_callable=AsyncMock),
        patch("jobs.worker.wait_for_cancellation", side_effect=instant_cancel),
        patch("jobs.worker.finish_processing", new_callable=AsyncMock, return_value=None),
        patch("jobs.worker.renew_processing", new_callable=AsyncMock, return_value=True),
    ):
        await _process("req-1", "conv-1", "hello")

    last_call = mock_publish.call_args_list[-1]
    chunk = json.loads(last_call[0][2])

    assert chunk["data"]["cancelled"] is True
