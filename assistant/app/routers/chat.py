import asyncio
import uuid
from collections.abc import AsyncGenerator
from typing import Annotated

from fastapi import APIRouter, Depends, Header, HTTPException, Request
from fastapi.responses import JSONResponse, StreamingResponse

from app.config import settings
from app.models import ChatRequest
from app.rate_limiter import limiter
from app.responses import success
from app.services import chat_service

router = APIRouter()


def _verify_api_key(x_api_key: Annotated[str, Header()] = "") -> None:
    if settings.api_key and x_api_key != settings.api_key:
        raise HTTPException(status_code=401, detail="Invalid API key")


async def _sse_stream(message: str, conversation_id: str) -> AsyncGenerator[str, None]:
    try:
        async for data in chat_service.stream_chat(message, conversation_id):
            yield f"data: {data}\n\n"
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

    return StreamingResponse(
        _sse_stream(body.message, conversation_id),
        media_type="text/event-stream",
        headers={"Cache-Control": "no-cache", "X-Accel-Buffering": "no"},
    )


@router.delete("/chat/{conversation_id}")
async def delete_conversation(
    conversation_id: str,
    _: Annotated[None, Depends(_verify_api_key)],
) -> JSONResponse:
    await chat_service.delete_conversation(conversation_id)

    return success({"conversation_id": conversation_id})
