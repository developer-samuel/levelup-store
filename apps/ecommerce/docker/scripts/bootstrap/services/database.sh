#!/bin/bash
set -e

echo "📡 Checking database availability..."

until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" > /dev/null 2>&1; do
  echo "⏳ PostgreSQL is not ready yet, waiting..."
  sleep 3
done

echo "✅ PostgreSQL is ready!"
