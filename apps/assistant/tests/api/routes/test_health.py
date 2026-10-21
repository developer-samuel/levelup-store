from unittest.mock import MagicMock, patch

import pytest
from httpx import AsyncClient

from app.routers.health import _check_chromadb, _check_ollama, _check_postgres, _check_redis


@pytest.mark.asyncio
async def test_health_returns_ok(client: AsyncClient) -> None:
    response = await client.get("/health")

    assert response.status_code == 200
    assert response.json()["data"]["status"] == "ok"


@pytest.mark.asyncio
async def test_health_ready_all_healthy(client: AsyncClient) -> None:
    with (
        patch("app.routers.health._check_ollama", return_value=True),
        patch("app.routers.health._check_redis", return_value=True),
        patch("app.routers.health._check_chromadb", return_value=True),
        patch("app.routers.health._check_postgres", return_value=True),
    ):
        response = await client.get("/health/ready")

    assert response.status_code == 200

    data = response.json()["data"]

    assert data["status"] == "ok"
    assert all(data["services"].values())


@pytest.mark.asyncio
async def test_health_ready_degraded_when_service_down(client: AsyncClient) -> None:
    with (
        patch("app.routers.health._check_ollama", return_value=False),
        patch("app.routers.health._check_redis", return_value=True),
        patch("app.routers.health._check_chromadb", return_value=True),
        patch("app.routers.health._check_postgres", return_value=True),
    ):
        response = await client.get("/health/ready")

    assert response.status_code == 503

    data = response.json()["data"]

    assert data["status"] == "degraded"
    assert data["services"]["ollama"] is False


def test_check_ollama_success() -> None:
    mock_response = MagicMock()
    mock_response.is_success = True

    with patch("app.routers.health.httpx.Client") as mock_client_cls:
        mock_client_cls.return_value.__enter__.return_value.get.return_value = mock_response
        result = _check_ollama()

    assert result is True


def test_check_ollama_failure_on_exception() -> None:
    with patch("app.routers.health.httpx.Client") as mock_client_cls:
        mock_client_cls.return_value.__enter__.return_value.get.side_effect = Exception("timeout")
        result = _check_ollama()

    assert result is False


def test_check_chromadb_success() -> None:
    mock_response = MagicMock()
    mock_response.is_success = True

    with patch("app.routers.health.httpx.Client") as mock_client_cls:
        mock_client_cls.return_value.__enter__.return_value.get.return_value = mock_response
        result = _check_chromadb()

    assert result is True


def test_check_chromadb_failure_on_exception() -> None:
    with patch("app.routers.health.httpx.Client") as mock_client_cls:
        mock_client_cls.return_value.__enter__.return_value.get.side_effect = Exception("down")
        result = _check_chromadb()

    assert result is False


def test_check_redis_success() -> None:
    with patch("app.routers.health.Redis") as mock_redis_cls:
        mock_redis_cls.from_url.return_value.ping.return_value = True
        result = _check_redis()

    assert result is True


def test_check_redis_failure_on_exception() -> None:
    with patch("app.routers.health.Redis") as mock_redis_cls:
        mock_redis_cls.from_url.return_value.ping.side_effect = Exception("no redis")
        result = _check_redis()

    assert result is False


def test_check_postgres_success() -> None:
    mock_conn = MagicMock()

    with patch("app.routers.health.psycopg2.connect", return_value=mock_conn):
        result = _check_postgres()

    assert result is True

    mock_conn.close.assert_called_once()


def test_check_postgres_failure_on_exception() -> None:
    with patch("app.routers.health.psycopg2.connect", side_effect=Exception("no db")):
        result = _check_postgres()

    assert result is False
