#!/bin/bash
set -euo pipefail

# Disable OTel auto-instrumentation for CLI tools - extension is not loaded locally
export OTEL_PHP_DISABLED_INSTRUMENTATIONS=all

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