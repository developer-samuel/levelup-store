#!/bin/bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "🟢 Cleaning .uml/..."
rm -rf .uml
mkdir -p .uml

bash "$SCRIPT_DIR/../operations/platform.sh"
bash "$SCRIPT_DIR/../operations/apps.sh"

echo ""
echo "✅ All UML diagrams generated in .uml/"
find .uml -name "*.png" | sort | sed 's/^/   /'
