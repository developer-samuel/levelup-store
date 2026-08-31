#!/bin/bash
set -e

# ────────────── Clean old PHPUnit folder ──────────────
source scripts/tools/common/directory/clean.sh
clean_directory "var/tools/php-unit"
