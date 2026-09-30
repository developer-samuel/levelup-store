#!/bin/bash
set -e

echo "🟢 Setting up Node.js..."
curl -fsSL https://deb.nodesource.com/setup_24.x | bash -
apt-get install -y nodejs

echo "🟢 Setting up pnpm..."
corepack enable && corepack prepare pnpm@latest --activate
