#!/bin/bash

# ────────────── Config ──────────────
BASE_DIR="$(dirname "$(dirname "$0")")/bootstrap"

CACHE_FILE="var/tools/php-cs-fixer/.php-cs-fixer.cache"
PHP_CS_FIXER_CONFIG=".php-cs-fixer.dist.php"
