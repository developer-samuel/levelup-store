#!/bin/bash
set -euo pipefail

echo "Cleaning stale var/ directories..."
rm -rf var/cache var/log var/sessions var/tmp

echo "Creating required var/ directories..."
mkdir -p var/cache var/log var/sessions var/tmp var/tools


echo "Done."
