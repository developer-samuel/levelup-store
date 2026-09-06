#!/bin/bash
set -euo pipefail

# ────────────── Load Config ──────────────
source scripts/tools/php-metrics/php-metrics.config.sh

# ────────────── Load Functions ──────────────
source scripts/tools/common/bootstrap.sh
source scripts/tools/php-metrics/functions/php-metrics.sh

# ────────────── Execute ──────────────
bootstrap
run_php_metrics
