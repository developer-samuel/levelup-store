#!/bin/bash
set -e

echo "⚙️ Installing dependencies..."
pnpm install --ignore-scripts
echo "✅ Dependencies installed."

echo "⚙️ Building assistant client..."
pnpm build
echo "✅ Client built."

echo "⚙️ Generating reports..."
mkdir -p reports

pnpm lint:report
echo "✅ ESLint report generated."
