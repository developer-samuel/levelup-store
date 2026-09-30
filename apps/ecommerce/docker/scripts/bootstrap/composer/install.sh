#!/bin/bash
set -e

# 📥 Run composer install if the 'vendor' directory does not exist or is empty
if [ ! -d "vendor" ] || [ -z "$(ls -A vendor)" ]; then
  echo "📦 Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
else
  echo "✅ Vendor folder exists, skipping installation."
fi