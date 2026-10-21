import pytest
from httpx import AsyncClient


@pytest.mark.asyncio
async def test_info_returns_model(client: AsyncClient) -> None:
    response = await client.get("/info")

    assert response.status_code == 200

    data = response.json()["data"]

    assert "model" in data
    assert isinstance(data["model"], str)
    assert len(data["model"]) > 0


@pytest.mark.asyncio
async def test_info_returns_ollama_flag(client: AsyncClient) -> None:
    response = await client.get("/info")
    data = response.json()["data"]

    assert "ollama" in data
    assert isinstance(data["ollama"], bool)
