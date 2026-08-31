#!/bin/bash
set -euo pipefail

run_php_cs_fixer() {
  echo "🟢 Running PHP-CS-Fixer..."

  php-cs-fixer fix --config="$PHP_CS_FIXER_CONFIG" --cache-file="$CACHE_FILE" --verbose --diff

  echo "✅ PHP-CS-Fixer completed successfully."
}
