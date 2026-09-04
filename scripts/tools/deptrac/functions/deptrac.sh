#!/bin/bash
set -euo pipefail

run_deptrac() {
  echo "🟢 Running Deptrac analysis..."

  deptrac analyse --config-file="$DEPTRAC_CONFIG" --cache-file="$CACHE_FILE" --formatter json > "$REPORT_JSON"

  echo ""
  echo "✅ Deptrac reports generated successfully."
}
