import json
from typing import cast

from redis.asyncio import Redis

from app.config import settings

_TTL = 24 * 60 * 60  # 24 hours
_KEY_PREFIX = "conversation:"


def _redis() -> Redis:
    return cast(Redis, Redis.from_url(settings.redis_url, decode_responses=True))


async def load_history(conversation_id: str) -> list[dict[str, str]]:
    async with _redis() as r:
        data = await r.get(f"{_KEY_PREFIX}{conversation_id}")
    return json.loads(data) if data else []


async def save_history(conversation_id: str, history: list[dict[str, str]]) -> None:
    async with _redis() as r:
        await r.setex(f"{_KEY_PREFIX}{conversation_id}", _TTL, json.dumps(history))


async def delete_history(conversation_id: str) -> None:
    async with _redis() as r:
        await r.delete(f"{_KEY_PREFIX}{conversation_id}")
