#!/bin/bash
set -e

echo "🔧 Installing MinIO Client (mc)..."
curl -sSfL https://github.com/minio/mc/releases/download/RELEASE.2025-08-13T08-35-41Z/mc.linux-amd64.RELEASE.2025-08-13T08-35-41Z -o /usr/local/bin/mc
chmod +x /usr/local/bin/mc

echo "✅ MinIO Client installed."
