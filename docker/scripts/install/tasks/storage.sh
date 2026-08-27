#!/bin/bash
set -e

echo "🔧 Installing MinIO Client (mc)..."
curl -sSL https://dl.min.io/client/mc/release/linux-amd64/mc -o /usr/local/bin/mc
chmod +x /usr/local/bin/mc

echo "✅ MinIO Client installed."
