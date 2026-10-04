#!/bin/bash
# Detects which apps changed compared to previous commit
# Sets ECOMMERCE_CHANGED and ASSISTANT_CHANGED in jenkins.env
set -e

BASE=${BASE_REF:-HEAD~1}

ECOMMERCE_CHANGED=false
ASSISTANT_CHANGED=false

echo "Detecting changes against ${BASE}..."

CHANGED_FILES=$(git diff --name-only "${BASE}..HEAD" 2>/dev/null || git diff --name-only HEAD~1..HEAD)

echo "Changed files:"
echo "${CHANGED_FILES}"

if echo "${CHANGED_FILES}" | grep -E "^apps/ecommerce/" | grep -vE "(docs/|scripts/|tests/|\.md$|\.mmd$)" | grep -q .; then
    ECOMMERCE_CHANGED=true
fi

if echo "${CHANGED_FILES}" | grep -E "^apps/assistant/" | grep -vE "(docs/|\.md$|\.mmd$)" | grep -q .; then
    ASSISTANT_CHANGED=true
fi

echo "ECOMMERCE_CHANGED=${ECOMMERCE_CHANGED}"
echo "ASSISTANT_CHANGED=${ASSISTANT_CHANGED}"

echo "ECOMMERCE_CHANGED=${ECOMMERCE_CHANGED}" > jenkins.env
echo "ASSISTANT_CHANGED=${ASSISTANT_CHANGED}" >> jenkins.env
