from typing import cast

from redis.asyncio import Redis

from app.config import settings

_KEY_PRIMARY = "model_usage:primary"
_KEY_FALLBACK = "model_usage:fallback"
_KEY_OLLAMA = "model_usage:ollama"

_redis: Redis = cast(Redis, Redis.from_url(settings.redis_url, decode_responses=True))


async def _increment(key: str, limit: int) -> bool:
    count = await _redis.incr(key)

    if count == 1:
        await _redis.expire(key, settings.assistant_limit_window)

    return bool(count <= limit)


async def check_and_increment_ollama() -> bool:
    return await _increment(_KEY_OLLAMA, settings.assistant_ollama_limit)


async def check_and_increment_primary() -> bool:
    return await _increment(_KEY_PRIMARY, settings.assistant_primary_limit)


async def check_and_increment_fallback() -> bool:
    return await _increment(_KEY_FALLBACK, settings.assistant_fallback_limit)


async def is_primary_exhausted() -> bool:
    count = await _redis.get(_KEY_PRIMARY)

    return int(count or 0) >= settings.assistant_primary_limit


async def is_fallback_exhausted() -> bool:
    count = await _redis.get(_KEY_FALLBACK)
    
    return int(count or 0) >= settings.assistant_fallback_limit
