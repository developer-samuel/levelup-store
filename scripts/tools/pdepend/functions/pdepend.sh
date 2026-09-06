#!/bin/bash
set -euo pipefail

run_pdepend() {
  echo "🟢 Running PDepend analysis..."

  vendor/bin/pdepend --summary-xml="$REPORT_SUMMARY_XML" --jdepend-xml="$REPORT_JDEPEND_XML" src/

  echo "✅ PDepend reports generated successfully."
}
