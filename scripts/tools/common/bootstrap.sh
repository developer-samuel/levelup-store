#!/bin/bash
set -euo pipefail

# ────────────── Boostrap function ──────────────
bootstrap() {
    for script in clean.sh setup.sh; do
        local path="$BASE_DIR/$script"
        
        if [[ -f "$path" && -x "$path" ]]; then
            echo "🟢 Executing $script..."
            "$path"
        else
            echo "⚠️  $script not found or not executable at $path, skipping."
        fi
    done
}