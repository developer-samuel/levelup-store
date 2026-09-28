#!/bin/bash
set -euo pipefail

# ────────────── Load Config ──────────────
source scripts/tools/pdepend/pdepend.config.sh

# ────────────── Load Functions ──────────────
source scripts/tools/common/bootstrap.sh
source scripts/tools/pdepend/functions/pdepend.sh

# ────────────── Execute ──────────────
bootstrap
run_pdepend
