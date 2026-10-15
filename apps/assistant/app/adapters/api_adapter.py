import json
from collections.abc import AsyncGenerator

from google import genai
from google.genai import types

from app.config import settings


async def _stream_model(
    client: genai.Client,
    model: str,
    history: list[types.Content],
    config: types.GenerateContentConfig,
    conversation_id: str,
) -> AsyncGenerator[str, None]:
    async for chunk in await client.aio.models.generate_content_stream(
        model=model,
        contents=history,
        config=config,
    ):
        token = chunk.text

        if token:
            yield json.dumps(
                {"success": True, "data": {"token": token, "conversation_id": conversation_id}}
            )

    yield json.dumps({"success": True, "data": {"done": True, "conversation_id": conversation_id}})


async def generate(prompt: str) -> str:
    try:
        client = genai.Client(api_key=settings.assistant_api_key)

        response = await client.aio.models.generate_content(
            model=settings.assistant_model,
            contents=prompt,
            config=types.GenerateContentConfig(temperature=0),
        )

        return response.text or ""
    except Exception:
        return ""


async def stream(
    messages: list[dict[str, str]],
    conversation_id: str,
    model: str | None = None,
) -> AsyncGenerator[str, None]:
    client = genai.Client(api_key=settings.assistant_api_key)
    active_model = model or settings.assistant_model

    system_prompt = next(
        (m["content"] for m in messages if m["role"] == "system"),
        None,
    )

    history = [
        types.Content(
            role="user" if m["role"] == "user" else "model",
            parts=[types.Part(text=m["content"])],
        )
        for m in messages
        if m["role"] != "system"
    ]

    config = types.GenerateContentConfig(
        system_instruction=system_prompt,
        temperature=0.1,
    )

    try:
        async for chunk in _stream_model(client, active_model, history, config, conversation_id):
            yield chunk
    except Exception:
        yield json.dumps({"success": False, "message": "Something went wrong. Please try again."})
