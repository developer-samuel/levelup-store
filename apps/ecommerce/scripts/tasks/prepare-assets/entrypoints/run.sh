#!/bin/bash
set -euo pipefail

# ────────────── Prepare assets/controllers ──────────────
echo "Checking if assets/controllers exists..."

if [ ! -d "assets/controllers" ]; then
    echo "Folder not found, creating assets/controllers..."
    mkdir -p assets/controllers
else
    echo "Folder already exists, skipping..."
fi

echo "Done."
