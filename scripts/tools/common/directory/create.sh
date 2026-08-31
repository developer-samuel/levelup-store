#!/bin/bash
set -e

# ────────────── Create directory function ──────────────
create_directory() {
    local directory=$1

    if [ ! -d "$directory" ]; then
        echo "🟢 Directory $directory does not exist, creating..."
        mkdir -p "$directory"
    else
        echo "ℹ️ Directory $directory already exists, continuing execution..."
    fi
}
