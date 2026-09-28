#!/bin/bash
set -e

echo "🔧 Installing MinIO Client (mc)..."
MC_ARCH=$(dpkg --print-architecture)
curl -sSfL "https://github.com/minio/mc/releases/download/RELEASE.2025-08-13T08-35-41Z/mc.linux-${MC_ARCH}.RELEASE.2025-08-13T08-35-41Z" -o /usr/local/bin/mc
chmod +x /usr/local/bin/mc

echo "✅ MinIO Client installed."
