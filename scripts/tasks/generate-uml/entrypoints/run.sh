#!/bin/bash
set -euo pipefail

# ────────────── Config ──────────────
OUTPUT_DIR=".uml"
MMDC="./node_modules/.bin/mmdc"

# ────────────── Checks ──────────────
if ! command -v docker &>/dev/null && [ ! -f "$MMDC" ]; then
    echo "❌ mmdc not found. Run: pnpm install"
    exit 1
fi

# ────────────── Setup ──────────────
echo "🟢 Cleaning $OUTPUT_DIR..."
rm -rf "$OUTPUT_DIR"
mkdir -p "$OUTPUT_DIR"

# ────────────── Generate ──────────────
echo "🟢 Generating UML diagrams..."

if command -v docker &>/dev/null; then
    docker run --rm \
        -v "$(pwd)/docs:/data" \
        -v "$(pwd)/$OUTPUT_DIR:/$OUTPUT_DIR" \
        --user "$(id -u):$(id -g)" \
        --entrypoint sh \
        "ghcr.io/mermaid-js/mermaid-cli/mermaid-cli:latest" \
        -c "find /data/diagrams -name '*.mmd' | while read f; do
            rel=\"\${f#/data/diagrams/}\"; dir=\$(dirname \"\$rel\"); name=\$(basename \"\$f\" .mmd)
            mkdir -p \"/$OUTPUT_DIR/\$dir\"
            echo \"  → \$dir/\$name\"
            /home/mermaidcli/node_modules/.bin/mmdc -p /puppeteer-config.json -i \"\$f\" -o \"/$OUTPUT_DIR/\$dir/\${name}.png\" --scale 3 2>/dev/null
        done"
else
    find docs/diagrams -name "*.mmd" | while read -r f; do
        rel="${f#docs/diagrams/}"
        dir=$(dirname "$rel")
        name=$(basename "$f" .mmd)
        mkdir -p "$OUTPUT_DIR/$dir"
        echo "  → $dir/$name"
        "$MMDC" -i "$f" -o "$OUTPUT_DIR/$dir/${name}.png" --scale 3 2>/dev/null
    done
fi

echo "✅ UML diagrams generated in $OUTPUT_DIR/"
