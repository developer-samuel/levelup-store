#!/bin/bash
set -euo pipefail

# ────────────── Load Config ──────────────
source scripts/tools/sonar/bash/sonar.config.sh

# ────────────── Load Functions ──────────────
source scripts/tools/sonar/bash/functions/sonar.sh

# ────────────── ESLint Report ──────────────
mkdir -p var/tools/eslint
echo "📋 Generating ESLint report..."
pnpm run lint:report

# ────────────── Execute ──────────────
run_sonar
