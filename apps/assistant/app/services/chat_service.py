import asyncio
import json
from collections.abc import AsyncGenerator, Coroutine
from typing import Any, cast

from redis.asyncio import Redis

from app.config import settings
from app.context_builder import (
    format_catalog_summary,
    format_top_ordered,
    format_top_reviewed,
    format_user_cart,
    format_user_name,
    format_user_order_profile,
    format_user_orders,
    format_user_reviews,
    format_user_wishlist,
)
from app.prompts import CHAT_SYSTEM_PROMPT
from app.repositories.cart_repository import fetch_user_cart
from app.repositories.order_repository import fetch_user_order_profile, fetch_user_orders
from app.repositories.product_repository import (
    fetch_catalog_summary,
    fetch_top_ordered_products,
    fetch_top_reviewed_products,
)
from app.repositories.review_repository import fetch_user_reviews
from app.repositories.user_repository import fetch_user_name
from app.repositories.wishlist_repository import fetch_user_wishlist
from app.services.guard_service import is_attack
from app.services.llm_service import stream as _llm_stream
from app.services.rag_service import query, query_documents

_TTL_GUEST = settings.conversation_ttl_guest
_TTL_USER = settings.conversation_ttl_user
_KEY_PREFIX = "conversation:"
_MAX_HISTORY = 30

_redis: Redis = cast(Redis, Redis.from_url(settings.redis_url, decode_responses=True))


async def close() -> None:
    await _redis.aclose()


async def _load_history(conversation_id: str) -> list[dict[str, str]]:
    data = await _redis.get(f"{_KEY_PREFIX}{conversation_id}")
    return json.loads(data) if data else []


async def _save_history(
    conversation_id: str, history: list[dict[str, str]], user_id: int | None = None
) -> None:
    ttl = _TTL_USER if user_id else _TTL_GUEST
    await _redis.setex(f"{_KEY_PREFIX}{conversation_id}", ttl, json.dumps(history[-_MAX_HISTORY:]))


async def delete_conversation(conversation_id: str) -> None:
    await _redis.delete(f"{_KEY_PREFIX}{conversation_id}")


async def stream_chat(
    message: str, conversation_id: str, user_id: int | None = None
) -> AsyncGenerator[str, None]:
    yield json.dumps({"success": True, "data": {"thinking": True}})

    if await is_attack(message):
        blocked_msg = (
            "I'm sorry, I'm unable to help with that request. "
            f"Please contact our support at {settings.support_email}."
        )

        yield json.dumps({"success": True, "data": {"token": blocked_msg}})
        yield json.dumps({"success": True, "data": {"done": True}})

        return

    history = await _load_history(conversation_id)

    recent = [m["content"] for m in history[-4:] if m.get("role") in ("user", "assistant")]
    rag_query = " ".join(recent + [message]) if recent else message

    gather_tasks: list[Coroutine[Any, Any, Any]] = [
        asyncio.to_thread(query, rag_query),
        asyncio.to_thread(query_documents, rag_query),
        asyncio.to_thread(fetch_catalog_summary),
        asyncio.to_thread(fetch_top_reviewed_products),
        asyncio.to_thread(fetch_top_ordered_products),
    ]
    if user_id:
        gather_tasks.extend(
            [
                asyncio.to_thread(fetch_user_orders, user_id),
                asyncio.to_thread(fetch_user_cart, user_id),
                asyncio.to_thread(fetch_user_reviews, user_id),
                asyncio.to_thread(fetch_user_wishlist, user_id),
                asyncio.to_thread(fetch_user_name, user_id),
                asyncio.to_thread(fetch_user_order_profile, user_id),
            ]
        )

    results = await asyncio.gather(*gather_tasks)
    rag_context = cast(str, results[0])
    documents_context = cast(str, results[1])
    catalog = cast(dict[str, list[str]], results[2])
    top_reviewed = cast(list[tuple[object, ...]], results[3])
    top_ordered = cast(list[tuple[object, ...]], results[4])
    user_orders = cast(list[tuple[object, ...]], results[5]) if user_id else []
    user_cart = cast(list[tuple[object, ...]], results[6]) if user_id else []
    user_reviews = cast(list[tuple[object, ...]], results[7]) if user_id else []
    user_wishlist = cast(list[tuple[object, ...]], results[8]) if user_id else []
    user_name = cast(tuple[str, str] | None, results[9]) if user_id else None
    user_order_profile = cast(list[tuple[object, ...]], results[10]) if user_id else []

    context_parts = list(
        filter(
            None,
            [
                format_user_name(user_name),
                rag_context,
                format_catalog_summary(catalog),
                format_top_reviewed(top_reviewed),
                format_top_ordered(top_ordered),
                format_user_order_profile(user_order_profile),
                format_user_orders(user_orders),
                format_user_cart(user_cart),
                format_user_reviews(user_reviews),
                format_user_wishlist(user_wishlist),
            ],
        )
    )

    context = "\n\n".join(context_parts) if context_parts else "No relevant products found."

    history.append({"role": "user", "content": message})

    messages = [
        {
            "role": "system",
            "content": CHAT_SYSTEM_PROMPT.format(
                context=context,
                documents_context=documents_context or "No store policy documents found.",
            ),
        },
        *history,
    ]

    full_response: list[str] = []

    async for raw in _llm_stream(messages, conversation_id):
        payload = json.loads(raw)

        if payload.get("success") and "token" in payload.get("data", {}):
            full_response.append(payload["data"]["token"])

        yield raw

    if full_response:
        history.append({"role": "assistant", "content": "".join(full_response)})

        await _save_history(conversation_id, history, user_id)
