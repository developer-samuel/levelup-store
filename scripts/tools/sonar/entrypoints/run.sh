#!/bin/bash
set -euo pipefail

# ────────────── Load Config ──────────────
source scripts/tools/sonar/sonar.config.sh

# ────────────── Load Functions ──────────────
source scripts/tools/sonar/functions/sonar.sh

# ────────────── ESLint Report ──────────────
mkdir -p var/tools/eslint
echo "📋 Generating ESLint report..."
pnpm run lint:report

# ────────────── Execute ──────────────
run_sonar
