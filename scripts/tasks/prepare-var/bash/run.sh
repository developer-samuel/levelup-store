#!/bin/bash
set -euo pipefail

echo "Cleaning stale var/ directories..."
sudo rm -rf var/cache var/log var/sessions var/tmp
sudo chown -R "$(id -u):$(id -g)" var/

echo "Creating required var/ directories..."
mkdir -p var/cache var/log var/sessions var/tmp var/tools

echo "Done."
