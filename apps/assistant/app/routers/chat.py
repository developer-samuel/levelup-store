import asyncio
import json
import uuid
from collections.abc import AsyncGenerator
from typing import Annotated

from fastapi import APIRouter, Depends, Request
from fastapi.responses import JSONResponse, StreamingResponse

from app.config import settings
from app.guard import verify_api_key
from app.middleware.rate_limiter import limiter
from app.models import ChatRequest
from app.repositories.user_repository import fetch_user_name
from app.responses import success
from app.routers.chat_queue import queued_sse
from app.services import chat_service

router = APIRouter()

_FAST_REPLY_WORDS = frozenset(
    {
        "hi",
        "hey",
        "hello",
        "hiya",
        "howdy",
        "sup",
        "yo",
        "test",
        "testing",
        "ok",
        "okay",
        "k",
        "lol",
        "lmao",
        "asdf",
        "qwerty",
        "foo",
        "bar",
        "baz",
        "lorem",
        "ipsum",
        "hmm",
        "hm",
        "ugh",
    }
)


def _gibberish_reply(user_id: int | None) -> str:
    if user_id is not None:
        name = fetch_user_name(user_id)
        first = name[0].strip() if name else ""

        if first:
            return f"{first}, how can I help you?"

    return "How can I help you?"


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

    return False


async def _gibberish_sse(user_id: int | None = None) -> AsyncGenerator[str, None]:
    """Immediate response for gibberish input - no LLM call."""

    reply = _gibberish_reply(user_id)
    yield f"data: {json.dumps({'success': True, 'data': {'thinking': True}})}\n\n"

    await asyncio.sleep(0.05)

    yield f"data: {json.dumps({'success': True, 'data': {'token': reply}})}\n\n"
    yield f"data: {json.dumps({'success': True, 'data': {'done': True}})}\n\n"


async def _direct_sse(
    message: str, conversation_id: str, user_id: int | None = None
) -> AsyncGenerator[str, None]:
    """Stream directly from chat_service - used when RabbitMQ queue is disabled."""

    try:
        async for data in chat_service.stream_chat(message, conversation_id, user_id):
            yield f"data: {data}\n\n"
    except asyncio.CancelledError:
        pass
    except Exception as e:
        import logging

        logging.getLogger(__name__).exception("SSE stream error: %s", e)
        msg = "Something went wrong. Please try again."
        
        yield f"data: {json.dumps({'success': False, 'message': msg})}\n\n"


@router.post("/chat")
@limiter.limit("50 per 5 hours")
async def chat(
    request: Request,
    body: ChatRequest,
    _: Annotated[None, Depends(verify_api_key)],
) -> StreamingResponse:
    conversation_id = body.conversation_id or str(uuid.uuid4())

    if _is_gibberish(body.message):
        generator = _gibberish_sse(body.user_id)
    elif settings.assistant_type == "ollama" and settings.rabbitmq_url:
        request_id = str(body.request_id or uuid.uuid4())
        generator = queued_sse(body.message, conversation_id, request_id)
    else:
        generator = _direct_sse(body.message, conversation_id, body.user_id)

    return StreamingResponse(
        generator,
        media_type="text/event-stream",
        headers={"Cache-Control": "no-cache", "X-Accel-Buffering": "no"},
    )


@router.delete("/chat/{conversation_id}")
async def delete_conversation(
    conversation_id: str,
    _: Annotated[None, Depends(verify_api_key)],
) -> JSONResponse:
    await chat_service.delete_conversation(conversation_id)

    return success({"conversation_id": conversation_id})
