import asyncio
import json
from collections.abc import AsyncGenerator
from unittest.mock import AsyncMock, patch

import pytest
from httpx import AsyncClient

from app.routers.chat import _gibberish_reply


@pytest.mark.asyncio
async def test_delete_conversation(client: AsyncClient) -> None:
    with patch("app.services.chat_service.delete_conversation", new_callable=AsyncMock):
        response = await client.delete("/chat/conv-123")

    assert response.status_code == 200
    assert response.json()["data"]["conversation_id"] == "conv-123"


@pytest.mark.asyncio
async def test_chat_direct_sse_streams_tokens(client: AsyncClient) -> None:
    async def fake_stream(msg: str, conv_id: str, user_id: object) -> AsyncGenerator[str, None]:
        yield json.dumps({"success": True, "data": {"thinking": True}})
        yield json.dumps({"success": True, "data": {"token": "Hello"}})
        yield json.dumps({"success": True, "data": {"done": True}})

    with (
        patch("app.services.chat_service.stream_chat", side_effect=fake_stream),
        patch("app.routers.chat.settings") as s,
    ):
        s.assistant_type = "api"
        s.rabbitmq_url = None

        response = await client.post(
            "/chat",
            json={"message": "what laptops do you have", "conversation_id": "conv-1"},
        )

    assert response.status_code == 200
    assert "text/event-stream" in response.headers["content-type"]
    assert b"Hello" in response.content


@pytest.mark.asyncio
async def test_chat_direct_sse_cancelled_error_is_silent(client: AsyncClient) -> None:
    async def cancelled_stream(
        msg: str,
        conv_id: str,
        user_id: object,
    ) -> AsyncGenerator[str, None]:
        raise asyncio.CancelledError()
        yield

    with (
        patch("app.services.chat_service.stream_chat", side_effect=cancelled_stream),
        patch("app.routers.chat.settings") as s,
    ):
        s.assistant_type = "api"
        s.rabbitmq_url = None

        response = await client.post(
            "/chat",
            json={"message": "what laptops do you have", "conversation_id": "conv-1"},
        )

    assert response.status_code == 200
    assert b"went wrong" not in response.content


@pytest.mark.asyncio
async def test_chat_direct_sse_exception_yields_error(client: AsyncClient) -> None:
    async def broken_stream(msg: str, conv_id: str, user_id: object) -> AsyncGenerator[str, None]:
        raise Exception("boom")
        yield

    with (
        patch("app.services.chat_service.stream_chat", side_effect=broken_stream),
        patch("app.routers.chat.settings") as s,
    ):
        s.assistant_type = "api"
        s.rabbitmq_url = None

        response = await client.post(
            "/chat",
            json={"message": "what laptops do you have", "conversation_id": "conv-1"},
        )

    assert response.status_code == 200
    assert b"went wrong" in response.content


@pytest.mark.asyncio
async def test_gibberish_reply_with_user_name() -> None:
    with patch("app.routers.chat.fetch_user_name", return_value=("John", "Doe")):
        reply = _gibberish_reply(user_id=1)

    assert "John" in reply


@pytest.mark.asyncio
async def test_gibberish_reply_without_user() -> None:
    reply = _gibberish_reply(user_id=None)

    assert reply == "How can I help you?"


@pytest.mark.asyncio
async def test_chat_queued_path(client: AsyncClient) -> None:
    async def fake_queued(msg: str, conv_id: str, req_id: str) -> AsyncGenerator[str, None]:
        yield json.dumps({"success": True, "data": {"done": True}})

    with (
        patch("app.routers.chat.queued_sse", side_effect=fake_queued),
        patch("app.routers.chat.settings") as s,
    ):
        s.assistant_type = "ollama"
        s.rabbitmq_url = "amqp://guest:guest@localhost/"

        response = await client.post(
            "/chat",
            json={
                "message": "show me laptops",
                "conversation_id": "conv-1",
                "request_id": "550e8400-e29b-41d4-a716-446655440000",
            },
        )

    assert response.status_code == 200
