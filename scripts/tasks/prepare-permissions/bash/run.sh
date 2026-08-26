#!/bin/bash
set -euo pipefail

echo "Setting permissions for required var/ directories..."
chmod 775 var/cache var/log var/sessions var/tmp var/tools 2>/dev/null || true

echo "Setting permissions for var/..."
find var/ -user "$(id -u)" -exec chmod 775 {} \;

echo "Making project scripts executable..."
find scripts/ -type f -name "*.sh" -exec chmod +x {} \;

echo "Making bin/ executables executable..."
find bin/ -type f -exec chmod +x {} \;

echo "Making vendor binaries executable..."
find vendor/bin/ -type f -exec chmod +x {} \;

echo "Done."
