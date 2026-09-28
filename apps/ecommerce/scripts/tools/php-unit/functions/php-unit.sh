#!/bin/bash
set -euo pipefail

run_phpunit() {
    echo "🟢 Running PHPUnit..."
    php bin/phpunit \
        --testdox \
        --coverage-html="$COVERAGE_HTML"
}
