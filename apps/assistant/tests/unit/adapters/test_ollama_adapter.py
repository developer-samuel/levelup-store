import json
from collections.abc import AsyncGenerator
from unittest.mock import AsyncMock, MagicMock, patch

import pytest
from httpx import ConnectError

from app.adapters.ollama_adapter import stream


def _make_chunk(token: str) -> dict[str, object]:
    return {"message": {"content": token}}


async def _async_gen(*chunks: dict[str, object]) -> AsyncGenerator[dict[str, object], None]:
    for chunk in chunks:
        yield chunk


@pytest.mark.asyncio
async def test_stream_yields_tokens() -> None:
    chunk1 = _make_chunk("Hello")
    chunk2 = _make_chunk(" world")

    async def fake_chat(**kwargs: object) -> AsyncGenerator[dict[str, object], None]:
        return _async_gen(chunk1, chunk2)

    mock_client = MagicMock()
    mock_client.chat = fake_chat

    with patch("app.adapters.ollama_adapter._ollama.AsyncClient", return_value=mock_client):
        chunks = [chunk async for chunk in stream([{"role": "user", "content": "hi"}], "conv-1")]

    assert any("Hello" in c for c in chunks)
    assert any("world" in c for c in chunks)

    done_chunk = json.loads(chunks[-1])

    assert done_chunk["data"]["done"] is True


@pytest.mark.asyncio
async def test_stream_yields_error_on_connect_error() -> None:
    mock_client = MagicMock()
    mock_client.chat = AsyncMock(side_effect=ConnectError("no host", request=MagicMock()))

    with patch("app.adapters.ollama_adapter._ollama.AsyncClient", return_value=mock_client):
        chunks = [chunk async for chunk in stream([{"role": "user", "content": "hi"}], "conv-1")]

    assert len(chunks) == 1

    data = json.loads(chunks[0])

    assert data["success"] is False


@pytest.mark.asyncio
async def test_stream_skips_empty_tokens() -> None:
    chunk_empty = _make_chunk("")
    chunk_real = _make_chunk("hi")

    async def fake_chat(**kwargs: object) -> AsyncGenerator[dict[str, object], None]:
        return _async_gen(chunk_empty, chunk_real)

    mock_client = MagicMock()
    mock_client.chat = fake_chat

    with patch("app.adapters.ollama_adapter._ollama.AsyncClient", return_value=mock_client):
        chunks = [chunk async for chunk in stream([{"role": "user", "content": "hi"}], "conv-1")]

    token_chunks = [c for c in chunks if "token" in c]

    assert len(token_chunks) == 1
