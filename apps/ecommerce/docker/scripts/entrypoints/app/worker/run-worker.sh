#!/bin/bash
set -e

LOG_FILE="/var/www/apps/ecommerce/var/log/worker.log"

echo "⏳ Waiting for database migrations to be ready..." >> "$LOG_FILE" 2>&1

until php /var/www/apps/ecommerce/bin/console doctrine:migrations:status --env=dev >/dev/null 2>&1; do
    echo "$(date '+%Y-%m-%d %H:%M:%S') Database not ready, sleeping 5s..." >> "$LOG_FILE" 2>&1
    sleep 5
done

echo "✅ Database ready. Starting messenger worker..." >> "$LOG_FILE" 2>&1

php /var/www/apps/ecommerce/bin/console messenger:consume async --time-limit=3600 --memory-limit=128M -vv >> "$LOG_FILE" 2>&1
