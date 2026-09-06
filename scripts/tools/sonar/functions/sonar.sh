#!/bin/bash
set -euo pipefail

run_sonar() {
    echo "🔍 Running SonarQube analysis..."

    local token_arg=""
    if [ -n "$SONAR_TOKEN" ]; then
        token_arg="-e SONAR_TOKEN=${SONAR_TOKEN}"
    fi

    docker run --rm \
        --network "$SONAR_NETWORK" \
        -e SONAR_HOST_URL="$SONAR_HOST_INTERNAL" \
        $token_arg \
        -v "${PWD}:/usr/src" \
        "$SONAR_SCANNER_IMAGE" \
        -Dproject.settings="$SONAR_PROPERTIES_FILE" \
        -Dsonar.projectKey="$SONAR_PROJECT_KEY" \
        -Dsonar.host.url="$SONAR_HOST_INTERNAL"

    echo "✅ Analysis complete → ${SONAR_HOST}/dashboard?id=${SONAR_PROJECT_KEY}"
}
