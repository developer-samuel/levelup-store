#!/bin/bash
set -euo pipefail

# ────────────── Prepare var/tmp ──────────────
echo "Checking if var/tmp exists..."

if [ ! -d "var/tmp" ]; then
    echo "Folder not found, creating var/tmp..."
    mkdir -p var/tmp
else
    echo "Folder already exists, skipping..."
fi

echo "Done."
