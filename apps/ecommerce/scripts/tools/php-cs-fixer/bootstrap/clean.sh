#!/bin/bash
set -e

# ────────────── Clean old PHP-CS-Fixer folder ──────────────
source scripts/tools/common/directory/clean.sh
clean_directory "var/tools/php-cs-fixer"
