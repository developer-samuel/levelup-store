import json
from unittest.mock import AsyncMock, patch

import pytest

from app.messaging.rabbitmq import publish_request


@pytest.mark.asyncio
async def test_publish_request_sends_message() -> None:
    mock_channel = AsyncMock()
    mock_connection = AsyncMock()
    mock_connection.__aenter__ = AsyncMock(return_value=mock_connection)
    mock_connection.__aexit__ = AsyncMock(return_value=False)
    mock_connection.channel = AsyncMock(return_value=mock_channel)

    with patch(
        "app.messaging.rabbitmq.aio_pika.connect_robust",
        new_callable=AsyncMock,
        return_value=mock_connection,
    ):
        await publish_request("req-1", "conv-1", "hello")

    mock_channel.declare_queue.assert_called_once()
    mock_channel.default_exchange.publish.assert_called_once()


@pytest.mark.asyncio
async def test_publish_request_message_contains_data() -> None:
    mock_channel = AsyncMock()
    mock_connection = AsyncMock()
    mock_connection.__aenter__ = AsyncMock(return_value=mock_connection)
    mock_connection.__aexit__ = AsyncMock(return_value=False)
    mock_connection.channel = AsyncMock(return_value=mock_channel)

    with patch(
        "app.messaging.rabbitmq.aio_pika.connect_robust",
        new_callable=AsyncMock,
        return_value=mock_connection,
    ):
        await publish_request("req-42", "conv-99", "test message")

    call_args = mock_channel.default_exchange.publish.call_args
    message = call_args[0][0]
    body = json.loads(message.body.decode())

    assert body["request_id"] == "req-42"
    assert body["conversation_id"] == "conv-99"
    assert body["message"] == "test message"
