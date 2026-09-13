#!/bin/bash
set -e

echo "🧹 Clearing cache..."
find var/cache -mindepth 1 -delete 2>/dev/null || true
echo "✅ Cache cleared."

echo "📁 Ensuring cache directories exist..."
mkdir -p var/cache/local
echo "✅ Cache directories ready."

echo "⚡ Warming up Symfony cache..."
php bin/console cache:warmup
echo "✅ Symfony cache warmed up."

echo "🔧 Fixing cache ownership..."
chown -R www-data:www-data /var/www/var/cache
echo "✅ Cache ownership fixed."