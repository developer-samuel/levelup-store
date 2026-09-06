#!/bin/bash
set -e

echo "📡 Checking RabbitMQ availability..."

until bash -c "echo > /dev/tcp/${RABBITMQ_HOST:-rabbitmq}/${RABBITMQ_PORT:-5672}" > /dev/null 2>&1; do
    echo "⏳ RabbitMQ not ready yet, waiting..."
    sleep 3
done

echo "✅ RabbitMQ is ready!"
