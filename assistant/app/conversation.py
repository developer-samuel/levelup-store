import json
from typing import cast

from redis.asyncio import Redis

from app.config import settings

_TTL = 24 * 60 * 60  # 24 hours
_KEY_PREFIX = "conversation:"
_MAX_HISTORY = 30  # max messages per conversation

_redis: Redis = cast(Redis, Redis.from_url(settings.redis_url, decode_responses=True))


async def load_history(conversation_id: str) -> list[dict[str, str]]:
    data = await _redis.get(f"{_KEY_PREFIX}{conversation_id}")
    return json.loads(data) if data else []


async def save_history(conversation_id: str, history: list[dict[str, str]]) -> None:
    await _redis.setex(f"{_KEY_PREFIX}{conversation_id}", _TTL, json.dumps(history[-_MAX_HISTORY:]))


async def delete_history(conversation_id: str) -> None:
    await _redis.delete(f"{_KEY_PREFIX}{conversation_id}")
