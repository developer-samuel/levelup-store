#!/bin/bash
set -euo pipefail

# ─── Project ownership ────────────────────────────────────────────────────────

echo "Fixing project ownership..."
if command -v sudo &>/dev/null; then
    sudo chown -R "$(id -u):$(id -g)" .
fi

# ─── Shell scripts ────────────────────────────────────────────────────────────

echo "Making project scripts executable..."

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

# ─── Frontend package files ───────────────────────────────────────────────────

for f in package.json package-lock.json pnpm-lock.yaml pnpm-workspace.yaml; do
    [ -f "$f" ] && chmod 644 "$f"
done

# ─── dist/ ────────────────────────────────────────────────────────────────────

if [ -d "dist/" ]; then
    if command -v sudo &>/dev/null; then sudo chown -R "$(id -u):$(id -g)" dist/; fi
    find dist/ -type d -exec chmod 775 {} +
    find dist/ -type f -exec chmod 664 {} +
fi

echo "✅ Permissions prepared."
