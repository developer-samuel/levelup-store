import asyncio
import json
import logging
from collections.abc import AsyncGenerator
from typing import Annotated

from fastapi import APIRouter, Depends
from fastapi.responses import JSONResponse, StreamingResponse

from app.config import settings
from app.guard import verify_api_key
from app.messaging.queue import (
    cancel_request,
    consume_subscription,
    is_processing_request,
    load_response,
    open_subscription,
    publish_chunk,
    register_request,
    subscribe_stream,
)
from app.messaging.rabbitmq import publish_request
from app.responses import success

router = APIRouter()

_SSE_HEADERS = {"Cache-Control": "no-cache", "X-Accel-Buffering": "no"}


async def _done_sse() -> AsyncGenerator[str, None]:
    yield f"data: {json.dumps({'success': True, 'data': {'done': True}})}\n\n"


async def _replay_sse(saved: str) -> AsyncGenerator[str, None]:
    yield f"data: {json.dumps({'success': True, 'data': {'thinking': True}})}\n\n"
    yield f"data: {json.dumps({'success': True, 'data': {'token': saved}})}\n\n"
    yield f"data: {json.dumps({'success': True, 'data': {'done': True}})}\n\n"


async def queued_sse(
    message: str,
    conversation_id: str,
    request_id: str,
) -> AsyncGenerator[str, None]:
    """Queue-aware SSE: publishes to RabbitMQ and streams via Redis pub/sub.

    If another request for the same conversation is already being processed,
    the new request is queued and a ``queued`` event is sent to the client
    first so the UI can show the queue status.
    """

    try:
        r, pubsub = await open_subscription(conversation_id, request_id)

        position = await register_request(
            {
                "request_id": request_id,
                "conversation_id": conversation_id,
                "message": message,
            }
        )

        if position < 0:
            await publish_chunk(
                conversation_id,
                request_id,
                json.dumps({"success": True, "data": {"done": True, "cancelled": True}}),
            )
        elif position == 0:
            thinking_event = json.dumps({"success": True, "data": {"thinking": True}})

            yield f"data: {thinking_event}\n\n"

            await publish_request(request_id, conversation_id, message)
        else:
            queued_event = json.dumps(
                {"success": True, "data": {"queued": True, "queue_position": position}}
            )

            yield f"data: {queued_event}\n\n"

        async for chunk in consume_subscription(r, pubsub, conversation_id, request_id):
            yield f"data: {chunk}\n\n"

    except asyncio.CancelledError:
        logging.getLogger(__name__).debug("SSE stream cancelled")
        raise


async def _reattach_sse(conversation_id: str, request_id: str) -> AsyncGenerator[str, None]:
    try:
        async for chunk in subscribe_stream(conversation_id, request_id):
            yield f"data: {chunk}\n\n"
    except asyncio.CancelledError:
        logging.getLogger(__name__).debug("SSE reattach stream cancelled")
        raise


@router.get("/chat/reattach/{conversation_id}/{request_id}")
async def reattach_chat(
    conversation_id: str,
    request_id: str,
    _: Annotated[None, Depends(verify_api_key)],
) -> StreamingResponse:
    """Re-subscribe to an in-progress stream after a page reload.

    Does NOT create a new queue entry - only attaches to the existing Redis
    pub/sub channel.  Returns a ``done`` event immediately when queue mode is
    disabled or the worker has already finished.
    """

    if not settings.rabbitmq_url:
        return StreamingResponse(_done_sse(), media_type="text/event-stream", headers=_SSE_HEADERS)

    if not await is_processing_request(request_id):
        saved = await load_response(request_id)

        if saved:
            return StreamingResponse(
                _replay_sse(saved), media_type="text/event-stream", headers=_SSE_HEADERS
            )

        return StreamingResponse(_done_sse(), media_type="text/event-stream", headers=_SSE_HEADERS)

    return StreamingResponse(
        _reattach_sse(conversation_id, request_id),
        media_type="text/event-stream",
        headers=_SSE_HEADERS,
    )


@router.post("/chat/requests/{request_id}/cancel")
async def cancel_chat_request(
    request_id: str,
    _: Annotated[None, Depends(verify_api_key)],
) -> JSONResponse:
    if not settings.rabbitmq_url:
        return success({"cancelled": False})

    cancelled = await cancel_request(request_id)

    return success({"cancelled": cancelled})
