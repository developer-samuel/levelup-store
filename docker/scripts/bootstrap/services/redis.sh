#!/bin/bash
set -e

echo "📡 Checking Redis server availability..."

until redis-cli -h "$REDIS_HOST" -p "$REDIS_PORT" ping | grep -q PONG; do
    echo "⏳ Redis not ready yet, waiting..."
    sleep 3
done

echo "✅ Redis is ready!"
