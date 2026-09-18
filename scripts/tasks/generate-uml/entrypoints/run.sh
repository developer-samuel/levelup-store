#!/bin/bash
set -euo pipefail

# ────────────── Config ──────────────
OUTPUT_DIR=".uml"
MMDC="./node_modules/.bin/mmdc"

# ────────────── Checks ──────────────
if [ ! -f "$MMDC" ]; then
    echo "❌ mmdc not found. Run: pnpm install"
    exit 1
fi

# ────────────── Setup ──────────────
echo "🟢 Cleaning $OUTPUT_DIR..."
rm -rf "$OUTPUT_DIR"
mkdir -p "$OUTPUT_DIR"

# ────────────── Generate ──────────────
echo "🟢 Generating UML diagrams..."

find docs/diagrams -name "*.mmd" | while read -r f; do
    rel="${f#docs/diagrams/}"
    dir=$(dirname "$rel")
    name=$(basename "$f" .mmd)
    mkdir -p "$OUTPUT_DIR/$dir"
    echo "  → $dir/$name"
    "$MMDC" -i "$f" -o "$OUTPUT_DIR/$dir/${name}.png" --scale 3 2>/dev/null
done

echo "✅ UML diagrams generated in $OUTPUT_DIR/"
