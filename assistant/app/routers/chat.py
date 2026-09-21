import asyncio
import json
import uuid
from collections.abc import AsyncGenerator

import ollama
from fastapi import APIRouter
from fastapi.responses import StreamingResponse
from httpx import ConnectError

from app.config import settings
from app.models import ChatRequest
from app.prompts import CHAT_SYSTEM_PROMPT
from app.rag import query

router = APIRouter()


@router.post("/chat")
async def chat(request: ChatRequest) -> StreamingResponse:
    conversation_id = request.conversation_id or str(uuid.uuid4())
    client = ollama.AsyncClient(host=settings.ollama_host)
    context = await asyncio.to_thread(query, request.message)

    async def stream() -> AsyncGenerator[str, None]:
        try:
            async for chunk in await client.chat(
                model=settings.ollama_model,
                messages=[
                    {
                        "role": "system",
                        "content": CHAT_SYSTEM_PROMPT.format(
                            context=context or "No relevant products found."
                        ),
                    },
                    {"role": "user", "content": request.message},
                ],
                stream=True,
                options={"temperature": 0.1},
            ):
                token = chunk["message"]["content"]
                if token:
                    data = json.dumps({"token": token, "conversation_id": conversation_id})
                    yield f"data: {data}\n\n"
            yield f"data: {json.dumps({'done': True, 'conversation_id': conversation_id})}\n\n"
        except ConnectError:
            payload = {"error": "Ollama service unavailable", "conversation_id": conversation_id}
            yield f"data: {json.dumps(payload)}\n\n"
        except ollama.ResponseError as e:
            yield f"data: {json.dumps({'error': str(e), 'conversation_id': conversation_id})}\n\n"

    return StreamingResponse(
        stream(),
        media_type="text/event-stream",
        headers={"Cache-Control": "no-cache", "X-Accel-Buffering": "no"},
    )
