#!/bin/bash
set -euo pipefail

# ────────────── Config ──────────────
OUTPUT_DIR="docs/uml"
IMAGE="ghcr.io/mermaid-js/mermaid-cli/mermaid-cli:latest"
MMDC="/home/mermaidcli/node_modules/.bin/mmdc"

# ────────────── Setup ──────────────
if [ ! -d "$OUTPUT_DIR" ]; then
    echo "🟢 Creating $OUTPUT_DIR..."
    mkdir -p "$OUTPUT_DIR"
fi
chmod 777 "$OUTPUT_DIR"

# ────────────── Generate ──────────────
echo "🟢 Generating UML diagrams..."

docker run --rm \
    -v "$(pwd)/docs:/data" \
    --entrypoint sh \
    "$IMAGE" \
    -c "
        MMDC=$MMDC
        find /data/uml -mindepth 1 -delete 2>/dev/null || true
        for f in \$(find /data/diagrams -name '*.mmd'); do
            rel=\"\${f#/data/diagrams/}\"
            dir=\$(dirname \"\$rel\")
            name=\$(basename \"\$f\" .mmd)
            mkdir -p \"/data/uml/\$dir\"
            echo \"  → \$dir/\$name\"
            \$MMDC -p /puppeteer-config.json -i \"\$f\" -o \"/data/uml/\$dir/\${name}.svg\"
        done
    "

echo "✅ UML diagrams generated in $OUTPUT_DIR/"
