import json
import uuid
from collections.abc import AsyncGenerator

import ollama
from fastapi import APIRouter
from fastapi.responses import StreamingResponse

from app.config import settings
from app.models import ChatRequest

router = APIRouter()

SYSTEM_PROMPT = (
    "You are a helpful assistant for LevelUp Store, an e-commerce platform. "
    "Help customers with product questions, order inquiries, and general support. "
    "Be concise, friendly, and professional."
)


@router.post("/chat")
async def chat(request: ChatRequest) -> StreamingResponse:
    conversation_id = request.conversation_id or str(uuid.uuid4())
    client = ollama.AsyncClient(host=settings.ollama_host)

    async def stream() -> AsyncGenerator[str, None]:
        async for chunk in await client.chat(
            model=settings.ollama_model,
            messages=[
                {"role": "system", "content": SYSTEM_PROMPT},
                {"role": "user", "content": request.message},
            ],
            stream=True,
        ):
            token = chunk["message"]["content"]
            if token:
                data = json.dumps({"token": token, "conversation_id": conversation_id})
                yield f"data: {data}\n\n"
        yield f"data: {json.dumps({'done': True, 'conversation_id': conversation_id})}\n\n"

    return StreamingResponse(
        stream(),
        media_type="text/event-stream",
        headers={"Cache-Control": "no-cache", "X-Accel-Buffering": "no"},
    )
