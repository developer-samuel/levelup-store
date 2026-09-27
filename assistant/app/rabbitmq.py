from __future__ import annotations

import json

import aio_pika

from app.config import settings

QUEUE_NAME = "ai_requests"


async def publish_request(
    request_id: str,
    conversation_id: str,
    message: str,
) -> None:
    """Publish an AI chat request to the RabbitMQ queue."""

    connection = await aio_pika.connect_robust(settings.rabbitmq_url)

    async with connection:
        channel = await connection.channel()

        await channel.declare_queue(QUEUE_NAME, durable=True)
        await channel.default_exchange.publish(
            aio_pika.Message(
                body=json.dumps(
                    {
                        "request_id": request_id,
                        "conversation_id": conversation_id,
                        "message": message,
                    }
                ).encode(),
                delivery_mode=aio_pika.DeliveryMode.PERSISTENT,
            ),
            routing_key=QUEUE_NAME,
        )
