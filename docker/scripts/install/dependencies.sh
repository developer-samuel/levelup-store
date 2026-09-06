#!/bin/bash
set -e

export DEBIAN_FRONTEND=noninteractive

echo "🟢 Installing core dependencies..."
bash "$(dirname "$0")/tasks/core-dependencies.sh"

echo "🟢 Installing wkhtmltopdf..."
bash "$(dirname "$0")/tasks/wkhtmltopdf.sh"

echo "🟢 Setting up Node.js..."
bash "$(dirname "$0")/tasks/node.sh"

echo "🟢 Installing PHP extensions..."
bash "$(dirname "$0")/tasks/php-extensions.sh"

echo "🟢 Installing storage tools..."
bash "$(dirname "$0")/tasks/storage.sh"

echo "🧹 Cleaning up..."
rm -rf /var/lib/apt/lists/*
