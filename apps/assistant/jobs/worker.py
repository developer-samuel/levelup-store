"""AI request worker.

Consumes chat requests from RabbitMQ, streams responses via Ollama,
and publishes each token to the per-request Redis pub/sub channel.
After finishing, it checks for queued follow-up requests for the
same conversation and re-enqueues them so they are processed next.

Run with:
    python -m jobs.worker
"""

from __future__ import annotations

import asyncio
import json
import logging
from contextlib import suppress

import aio_pika

from app.config import settings
from app.queue import (
    finish_processing,
    is_processing_request,
    publish_chunk,
    renew_processing,
    wait_for_cancellation,
)
from app.rabbitmq import QUEUE_NAME, publish_request
from app.services.chat_service import stream_chat

logging.basicConfig(level=logging.INFO)
log = logging.getLogger(__name__)


async def _stream_response(request_id: str, conversation_id: str, message: str) -> None:
    async for data in stream_chat(message, conversation_id):
        await publish_chunk(conversation_id, request_id, data)


async def _maintain_processing_lease(request_id: str) -> None:
    while True:
        await asyncio.sleep(60)

        if not await renew_processing(request_id):
            return


async def _process(request_id: str, conversation_id: str, message: str) -> None:
    if not await is_processing_request(request_id):
        log.warning("Skipping stale request %s; it no longer owns the worker slot", request_id)

        await publish_chunk(
            conversation_id,
            request_id,
            json.dumps(
                {
                    "success": False,
                    "message": "This request expired before processing. Please retry.",
                }
            ),
        )
        return

    log.info("Processing request %s for conversation %s", request_id, conversation_id)

    stream_task = asyncio.create_task(_stream_response(request_id, conversation_id, message))
    cancel_task = asyncio.create_task(wait_for_cancellation(request_id))
    lease_task = asyncio.create_task(_maintain_processing_lease(request_id))

    try:
        done, _ = await asyncio.wait(
            {stream_task, cancel_task, lease_task},
            return_when=asyncio.FIRST_COMPLETED,
        )
        if lease_task in done:
            await lease_task
        if cancel_task in done or lease_task in done:
            stream_task.cancel()
            with suppress(asyncio.CancelledError):
                await stream_task
            if cancel_task in done:
                await publish_chunk(
                    conversation_id,
                    request_id,
                    json.dumps({"success": True, "data": {"done": True, "cancelled": True}}),
                )
        else:
            cancel_task.cancel()

            with suppress(asyncio.CancelledError):
                await cancel_task
            await stream_task
    except Exception:
        log.exception("Error processing request %s", request_id)

        await publish_chunk(
            conversation_id,
            request_id,
            json.dumps({"success": False, "message": "Something went wrong. The server may be busy - please try again."}),
        )
    finally:
        if not stream_task.done():
            stream_task.cancel()
            with suppress(asyncio.CancelledError):
                await stream_task
        if not cancel_task.done():
            cancel_task.cancel()
            with suppress(asyncio.CancelledError):
                await cancel_task
        if not lease_task.done():
            lease_task.cancel()
            with suppress(asyncio.CancelledError):
                await lease_task

        next_req = await finish_processing(request_id)

        if next_req is not None:
            next_id = next_req["request_id"]
            next_conv = next_req["conversation_id"]
            next_msg = next_req["message"]
            log.info("Handing off to next request %s for conversation %s", next_id, next_conv)
            await publish_request(next_id, next_conv, next_msg)


async def main() -> None:
    log.info("Connecting to RabbitMQ at %s", settings.rabbitmq_url)
    connection = await aio_pika.connect_robust(settings.rabbitmq_url)

    async with connection:
        channel = await connection.channel()

        await channel.set_qos(prefetch_count=1)

        queue = await channel.declare_queue(QUEUE_NAME, durable=True)
        log.info("Worker ready. Waiting for messages on queue '%s'.", QUEUE_NAME)

        async with queue.iterator() as queue_iter:
            async for amqp_message in queue_iter:
                try:
                    async with amqp_message.process(requeue=True):
                        body: dict[str, str] = json.loads(amqp_message.body)
                        await _process(
                            body["request_id"],
                            body["conversation_id"],
                            body["message"],
                        )
                except Exception:
                    log.exception("Request failed; RabbitMQ will requeue it")
                    await asyncio.sleep(2)


if __name__ == "__main__":
    asyncio.run(main())
