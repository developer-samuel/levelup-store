#!/bin/bash
set -e

if [ -f public/hot ] && ! docker ps --filter "name=levelup_store_ecommerce_vite" --filter "status=running" | grep -q levelup_store_ecommerce_vite; then
  echo "🧹 Removing public/hot because vite container is not running..."
  rm public/hot
fi

echo "⚙️ Installing frontend dependencies..."
pnpm config set store-dir /tmp/.pnpm-store
cd /var/www && pnpm install

echo "⚙️ Building all assets..."
pnpm build
echo "✅ All assets built."

echo "⚙️ Generating ESLint reports..."
mkdir -p apps/ecommerce/var/tools/eslint
mkdir -p apps/assistant/client/reports
pnpm lint:report
echo "✅ ESLint reports generated."
