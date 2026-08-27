#!/bin/bash
set -e

if [ "${REDIS_ENABLED}" = "true" ]; then
    echo "🧹 Clearing Redis cache (host=${REDIS_HOST} port=${REDIS_PORT})..."
    redis-cli -h "$REDIS_HOST" -p "$REDIS_PORT" FLUSHALL
    echo "✅ Redis cache cleared."
else
    echo "⏭️ Redis disabled, skipping Redis cache clear."
fi
