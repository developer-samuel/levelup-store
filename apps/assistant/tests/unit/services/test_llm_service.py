import json
from collections.abc import AsyncGenerator
from unittest.mock import AsyncMock, patch

import pytest

from app.services.llm_service import stream


async def _chunks(*items: str) -> AsyncGenerator[str, None]:
    for item in items:
        yield item


@pytest.mark.asyncio
async def test_stream_ollama_allowed() -> None:
    with (
        patch("app.services.llm_service.settings") as s,
        patch(
            "app.services.llm_service.check_and_increment_ollama",
            new_callable=AsyncMock,
            return_value=True,
        ),
        patch(
            "app.services.llm_service.ollama_adapter.stream", return_value=_chunks('{"data":"ok"}')
        ),
    ):
        s.assistant_type = "ollama"
        s.assistant_ollama_model = "mistral"
        chunks = [c async for c in stream([], "conv-1")]

    model_chunk = json.loads(chunks[0])

    assert model_chunk["data"]["fallback"] is False


@pytest.mark.asyncio
async def test_stream_ollama_limit_reached() -> None:
    with (
        patch("app.services.llm_service.settings") as s,
        patch(
            "app.services.llm_service.check_and_increment_ollama",
            new_callable=AsyncMock,
            return_value=False,
        ),
    ):
        s.assistant_type = "ollama"
        chunks = [c async for c in stream([], "conv-1")]

    data = json.loads(chunks[0])

    assert data["success"] is False


@pytest.mark.asyncio
async def test_stream_api_primary() -> None:
    with (
        patch("app.services.llm_service.settings") as s,
        patch(
            "app.services.llm_service.is_primary_exhausted",
            new_callable=AsyncMock,
            return_value=False,
        ),
        patch(
            "app.services.llm_service.check_and_increment_primary",
            new_callable=AsyncMock,
            return_value=True,
        ),
        patch("app.services.llm_service.api_adapter.stream", return_value=_chunks('{"data":"ok"}')),
    ):
        s.assistant_type = "api"
        s.assistant_model = "gemini"
        chunks = [c async for c in stream([], "conv-1")]

    model_chunk = json.loads(chunks[0])

    assert model_chunk["data"]["fallback"] is False


@pytest.mark.asyncio
async def test_stream_api_fallback() -> None:
    with (
        patch("app.services.llm_service.settings") as s,
        patch(
            "app.services.llm_service.is_primary_exhausted",
            new_callable=AsyncMock,
            return_value=True,
        ),
        patch(
            "app.services.llm_service.check_and_increment_fallback",
            new_callable=AsyncMock,
            return_value=True,
        ),
        patch("app.services.llm_service.api_adapter.stream", return_value=_chunks('{"data":"ok"}')),
    ):
        s.assistant_type = "api"
        s.assistant_fallback_model = "gemini-fallback"
        chunks = [c async for c in stream([], "conv-1")]

    model_chunk = json.loads(chunks[0])

    assert model_chunk["data"]["fallback"] is True


@pytest.mark.asyncio
async def test_stream_api_fallback_limit_reached() -> None:
    with (
        patch("app.services.llm_service.settings") as s,
        patch(
            "app.services.llm_service.is_primary_exhausted",
            new_callable=AsyncMock,
            return_value=True,
        ),
        patch(
            "app.services.llm_service.check_and_increment_fallback",
            new_callable=AsyncMock,
            return_value=False,
        ),
    ):
        s.assistant_type = "api"
        chunks = [c async for c in stream([], "conv-1")]

    data = json.loads(chunks[0])

    assert data["success"] is False
