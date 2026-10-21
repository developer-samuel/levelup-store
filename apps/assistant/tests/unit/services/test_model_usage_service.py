from unittest.mock import AsyncMock, patch

import pytest

from app.config import settings
from app.services.model_usage_service import (
    check_and_increment_fallback,
    check_and_increment_ollama,
    check_and_increment_primary,
    is_fallback_exhausted,
    is_primary_exhausted,
)


@pytest.fixture(autouse=True)
def reset_redis_mock() -> None:
    pass


async def _make_redis(get_val: str | None = None, incr_val: int = 1) -> AsyncMock:
    mock = AsyncMock()
    mock.incr.return_value = incr_val
    mock.get.return_value = get_val
    mock.expire.return_value = True
    return mock


@pytest.mark.asyncio
async def test_check_and_increment_primary_under_limit() -> None:
    redis = await _make_redis(incr_val=1)

    with patch("app.services.model_usage_service._redis", redis):
        result = await check_and_increment_primary()

    assert result is True
    redis.expire.assert_called_once()


@pytest.mark.asyncio
async def test_check_and_increment_primary_over_limit() -> None:
    redis = await _make_redis(incr_val=999)

    with patch("app.services.model_usage_service._redis", redis):
        result = await check_and_increment_primary()

    assert result is False


@pytest.mark.asyncio
async def test_expire_set_only_on_first_increment() -> None:
    redis = await _make_redis(incr_val=2)

    with patch("app.services.model_usage_service._redis", redis):
        await check_and_increment_primary()

    redis.expire.assert_not_called()


@pytest.mark.asyncio
async def test_is_primary_exhausted_true() -> None:
    redis = await _make_redis(get_val=str(settings.assistant_primary_limit))

    with patch("app.services.model_usage_service._redis", redis):
        result = await is_primary_exhausted()

    assert result is True


@pytest.mark.asyncio
async def test_is_primary_exhausted_false() -> None:
    redis = await _make_redis(get_val="1")

    with patch("app.services.model_usage_service._redis", redis):
        result = await is_primary_exhausted()

    assert result is False


@pytest.mark.asyncio
async def test_is_primary_exhausted_none_key() -> None:
    redis = await _make_redis(get_val=None)

    with patch("app.services.model_usage_service._redis", redis):
        result = await is_primary_exhausted()

    assert result is False


@pytest.mark.asyncio
async def test_check_and_increment_fallback_under_limit() -> None:
    redis = await _make_redis(incr_val=1)

    with patch("app.services.model_usage_service._redis", redis):
        result = await check_and_increment_fallback()

    assert result is True


@pytest.mark.asyncio
async def test_check_and_increment_ollama_under_limit() -> None:
    redis = await _make_redis(incr_val=1)

    with patch("app.services.model_usage_service._redis", redis):
        result = await check_and_increment_ollama()

    assert result is True


@pytest.mark.asyncio
async def test_is_fallback_exhausted_true() -> None:
    redis = await _make_redis(get_val=str(settings.assistant_fallback_limit))

    with patch("app.services.model_usage_service._redis", redis):
        result = await is_fallback_exhausted()

    assert result is True


@pytest.mark.asyncio
async def test_is_fallback_exhausted_false() -> None:
    redis = await _make_redis(get_val="1")

    with patch("app.services.model_usage_service._redis", redis):
        result = await is_fallback_exhausted()

    assert result is False
