#!/bin/bash
set -euo pipefail

# ────────────── Load Config ──────────────
source scripts/tools/deptrac/deptrac.config.sh

# ────────────── Load Functions ──────────────
source scripts/tools/common/bootstrap.sh
source scripts/tools/deptrac/functions/deptrac.sh

# ────────────── Execute ──────────────
bootstrap
run_deptrac
