#!/bin/bash
set -euo pipefail

run_php_metrics() {
    echo "🟢 Running PHP-Metrics..."

    phpmetrics --report-html="$REPORT_HTML" src/

    echo "✅ PHP-Metrics completed successfully."
}
