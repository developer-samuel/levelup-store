import json
from collections.abc import AsyncGenerator

from app.adapters import api_adapter, ollama_adapter
from app.config import settings
from app.services.model_usage_service import (
    check_and_increment_fallback,
    check_and_increment_ollama,
    check_and_increment_primary,
    is_primary_exhausted,
)

_MSG_LIMIT_REACHED = "Request limit reached. Please try again later."


async def stream(
    messages: list[dict[str, str]],
    conversation_id: str,
) -> AsyncGenerator[str, None]:
    if settings.assistant_type == "ollama":
        allowed = await check_and_increment_ollama()

        if not allowed:
            yield json.dumps({"success": False, "message": _MSG_LIMIT_REACHED})
            return

        model_event = {"model": settings.assistant_ollama_model, "fallback": False}

        yield json.dumps({"success": True, "data": model_event})

        async for chunk in ollama_adapter.stream(messages, conversation_id):
            yield chunk

    else:
        primary_exhausted = await is_primary_exhausted()

        if not primary_exhausted:
            allowed = await check_and_increment_primary()

            if allowed:
                model_event = {"model": settings.assistant_model, "fallback": False}

                yield json.dumps({"success": True, "data": model_event})

                async for chunk in api_adapter.stream(
                    messages, conversation_id, model=settings.assistant_model
                ):
                    yield chunk

                return

        allowed = await check_and_increment_fallback()

        if not allowed:
            yield json.dumps({"success": False, "message": _MSG_LIMIT_REACHED})
            return

        model_event = {"model": settings.assistant_fallback_model, "fallback": True}

        yield json.dumps({"success": True, "data": model_event})

        async for chunk in api_adapter.stream(
            messages, conversation_id, model=settings.assistant_fallback_model
        ):
            yield chunk
