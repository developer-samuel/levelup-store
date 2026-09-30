from __future__ import annotations

import asyncio
import json
from collections.abc import AsyncGenerator, Awaitable
from typing import Protocol, cast

import redis.asyncio as aioredis

from app.config import settings

_PROCESSING_TTL = 900   # 15 minutes safety TTL on the processing flag - must be >= _STREAM_TIMEOUT
_QUEUE_TTL = 3_600      # 1 hour TTL on the per-conversation queue list
_STREAM_TIMEOUT = 900   # 15 minutes: max wait for first/next chunk from worker


class _AsyncCloseable(Protocol):
    """Minimal protocol for objects that expose an async ``aclose`` method."""

    async def aclose(self) -> None: ...


_PROCESSING_KEY = "ai:processing"
_QUEUE_KEY = "ai:queue"


def _cancel_key(request_id: str) -> str:
    return f"ai:cancelled:{request_id}"


def _cancel_channel(request_id: str) -> str:
    return f"ai:cancel:{request_id}"


def _stream_channel(conversation_id: str, request_id: str) -> str:
    return f"ai:stream:{conversation_id}:{request_id}"


def _make_client() -> aioredis.Redis:
    return cast(aioredis.Redis, aioredis.Redis.from_url(settings.redis_url, decode_responses=True))


_PUBLISH_QUEUE_POSITIONS = """
local function publish_queue_positions(queue_key)
    local requests = redis.call('LRANGE', queue_key, 0, -1)
    for position, raw in ipairs(requests) do
        local ok, request = pcall(cjson.decode, raw)
        if ok then
            local channel = table.concat({
                'ai:stream:', request['conversation_id'], ':', request['request_id']
            })
            local event = cjson.encode({
                success = true,
                data = {queued = true, queue_position = position}
            })
            redis.call('PUBLISH', channel, event)
        end
    end
end
"""


async def register_request(payload: dict[str, str]) -> int:
    """Claim the global worker slot or enqueue the request atomically.

    Returns 0 when the caller claimed the slot, otherwise the request's
    1-based position behind the currently processing request.
    """
    script = _PUBLISH_QUEUE_POSITIONS + """
    if redis.call('EXISTS', KEYS[3]) == 1 then
        return -1
    end
    if redis.call('SET', KEYS[1], ARGV[1], 'NX', 'EX', ARGV[2]) then
        return 0
    end
    local position = redis.call('RPUSH', KEYS[2], ARGV[3])
    redis.call('EXPIRE', KEYS[2], ARGV[4])
    publish_queue_positions(KEYS[2])
    return position
    """
    async with _make_client() as r:
        position = await cast(
            Awaitable[int],
            r.eval(
                script,
                3,
                _PROCESSING_KEY,
                _QUEUE_KEY,
                _cancel_key(payload["request_id"]),
                payload["request_id"],
                _PROCESSING_TTL,
                json.dumps(payload),
                _QUEUE_TTL,
            ),
        )
    return position


async def is_processing_request(request_id: str) -> bool:
    """Return whether a request currently owns the global worker slot."""

    async with _make_client() as r:
        active_id = await cast(Awaitable[str | None], r.get(_PROCESSING_KEY))

    return active_id == request_id


async def renew_processing(request_id: str) -> bool:
    """Refresh the worker-slot lease without extending another request's lease."""

    script = """
    if redis.call('GET', KEYS[1]) == ARGV[1] then
        return redis.call('EXPIRE', KEYS[1], ARGV[2])
    end
    return 0
    """

    async with _make_client() as r:
        renewed = await cast(
            Awaitable[int],
            r.eval(script, 1, _PROCESSING_KEY, request_id, _PROCESSING_TTL),
        )

    return renewed == 1


async def finish_processing(request_id: str) -> dict[str, str] | None:
    """Hand off or release the worker slot only if this request still owns it."""

    script = _PUBLISH_QUEUE_POSITIONS + """

    if redis.call('GET', KEYS[1]) ~= ARGV[1] then
        return nil
    end
    local raw = redis.call('LPOP', KEYS[2])
    if raw then
        local request = cjson.decode(raw)
        redis.call('SET', KEYS[1], request['request_id'], 'EX', ARGV[2])
        publish_queue_positions(KEYS[2])
        return raw
    end
    redis.call('DEL', KEYS[1])
    return nil
    """

    async with _make_client() as r:
        raw = await cast(
            Awaitable[str | None],
            r.eval(script, 2, _PROCESSING_KEY, _QUEUE_KEY, request_id, _PROCESSING_TTL),
        )

    return json.loads(raw) if raw is not None else None


async def cancel_request(request_id: str) -> bool:
    """Remove a queued request or signal cancellation to the active worker."""

    script = _PUBLISH_QUEUE_POSITIONS + """

    local items = redis.call('LRANGE', KEYS[2], 0, -1)
    for _, raw in ipairs(items) do
        local ok, request = pcall(cjson.decode, raw)
        if ok and request['request_id'] == ARGV[1] then
            redis.call('LREM', KEYS[2], 1, raw)
            publish_queue_positions(KEYS[2])
            return 1
        end
    end
    if redis.call('GET', KEYS[1]) == ARGV[1] then
        redis.call('SET', KEYS[3], '1', 'EX', ARGV[2])
        redis.call('PUBLISH', ARGV[3], '1')
        return 2
    end
    redis.call('SET', KEYS[3], '1', 'EX', ARGV[2])
    return 3
    """

    async with _make_client() as r:
        result = await cast(
            Awaitable[int],
            r.eval(
                script,
                3,
                _PROCESSING_KEY,
                _QUEUE_KEY,
                _cancel_key(request_id),
                request_id,
                _PROCESSING_TTL,
                _cancel_channel(request_id),
            ),
        )

    return result != 0


async def wait_for_cancellation(request_id: str) -> None:
    """Wait until a cancellation is recorded for a request."""

    r = _make_client()
    pubsub = r.pubsub()
    channel = _cancel_channel(request_id)

    try:
        await pubsub.subscribe(channel)

        if await r.exists(_cancel_key(request_id)):
            return

        async for message in pubsub.listen():
            if message["type"] == "message":
                return
    finally:
        await pubsub.unsubscribe(channel)
        await pubsub.aclose()
        await r.aclose()


async def publish_chunk(conversation_id: str, request_id: str, data: str) -> None:
    """Publish a streaming chunk to the per-request pub/sub channel."""

    async with _make_client() as r:
        await r.publish(_stream_channel(conversation_id, request_id), data)


async def open_subscription(conversation_id: str, request_id: str) -> tuple[aioredis.Redis, object]:
    """Subscribe to the per-request pub/sub channel BEFORE triggering work.

    Returns (redis_client, pubsub) so the caller can publish to RabbitMQ after
    subscribing, eliminating the race condition where the worker publishes a
    chunk before the subscriber is ready.

    The caller must pass the returned objects to ``consume_subscription``.
    """

    channel = _stream_channel(conversation_id, request_id)
    r = _make_client()
    pubsub = r.pubsub()

    await pubsub.subscribe(channel)

    return r, pubsub


async def consume_subscription(
    r: aioredis.Redis,
    pubsub: object,
    conversation_id: str,
    request_id: str,
) -> AsyncGenerator[str, None]:
    """Iterate an already-subscribed pubsub and yield raw JSON strings.

    Terminates automatically when a ``done`` or error chunk is received,
    or after ``_STREAM_TIMEOUT`` seconds with no activity (worker down / crash).
    """

    channel = _stream_channel(conversation_id, request_id)

    try:
        async with asyncio.timeout(_STREAM_TIMEOUT):
            async for message in cast(aioredis.client.PubSub, pubsub).listen():
                if message["type"] != "message":
                    continue

                data: str = message["data"]
                yield data

                try:
                    parsed: dict[str, object] = json.loads(data)
                    inner = parsed.get("data") or {}

                    if not isinstance(inner, dict):
                        inner = {}
                    if inner.get("done") or not parsed.get("success"):
                        break
                except (json.JSONDecodeError, AttributeError):
                    break
    except TimeoutError:
        yield json.dumps(
            {"success": False, "message": "The time limit has expired. Please try again."}
        )
    finally:
        await cast(aioredis.client.PubSub, pubsub).unsubscribe(channel)
        await cast(_AsyncCloseable, pubsub).aclose()
        await r.aclose()


async def subscribe_stream(
    conversation_id: str,
    request_id: str,
) -> AsyncGenerator[str, None]:
    """Subscribe and consume in one call - use only when publish already happened."""
    
    r, pubsub = await open_subscription(conversation_id, request_id)

    async for chunk in consume_subscription(r, pubsub, conversation_id, request_id):
        yield chunk
