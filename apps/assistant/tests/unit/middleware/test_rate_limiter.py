import json
from unittest.mock import MagicMock, patch

from fastapi import Request

from app.middleware.rate_limiter import _get_ttl, rate_limit_handler


def _make_request(ip: str = "1.2.3.4") -> Request:
    request = MagicMock(spec=Request)
    request.client = MagicMock()
    request.client.host = ip

    return request


def test_rate_limit_handler_returns_429() -> None:
    with patch("app.middleware.rate_limiter._get_ttl", return_value=None):
        response = rate_limit_handler(_make_request(), Exception())

    assert response.status_code == 429


def test_rate_limit_handler_includes_retry_after_when_ttl() -> None:
    with patch("app.middleware.rate_limiter._get_ttl", return_value=120):
        response = rate_limit_handler(_make_request(), Exception())

    body = json.loads(response.body)

    assert body["retry_after"] == 120
    assert body["success"] is False


def test_rate_limit_handler_no_retry_after_when_no_ttl() -> None:
    with patch("app.middleware.rate_limiter._get_ttl", return_value=None):
        response = rate_limit_handler(_make_request(), Exception())

    body = json.loads(response.body)

    assert "retry_after" not in body


def test_get_ttl_returns_none_on_exception() -> None:
    with patch("app.middleware.rate_limiter.Redis") as mock_redis:
        mock_redis.from_url.side_effect = Exception("no redis")
        result = _get_ttl("1.2.3.4")

    assert result is None


def test_get_ttl_returns_none_when_no_keys() -> None:
    with patch("app.middleware.rate_limiter.Redis") as mock_redis:
        mock_redis.from_url.return_value.keys.return_value = []
        result = _get_ttl("1.2.3.4")

    assert result is None


def test_get_ttl_returns_value_when_key_exists() -> None:
    with patch("app.middleware.rate_limiter.Redis") as mock_redis:
        mock_redis.from_url.return_value.keys.return_value = ["LIMITS:LIMITER/1.2.3.4"]
        mock_redis.from_url.return_value.ttl.return_value = 300
        result = _get_ttl("1.2.3.4")

    assert result == 300


def test_get_ttl_returns_none_when_ttl_zero() -> None:
    with patch("app.middleware.rate_limiter.Redis") as mock_redis:
        mock_redis.from_url.return_value.keys.return_value = ["LIMITS:LIMITER/1.2.3.4"]
        mock_redis.from_url.return_value.ttl.return_value = 0
        result = _get_ttl("1.2.3.4")

    assert result is None
