#!/bin/bash
set -e

echo "🧹 Clearing cache..."
find var/cache -mindepth 1 -delete 2>/dev/null || true
php bin/console cache:clear
echo "✅ Cache cleared."

echo "🔧 Fixing cache ownership..."
chown -R www-data:www-data /var/www/apps/ecommerce/var/cache
echo "✅ Cache ownership fixed."