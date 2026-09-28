#!/bin/bash
set -e

# ────────────── Clean directory function ──────────────
clean_directory() {
    local dir=$1
    if [ -d "$dir" ]; then
        echo "🟢 Removing old $dir folder..."
        rm -rf "$dir"
    fi
}
