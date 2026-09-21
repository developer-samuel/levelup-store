import uuid
from collections.abc import AsyncGenerator

from fastapi import APIRouter, Request
from fastapi.responses import JSONResponse, StreamingResponse

from app.conversation import delete_history
from app.models import ChatRequest
from app.rate_limiter import limiter
from app.responses import success
from app.services import chat_service

router = APIRouter()


async def _sse_stream(message: str, conversation_id: str) -> AsyncGenerator[str, None]:
    async for data in chat_service.stream_chat(message, conversation_id):
        yield f"data: {data}\n\n"


@router.post("/chat")
@limiter.limit("3 per 5 hours")
async def chat(request: Request, body: ChatRequest) -> StreamingResponse:
    conversation_id = body.conversation_id or str(uuid.uuid4())
    return StreamingResponse(
        _sse_stream(body.message, conversation_id),
        media_type="text/event-stream",
        headers={"Cache-Control": "no-cache", "X-Accel-Buffering": "no"},
    )


@router.delete("/chat/{conversation_id}")
async def delete_conversation(conversation_id: str) -> JSONResponse:
    await delete_history(conversation_id)
    return success({"conversation_id": conversation_id})
