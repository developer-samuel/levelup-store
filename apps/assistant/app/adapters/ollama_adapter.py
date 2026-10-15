import json
from collections.abc import AsyncGenerator

import ollama as _ollama
from httpx import ConnectError

from app.config import settings


async def stream(
    messages: list[dict[str, str]],
    conversation_id: str,
) -> AsyncGenerator[str, None]:
    client = _ollama.AsyncClient(host=settings.assistant_ollama_host)

    try:
        async for chunk in await client.chat(
            model=settings.assistant_ollama_model,
            messages=messages,
            stream=True,
            options={"temperature": 0.1},
        ):
            token = chunk["message"]["content"]

            if token:
                yield json.dumps(
                    {"success": True, "data": {"token": token, "conversation_id": conversation_id}}
                )

        yield json.dumps(
            {"success": True, "data": {"done": True, "conversation_id": conversation_id}}
        )
    except (ConnectError, _ollama.ResponseError):
        msg = "Something went wrong. The server may be busy - please try again."
        yield json.dumps({"success": False, "message": msg})
