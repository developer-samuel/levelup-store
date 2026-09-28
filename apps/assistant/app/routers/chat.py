import asyncio
import json
import uuid
from collections.abc import AsyncGenerator
from typing import Annotated

from fastapi import APIRouter, Depends, Header, HTTPException, Request
from fastapi.responses import JSONResponse, StreamingResponse

from app.config import settings
from app.models import ChatRequest
from app.queue import (
    cancel_request,
    consume_subscription,
    is_processing_request,
    open_subscription,
    publish_chunk,
    register_request,
    subscribe_stream,
)
from app.rabbitmq import publish_request
from app.rate_limiter import limiter
from app.responses import success
from app.services import chat_service

router = APIRouter()

_ASCII_VOWELS = frozenset("aeiouAEIOU")

_FAST_REPLY_WORDS = frozenset({
    "hi", "hey", "hello", "hiya", "howdy", "sup", "yo",
    "test", "testing", "ok", "okay", "k", "lol", "lmao",
    "asdf", "qwerty", "foo", "bar", "baz", "lorem", "ipsum",
    "hmm", "hm", "ugh",
})

_GIBBERISH_REPLY = "Hello! How can I help you?"

def _is_gibberish(message: str) -> bool:
    """Return True for structurally obvious gibberish that should bypass the LLM.

    Only applied to pure-ASCII messages - any non-ASCII character (accented
    letters, Cyrillic, Arabic, CJK, …) means it is a real language and the
    LLM should handle it.
    """

    text = message.strip()

    if not text:
        return True
    # Non-ASCII → real language, never intercept
    if not text.isascii():
        return False
    # Known greetings and meaningless words
    if text.lower() in _FAST_REPLY_WORDS:
        return True
    # Single character or symbol
    if len(text) == 1:
        return True
    # All same character repeated (e.g. "zzz", "...", "!!!!")
    if len(set(text.replace(" ", ""))) == 1:
        return True
    # Short (≤ 10 chars), no spaces, no ASCII vowels → keyboard mashing
    if len(text) <= 10 and " " not in text and not any(c in _ASCII_VOWELS for c in text):
        return True
    return False


async def _gibberish_sse() -> AsyncGenerator[str, None]:
    """Immediate response for gibberish input - no LLM call."""

    yield f"data: {json.dumps({'success': True, 'data': {'thinking': True}})}\n\n"
    await asyncio.sleep(0.05)
    yield f"data: {json.dumps({'success': True, 'data': {'token': _GIBBERISH_REPLY}})}\n\n"
    yield f"data: {json.dumps({'success': True, 'data': {'done': True}})}\n\n"


def _verify_api_key(x_api_key: Annotated[str, Header()] = "") -> None:
    if settings.api_key and x_api_key != settings.api_key:
        raise HTTPException(status_code=401, detail="Invalid API key")


async def _direct_sse(message: str, conversation_id: str) -> AsyncGenerator[str, None]:
    """Stream directly from chat_service - used when RabbitMQ queue is disabled."""

    try:
        async for data in chat_service.stream_chat(message, conversation_id):
            yield f"data: {data}\n\n"
    except asyncio.CancelledError:
        pass


async def _queued_sse(
    message: str,
    conversation_id: str,
    request_id: str,
) -> AsyncGenerator[str, None]:
    """Queue-aware SSE: publishes to RabbitMQ and streams via Redis pub/sub.

    If another request for the same conversation is already being processed,
    the new request is queued and a ``queued`` event is sent to the client
    first so the UI can show the queue status.
    """

    try:
        r, pubsub = await open_subscription(conversation_id, request_id)

        position = await register_request(
            {
                "request_id": request_id,
                "conversation_id": conversation_id,
                "message": message,
            }
        )

        if position < 0:
            await publish_chunk(
                conversation_id,
                request_id,
                json.dumps({"success": True, "data": {"done": True, "cancelled": True}}),
            )
        elif position == 0:
            thinking_event = json.dumps({"success": True, "data": {"thinking": True}})
            yield f"data: {thinking_event}\n\n"
            await publish_request(request_id, conversation_id, message)
        else:
            queued_event = json.dumps(
                {"success": True, "data": {"queued": True, "queue_position": position}}
            )
            yield f"data: {queued_event}\n\n"

        async for chunk in consume_subscription(r, pubsub, conversation_id, request_id):
            yield f"data: {chunk}\n\n"

    except asyncio.CancelledError:
        pass


@router.post("/chat")
@limiter.limit("100 per 5 hours")
async def chat(
    request: Request,
    body: ChatRequest,
    _: Annotated[None, Depends(_verify_api_key)],
) -> StreamingResponse:
    conversation_id = body.conversation_id or str(uuid.uuid4())

    if _is_gibberish(body.message):
        generator = _gibberish_sse()
    elif settings.rabbitmq_url:
        request_id = str(body.request_id or uuid.uuid4())
        generator = _queued_sse(body.message, conversation_id, request_id)
    else:
        generator = _direct_sse(body.message, conversation_id)

    return StreamingResponse(
        generator,
        media_type="text/event-stream",
        headers={"Cache-Control": "no-cache", "X-Accel-Buffering": "no"},
    )


@router.get("/chat/reattach/{conversation_id}/{request_id}")
async def reattach_chat(
    conversation_id: str,
    request_id: str,
    _: Annotated[None, Depends(_verify_api_key)],
) -> StreamingResponse:
    """Re-subscribe to an in-progress stream after a page reload.

    Does NOT create a new queue entry - only attaches to the existing Redis
    pub/sub channel.  Returns a ``done`` event immediately when queue mode is
    disabled or the worker has already finished.
    """

    async def _done_sse() -> AsyncGenerator[str, None]:
        yield f"data: {json.dumps({'success': True, 'data': {'done': True}})}\n\n"

    sse_headers = {"Cache-Control": "no-cache", "X-Accel-Buffering": "no"}

    if not settings.rabbitmq_url:
        return StreamingResponse(_done_sse(), media_type="text/event-stream", headers=sse_headers)

    if not await is_processing_request(request_id):
        return StreamingResponse(_done_sse(), media_type="text/event-stream", headers=sse_headers)

    async def _reattach_sse() -> AsyncGenerator[str, None]:
        try:
            async for chunk in subscribe_stream(conversation_id, request_id):
                yield f"data: {chunk}\n\n"
        except asyncio.CancelledError:
            pass

    return StreamingResponse(_reattach_sse(), media_type="text/event-stream", headers=sse_headers)


@router.post("/chat/requests/{request_id}/cancel")
async def cancel_chat_request(
    request_id: str,
    _: Annotated[None, Depends(_verify_api_key)],
) -> JSONResponse:
    if not settings.rabbitmq_url:
        return success({"cancelled": False})

    cancelled = await cancel_request(request_id)

    return success({"cancelled": cancelled})


@router.delete("/chat/{conversation_id}")
async def delete_conversation(
    conversation_id: str,
    _: Annotated[None, Depends(_verify_api_key)],
) -> JSONResponse:
    await chat_service.delete_conversation(conversation_id)

    return success({"conversation_id": conversation_id})
