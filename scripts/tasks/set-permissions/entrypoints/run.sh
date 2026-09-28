#!/bin/bash
set -euo pipefail

# ─── Project ownership ────────────────────────────────────────────────────────

echo "Fixing project ownership..."
sudo chown -R "$(id -u):$(id -g)" .

# ─── var/ ─────────────────────────────────────────────────────────────────────

echo "Creating required var/ directories..."
mkdir -p \
    var/cache \
    var/log \
    var/sessions \
    var/tmp \
    var/tools

find var/ -type d -exec chmod 775 {} +
find var/ -type f -exec chmod 664 {} +

# ─── Shell scripts ───────────────────────────────────────────────────────────

echo "Making project scripts executable..."

if [ -d "scripts/" ]; then
    find scripts/ -type f -name "*.sh" -exec chmod +x {} +
fi

if [ -d "docker/" ]; then
    find docker/ -type f -name "*.sh" -exec chmod +x {} +
fi

# ─── bin/ ─────────────────────────────────────────────────────────────────────

if [ -d "bin/" ]; then
    find bin/ -type f -exec chmod +x {} +
fi

# ─── vendor/bin/ ──────────────────────────────────────────────────────────────

if [ -d "vendor/bin/" ]; then
    find vendor/bin/ -type f -exec chmod +x {} +
fi

# ─── node_modules/ ────────────────────────────────────────────────────────────

if [ -d "node_modules/" ]; then
    sudo chown -R "$(id -u):$(id -g)" node_modules/
    chmod +x node_modules/.bin/* 2>/dev/null || true
fi

# ─── Frontend package files ───────────────────────────────────────────────────

for f in package.json package-lock.json pnpm-lock.yaml pnpm-workspace.yaml; do
    [ -f "$f" ] && chmod 644 "$f"
done

# ─── dist/ ────────────────────────────────────────────────────────────

if [ -d "dist/" ]; then
    sudo chown -R "$(id -u):$(id -g)" dist/
    find dist/ -type d -exec chmod 775 {} +
    find dist/ -type f -exec chmod 664 {} +
fi

echo "✅ Permissions prepared."
