#!/bin/bash
set -euo pipefail

# ────────────── Config ──────────────
OUTPUT_DIR=".uml"
APPS_DIR="apps"
MMDC="./node_modules/.bin/mmdc"

# ────────────── Discover apps ──────────────
mapfile -t APPS < <(find "$APPS_DIR" -mindepth 1 -maxdepth 1 -type d | sort)

if [ ${#APPS[@]} -eq 0 ]; then
    echo "⚠️  No apps found in $APPS_DIR/"
    exit 0
fi

echo "🔍 Found apps: $(IFS=', '; echo "${APPS[*]##*/}")"

# ────────────── Generate ──────────────
echo "🟢 Generating UML diagrams..."

generate_for_app() {
    local app_path="$1"
    local app_name
    app_name=$(basename "$app_path")
    local diagrams_dir="$app_path/docs/diagrams"

    if [ ! -d "$diagrams_dir" ]; then
        echo "  ⚠️  $app_name: no docs/diagrams/ - skipping"
        return
    fi

    mapfile -t MMD_FILES < <(find "$diagrams_dir" -name "*.mmd" | sort)

    if [ ${#MMD_FILES[@]} -eq 0 ]; then
        echo "  ⚠️  $app_name: docs/diagrams/ exists but no .mmd files - skipping"
        return
    fi

    echo "  📂 $app_name (${#MMD_FILES[@]} diagrams)"

    for f in "${MMD_FILES[@]}"; do
        # path relative to app's docs/diagrams/
        rel="${f#"$diagrams_dir/"}"
        dir=$(dirname "$rel")
        name=$(basename "$f" .mmd)

        local out_dir="$OUTPUT_DIR/$app_name"
        [ "$dir" != "." ] && out_dir="$OUTPUT_DIR/$app_name/$dir"
        mkdir -p "$out_dir"

        echo "    → $app_name/$([[ "$dir" != "." ]] && echo "$dir/")$name"

        if command -v docker &>/dev/null; then
            docker run --rm \
                -v "$(pwd)/$diagrams_dir:/data" \
                -v "$(pwd)/$OUTPUT_DIR/$app_name:/$OUTPUT_DIR/$app_name" \
                --user "$(id -u):$(id -g)" \
                --entrypoint sh \
                "ghcr.io/mermaid-js/mermaid-cli/mermaid-cli:latest" \
                -c "
                    rel_f=\"${rel}\"
                    dir_f=\$(dirname \"\$rel_f\")
                    name_f=\$(basename \"\$rel_f\" .mmd)
                    mkdir -p \"/$OUTPUT_DIR/$app_name/\$dir_f\"
                    /home/mermaidcli/node_modules/.bin/mmdc \
                        -p /puppeteer-config.json \
                        -i \"/data/\$rel_f\" \
                        -o \"/$OUTPUT_DIR/$app_name/\$dir_f/\${name_f}.png\" \
                        --scale 3 2>/dev/null
                " || true
        else
            "$MMDC" -i "$f" -o "$out_dir/${name}.png" --scale 3 2>/dev/null || true
        fi
    done
}

for app in "${APPS[@]}"; do
    generate_for_app "$app"
done

