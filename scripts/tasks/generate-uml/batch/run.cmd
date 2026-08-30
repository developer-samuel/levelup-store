@echo off

REM ================================
REM Generate UML diagrams from .mmd
REM ================================

set OUTPUT_DIR=.uml
set IMAGE=ghcr.io/mermaid-js/mermaid-cli/mermaid-cli:latest
set MMDC=/home/mermaidcli/node_modules/.bin/mmdc

REM ────────────── Setup ──────────────
IF EXIST "%OUTPUT_DIR%" (
    echo Clearing %OUTPUT_DIR%...
    rmdir /S /Q "%OUTPUT_DIR%"
)
echo Creating %OUTPUT_DIR%...
mkdir "%OUTPUT_DIR%"

REM ────────────── Generate ──────────────
echo Generating UML diagrams...

docker run --rm ^
    -v "%CD%\docs:/data" ^
    --entrypoint sh ^
    %IMAGE% ^
    -c "MMDC=%MMDC%; for f in $(find /data/diagrams -name '*.mmd'); do rel=\"${f#/data/diagrams/}\"; dir=$(dirname \"$rel\"); name=$(basename \"$f\" .mmd); mkdir -p \"/data/uml/$dir\"; echo \"  -> $dir/$name\"; $MMDC -p /puppeteer-config.json -i \"$f\" -o \"/data/uml/$dir/${name}.svg\"; done"

echo Done! UML diagrams generated in %OUTPUT_DIR%\
