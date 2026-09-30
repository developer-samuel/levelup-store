#!/bin/bash
set -e

# 🧐 Check if Composer is installed; if not, install it
if ! command -v composer >/dev/null 2>&1; then
  echo "🛠️ Installing Composer (composer.json dependencies will be handled later)"

  # 📥 Download expected checksum of the installer
  EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"

  # 📥 Download Composer installer script
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

  # 🔍 Calculate actual checksum of the downloaded installer
  ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

  # ✅ Verify installer checksum matches expected checksum
  if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
    echo "❌ Invalid installer checksum"
    rm composer-setup.php
    exit 1
  fi

  # 🚀 Run the installer to install Composer globally
  php composer-setup.php --install-dir=/usr/local/bin --filename=composer
  rm composer-setup.php
  echo "✅ Composer installed successfully."
fi
