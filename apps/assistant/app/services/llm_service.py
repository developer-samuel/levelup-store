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


async def _stream_ollama(
    messages: list[dict[str, str]], conversation_id: str
) -> AsyncGenerator[str, None]:
    allowed = await check_and_increment_ollama()

    if not allowed:
        yield json.dumps({"success": False, "message": _MSG_LIMIT_REACHED})
        return

    yield json.dumps(
        {"success": True, "data": {"model": settings.assistant_ollama_model, "fallback": False}}
    )

    async for chunk in ollama_adapter.stream(messages, conversation_id):
        yield chunk


async def _stream_api(
    messages: list[dict[str, str]], conversation_id: str
) -> AsyncGenerator[str, None]:
    primary_exhausted = await is_primary_exhausted()

    if not primary_exhausted:
        allowed = await check_and_increment_primary()

        if allowed:
            yield json.dumps(
                {"success": True, "data": {"model": settings.assistant_model, "fallback": False}}
            )

            async for chunk in api_adapter.stream(
                messages, conversation_id, model=settings.assistant_model
            ):
                yield chunk

            return

    allowed = await check_and_increment_fallback()

    if not allowed:
        yield json.dumps({"success": False, "message": _MSG_LIMIT_REACHED})
        return

    yield json.dumps(
        {"success": True, "data": {"model": settings.assistant_fallback_model, "fallback": True}}
    )

    async for chunk in api_adapter.stream(
        messages, conversation_id, model=settings.assistant_fallback_model
    ):
        yield chunk


async def stream(
    messages: list[dict[str, str]],
    conversation_id: str,
) -> AsyncGenerator[str, None]:
    if settings.assistant_type == "ollama":
        async for chunk in _stream_ollama(messages, conversation_id):
            yield chunk
    else:
        async for chunk in _stream_api(messages, conversation_id):
            yield chunk
