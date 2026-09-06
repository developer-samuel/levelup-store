#!/bin/bash

# ────────────── Config ──────────────
BASE_DIR="$(dirname "$(dirname "$0")")/bootstrap"

CACHE_FILE="var/tools/php-unit/.phpunit.result.cache"
COVERAGE_HTML="var/tools/php-unit/html"
COVERAGE_CLOVER="var/tools/php-unit/clover.xml"
