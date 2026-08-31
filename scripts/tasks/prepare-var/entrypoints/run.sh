#!/bin/bash
set -euo pipefail

echo "Cleaning stale var/ directories..."
sudo rm -rf var/cache var/log var/sessions var/tmp

echo "Creating required var/ directories..."
mkdir -p var/cache var/log var/sessions var/tmp var/tools

if [ -d var/ ]; then
    sudo chown -R "$(id -u):$(id -g)" var/
fi

echo "Done."
