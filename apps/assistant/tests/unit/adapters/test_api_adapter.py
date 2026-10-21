import json
from collections.abc import AsyncIterator
from unittest.mock import AsyncMock, MagicMock, patch

import pytest

from app.adapters.api_adapter import generate, stream


@pytest.mark.asyncio
async def test_generate_returns_text() -> None:
    mock_response = MagicMock()
    mock_response.text = "Hello!"

    mock_client = AsyncMock()
    mock_client.aio.models.generate_content = AsyncMock(return_value=mock_response)

    with patch("app.adapters.api_adapter.genai.Client", return_value=mock_client):
        result = await generate("test prompt")

    assert result == "Hello!"


@pytest.mark.asyncio
async def test_generate_returns_empty_on_exception() -> None:
    mock_client = AsyncMock()
    mock_client.aio.models.generate_content = AsyncMock(side_effect=Exception("api down"))

    with patch("app.adapters.api_adapter.genai.Client", return_value=mock_client):
        result = await generate("test prompt")

    assert result == ""


@pytest.mark.asyncio
async def test_generate_returns_empty_when_text_none() -> None:
    mock_response = MagicMock()
    mock_response.text = None

    mock_client = AsyncMock()
    mock_client.aio.models.generate_content = AsyncMock(return_value=mock_response)

    with patch("app.adapters.api_adapter.genai.Client", return_value=mock_client):
        result = await generate("test prompt")

    assert result == ""


@pytest.mark.asyncio
async def test_stream_yields_tokens() -> None:
    chunk1 = MagicMock()
    chunk1.text = "Hello"
    chunk2 = MagicMock()
    chunk2.text = " world"

    async def fake_iter() -> AsyncIterator[MagicMock]:
        yield chunk1
        yield chunk2

    mock_client = MagicMock()
    mock_client.aio.models.generate_content_stream = AsyncMock(return_value=fake_iter())

    with patch("app.adapters.api_adapter.genai.Client", return_value=mock_client):
        messages = [{"role": "user", "content": "hi"}]
        chunks = [chunk async for chunk in stream(messages, "conv-1")]

    token_chunks = [json.loads(c) for c in chunks if "token" in c]

    assert any(t["data"]["token"] == "Hello" for t in token_chunks)

    done = json.loads(chunks[-1])

    assert done["data"]["done"] is True


@pytest.mark.asyncio
async def test_stream_yields_error_on_exception() -> None:
    mock_client = MagicMock()
    mock_client.aio.models.generate_content_stream = AsyncMock(side_effect=Exception("fail"))

    with patch("app.adapters.api_adapter.genai.Client", return_value=mock_client):
        messages = [{"role": "user", "content": "hi"}]
        chunks = [chunk async for chunk in stream(messages, "conv-1")]

    assert len(chunks) == 1

    data = json.loads(chunks[0])

    assert data["success"] is False
