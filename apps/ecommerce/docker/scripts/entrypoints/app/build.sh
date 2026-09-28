#!/bin/bash
set -e

if [ -f public/hot ] && ! docker ps --filter "name=levelup_store_ecommerce_vite" --filter "status=running" | grep -q levelup_store_ecommerce_vite; then
  echo "🧹 Removing public/hot because vite container is not running..."
  rm public/hot
fi

echo "⚙️ Installing frontend dependencies..."
pnpm config set store-dir /tmp/.pnpm-store
pnpm install --frozen-lockfile

echo "⚙️ Building all assets..."
pnpm build:all
echo "✅ All assets built."

echo "⚙️ Generating ESLint report..."
mkdir -p var/tools/eslint
pnpm lint:report
echo "✅ ESLint report generated."
