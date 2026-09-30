#!/bin/bash
set -e

# ────────────── Clean old PDepend folder ──────────────
source scripts/tools/common/directory/clean.sh
clean_directory "var/tools/pdepend"
