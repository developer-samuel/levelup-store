#!/bin/bash
set -e

echo "📡 Checking Redis server availability..."

REDIS_PASSWORD=$(echo "$REDIS_URL" | sed -n 's|redis://[^:]*:\([^@]*\)@.*|\1|p')

until redis-cli -h "$REDIS_HOST" -p "$REDIS_PORT" ${REDIS_PASSWORD:+-a "$REDIS_PASSWORD" --no-auth-warning} ping | grep -q PONG; do
    echo "⏳ Redis not ready yet, waiting..."
    sleep 3
done

echo "✅ Redis is ready!"
