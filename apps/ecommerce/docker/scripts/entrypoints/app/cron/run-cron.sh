#!/bin/bash
set -e

LOG_FILE="/var/www/apps/ecommerce/var/log/cron.log"

echo "⏳ Waiting for database migrations to be ready..." >> "$LOG_FILE" 2>&1

until php /var/www/apps/ecommerce/bin/console doctrine:migrations:status --env=dev >/dev/null 2>&1; do
    echo "$(date '+%Y-%m-%d %H:%M:%S') Database not ready, sleeping 5s..." >> "$LOG_FILE" 2>&1
    sleep 5
done

echo "✅ Database ready. Starting scheduler..." >> "$LOG_FILE" 2>&1

echo "🕒 $(date '+%Y-%m-%d %H:%M:%S') Starting scheduler..." >> "$LOG_FILE" 2>&1
exec php /var/www/apps/ecommerce/bin/console messenger:consume scheduler_default >> "$LOG_FILE" 2>&1
