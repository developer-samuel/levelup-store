#!/bin/bash
set -e

echo "🧹 Clearing cache..."
find var/cache -mindepth 1 -delete 2>/dev/null || true
echo "✅ Cache cleared."

echo "📁 Ensuring cache directories exist..."
mkdir -p var/cache/local
echo "✅ Cache directories ready."

echo "⚡ Warming up Composer cache..."
composer cache:warmup
echo "✅ Composer cache warmed up."