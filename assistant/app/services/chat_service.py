import asyncio
import json
from collections.abc import AsyncGenerator

import ollama
from httpx import ConnectError

from app.config import settings
from app.conversation import delete_history, load_history, save_history
from app.prompts import CHAT_SYSTEM_PROMPT
from app.rag import query


async def delete_conversation(conversation_id: str) -> None:
    await delete_history(conversation_id)


async def stream_chat(message: str, conversation_id: str) -> AsyncGenerator[str, None]:
    client = ollama.AsyncClient(host=settings.ollama_host)

    history = await load_history(conversation_id)
    context = await asyncio.to_thread(query, message)

    history.append({"role": "user", "content": message})

    messages = [
        {
            "role": "system",
            "content": CHAT_SYSTEM_PROMPT.format(context=context or "No relevant products found."),
        },
        *history,
    ]

    full_response: list[str] = []

    try:
        async for chunk in await client.chat(
            model=settings.ollama_model,
            messages=messages,
            stream=True,
            options={"temperature": 0.1},
        ):
            token = chunk["message"]["content"]
            
            if token:
                full_response.append(token)

                yield json.dumps({"success": True, "data": {"token": token, "conversation_id": conversation_id}})

        history.append({"role": "assistant", "content": "".join(full_response)})
        await save_history(conversation_id, history)
        yield json.dumps({"success": True, "data": {"done": True, "conversation_id": conversation_id}})
    except ConnectError:
        yield json.dumps({"success": False, "message": "Ollama service unavailable"})
    except ollama.ResponseError as e:
        yield json.dumps({"success": False, "message": str(e)})
