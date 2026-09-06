#!/bin/bash
set -euo pipefail

# ────────────── Load Config ──────────────
source scripts/tools/php-cs-fixer/php-cs-fixer.config.sh

# ────────────── Load Functions ──────────────
source scripts/tools/common/bootstrap.sh
source scripts/tools/php-cs-fixer/functions/php-cs-fixer.sh

# ────────────── Execute ──────────────
bootstrap
run_php_cs_fixer
