#!/bin/bash

# ────────────── Load .env ──────────────
if [ -f ".env" ]; then
    set -o allexport
    source .env
    set +o allexport
fi

# ────────────── Config ──────────────
SONAR_HOST="${SONAR_HOST:-http://localhost:9100}"
SONAR_HOST_INTERNAL="${SONAR_HOST_INTERNAL:-http://sonarqube:9000}"
SONAR_TOKEN="${SONAR_TOKEN:-}"
SONAR_PROJECT_KEY="${SONAR_PROJECT_KEY:-levelup-store}"
SONAR_PROPERTIES_FILE="sonar-project.properties"
SONAR_SCANNER_IMAGE="sonarsource/sonar-scanner-cli:latest"
SONAR_NETWORK="levelup-store_app-network"
