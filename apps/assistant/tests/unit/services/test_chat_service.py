import json
from collections.abc import AsyncGenerator
from unittest.mock import AsyncMock, MagicMock, patch

import pytest

from app.services.chat_service import close, delete_conversation, stream_chat


def _make_redis() -> MagicMock:
    r = AsyncMock()
    r.get = AsyncMock(return_value=None)
    r.setex = AsyncMock()
    r.delete = AsyncMock()
    r.aclose = AsyncMock()

    return r


@pytest.mark.asyncio
async def test_stream_chat_attack_blocked() -> None:
    mock_redis = _make_redis()

    with (
        patch("app.services.chat_service._redis", mock_redis),
        patch("app.services.chat_service.is_attack", new_callable=AsyncMock, return_value=True),
        patch("app.services.chat_service.settings") as s,
    ):
        s.support_email = "support@example.com"
        s.conversation_ttl_guest = 3600
        s.conversation_ttl_user = 86400

        chunks = [c async for c in stream_chat("drop table", "conv-1")]

    assert any("thinking" in c for c in chunks)
    assert any("sorry" in c.lower() or "unable" in c.lower() for c in chunks)
    assert any(json.loads(c).get("data", {}).get("done") for c in chunks)


@pytest.mark.asyncio
async def test_stream_chat_yields_llm_tokens() -> None:
    mock_redis = _make_redis()

    async def fake_llm(messages: list[object], conversation_id: str) -> AsyncGenerator[str, None]:
        yield json.dumps({"success": True, "data": {"token": "Hello"}})
        yield json.dumps({"success": True, "data": {"done": True}})

    with (
        patch("app.services.chat_service._redis", mock_redis),
        patch("app.services.chat_service.is_attack", new_callable=AsyncMock, return_value=False),
        patch("app.services.chat_service.query", return_value="rag context"),
        patch("app.services.chat_service.query_documents", return_value="docs context"),
        patch(
            "app.services.chat_service.fetch_catalog_summary",
            return_value={"brands": [], "categories": [], "types": [], "subtypes": []},
        ),
        patch("app.services.chat_service.fetch_top_reviewed_products", return_value=[]),
        patch("app.services.chat_service.fetch_top_ordered_products", return_value=[]),
        patch("app.services.chat_service._llm_stream", side_effect=fake_llm),
        patch("app.services.chat_service.asyncio.to_thread", side_effect=lambda fn, *a: fn(*a)),
    ):
        chunks = [c async for c in stream_chat("hello", "conv-1")]

    token_chunks = [json.loads(c) for c in chunks if "token" in c]

    assert any(t["data"]["token"] == "Hello" for t in token_chunks)


@pytest.mark.asyncio
async def test_stream_chat_saves_history() -> None:
    mock_redis = _make_redis()

    async def fake_llm(messages: list[object], conversation_id: str) -> AsyncGenerator[str, None]:
        yield json.dumps({"success": True, "data": {"token": "Hi!"}})
        yield json.dumps({"success": True, "data": {"done": True}})

    with (
        patch("app.services.chat_service._redis", mock_redis),
        patch("app.services.chat_service.is_attack", new_callable=AsyncMock, return_value=False),
        patch("app.services.chat_service.query", return_value=""),
        patch("app.services.chat_service.query_documents", return_value=""),
        patch(
            "app.services.chat_service.fetch_catalog_summary",
            return_value={"brands": [], "categories": [], "types": [], "subtypes": []},
        ),
        patch("app.services.chat_service.fetch_top_reviewed_products", return_value=[]),
        patch("app.services.chat_service.fetch_top_ordered_products", return_value=[]),
        patch("app.services.chat_service._llm_stream", side_effect=fake_llm),
        patch("app.services.chat_service.asyncio.to_thread", side_effect=lambda fn, *a: fn(*a)),
    ):
        _ = [c async for c in stream_chat("hi", "conv-1")]

    mock_redis.setex.assert_called_once()
    saved_data = json.loads(mock_redis.setex.call_args[0][2])

    assert any(m["role"] == "user" and m["content"] == "hi" for m in saved_data)
    assert any(m["role"] == "assistant" for m in saved_data)


@pytest.mark.asyncio
async def test_close_calls_redis_aclose() -> None:
    mock_redis = _make_redis()

    with patch("app.services.chat_service._redis", mock_redis):
        await close()

    mock_redis.aclose.assert_called_once()


@pytest.mark.asyncio
async def test_delete_conversation_calls_redis_delete() -> None:
    mock_redis = _make_redis()

    with patch("app.services.chat_service._redis", mock_redis):
        await delete_conversation("conv-99")

    mock_redis.delete.assert_called_once_with("conversation:conv-99")


@pytest.mark.asyncio
async def test_stream_chat_with_user_id_fetches_user_data() -> None:
    mock_redis = _make_redis()

    async def fake_llm(messages: list[object], conversation_id: str) -> AsyncGenerator[str, None]:
        yield json.dumps({"success": True, "data": {"done": True}})

    with (
        patch("app.services.chat_service._redis", mock_redis),
        patch("app.services.chat_service.is_attack", new_callable=AsyncMock, return_value=False),
        patch("app.services.chat_service.query", return_value=""),
        patch("app.services.chat_service.query_documents", return_value=""),
        patch(
            "app.services.chat_service.fetch_catalog_summary",
            return_value={"brands": [], "categories": [], "types": [], "subtypes": []},
        ),
        patch("app.services.chat_service.fetch_top_reviewed_products", return_value=[]),
        patch("app.services.chat_service.fetch_top_ordered_products", return_value=[]),
        patch("app.services.chat_service.fetch_user_orders", return_value=[]),
        patch("app.services.chat_service.fetch_user_cart", return_value=[]),
        patch("app.services.chat_service.fetch_user_reviews", return_value=[]),
        patch("app.services.chat_service.fetch_user_wishlist", return_value=[]),
        patch("app.services.chat_service.fetch_user_name", return_value=("John", "Doe")),
        patch("app.services.chat_service.fetch_user_order_profile", return_value=[]),
        patch("app.services.chat_service._llm_stream", side_effect=fake_llm),
        patch("app.services.chat_service.asyncio.to_thread", side_effect=lambda fn, *a: fn(*a)),
    ):
        chunks = [c async for c in stream_chat("hi", "conv-1", user_id=42)]

    assert len(chunks) > 0
