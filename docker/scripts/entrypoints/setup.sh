#!/bin/bash
set -e

TOTAL=7
source /usr/local/bin/scripts/helpers/step.sh

echo ""
echo "╔═════════════════════════════════════════════════╗"
echo "║           LEVELUP STORE - APP SETUP             ║"
echo "╚═════════════════════════════════════════════════╝"

step "Setting permissions..."
/usr/local/bin/scripts/bootstrap/permissions.sh

step "Checking Composer..."
/usr/local/bin/scripts/bootstrap/check-composer.sh

step "Preparing environment file (.env.example -> .env)"
/usr/local/bin/scripts/bootstrap/prepare-env.sh

step "Clearing caches and optimizing configuration..."
/usr/local/bin/scripts/bootstrap/optimize.sh

step "Clearing Redis cache..."
/usr/local/bin/scripts/entrypoints/app/clear-cache.sh

step "Running migrations and seeding database..."
/usr/local/bin/scripts/entrypoints/app/db-setup.sh

step "Setting up uploads..."
/usr/local/bin/scripts/bootstrap/uploads-setup.sh

echo ""
echo "╔═════════════════════════════════════════════════╗"
echo "║           LEVELUP STORE - SETUP DONE            ║"
echo "╚═════════════════════════════════════════════════╝"
echo "🌐  ${APP_URL:-http://localhost:8000}"
echo ""
