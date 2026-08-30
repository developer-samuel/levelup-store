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

sudo -n chown -R "$(id -u):$(id -g)" var/ 2>/dev/null || true
chmod -R 775 var/ 2>/dev/null || true

# ─── Shell scripts ────────────────────────────────────────────────────────────

echo "Making project scripts executable..."
if [ -d "scripts/" ]; then
    find scripts/ -type f -name "*.sh" -exec chmod +x {} +
fi
if [ -d "docker/" ]; then
    find docker/ -type f -name "*.sh" -exec chmod +x {} +
fi

# ─── bin/ ─────────────────────────────────────────────────────────────────────

echo "Making bin/ executables executable..."
if [ -d "bin/" ]; then
    find bin/ -type f -exec chmod +x {} +
fi

# ─── vendor/bin/ ──────────────────────────────────────────────────────────────

echo "Making vendor binaries executable..."
if [ -d "vendor/bin/" ]; then
    find vendor/bin/ -type f -exec chmod +x {} +
fi

# ─── node_modules/ ────────────────────────────────────────────────────────────

echo "Fixing node_modules ownership and permissions..."
if [ -d "node_modules/" ]; then
    sudo -n chown -R "$(id -u):$(id -g)" node_modules/ 2>/dev/null || true
    chmod +x node_modules/.bin/* 2>/dev/null || true
fi

echo "Done."
