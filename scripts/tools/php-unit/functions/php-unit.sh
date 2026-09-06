#!/bin/bash
set -euo pipefail

run_phpunit() {
    echo "🟢 Running PHPUnit..."
    php bin/phpunit \
        --testdox \
        --cache-result-file="$CACHE_FILE" \
        --coverage-html="$COVERAGE_HTML" \
        --coverage-clover="$COVERAGE_CLOVER"
}
