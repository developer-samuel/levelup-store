#!/bin/bash
set -e

if [ -f public/hot ] && ! docker ps --filter "name=levelup_store_vite" --filter "status=running" | grep -q levelup_store_vite; then
  echo "🧹 Removing public/hot because vite container is not running..."
  rm public/hot
fi

echo "⚙️ Rebuilding native modules for container platform..."
npm rebuild esbuild --silent

echo "⚙️ Building assets..."
npm run build
echo "✅ Assets built."

echo "⚙️ Generating ESLint report..."
mkdir -p var/tools/eslint
npm run lint:report
echo "✅ ESLint report generated."
