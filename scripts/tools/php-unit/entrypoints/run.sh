#!/bin/bash
set -euo pipefail

# ────────────── Load Config ──────────────
source scripts/tools/php-unit/php-unit.config.sh

# ────────────── Load Functions ──────────────
source scripts/tools/common/bootstrap.sh
source scripts/tools/php-unit/functions/php-unit.sh

# ────────────── Execute ──────────────
bootstrap
run_phpunit
