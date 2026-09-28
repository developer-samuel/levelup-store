#!/bin/bash
set -euo pipefail

# ─── var/ ─────────────────────────────────────────────────────────────────────

echo "Creating required var/ directories..."
mkdir -p \
    var/cache \
    var/log \
    var/sessions \
    var/tmp \
    var/tools

find var/ -type d -exec chmod 777 {} +
find var/ -type f -exec chmod 666 {} +

# ─── Shell scripts ────────────────────────────────────────────────────────────

echo "Making ecommerce scripts executable..."

if [ -d "scripts/" ]; then
    find scripts/ -type f -name "*.sh" -exec chmod +x {} +
fi

if [ -d "docker/" ]; then
    find docker/ -type f -name "*.sh" -exec chmod +x {} +
fi

# ─── node_modules/ ────────────────────────────────────────────────────────────

if [ -d "node_modules/" ]; then
    if command -v sudo &>/dev/null; then sudo chown -R "$(id -u):$(id -g)" node_modules/; fi
    chmod +x node_modules/.bin/* 2>/dev/null || true
fi

# ─── bin/ ─────────────────────────────────────────────────────────────────────

if [ -d "bin/" ]; then
    find bin/ -type f -exec chmod +x {} +
fi

# ─── vendor/bin/ ──────────────────────────────────────────────────────────────

if [ -d "vendor/bin/" ]; then
    find vendor/bin/ -type f -exec chmod +x {} +
fi

echo "✅ Ecommerce permissions prepared."
