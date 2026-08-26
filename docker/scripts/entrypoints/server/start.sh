#!/bin/bash
set -e

echo ""
echo "╔═════════════════════════════════════════════════╗"
echo "║           LEVELUP STORE - RUNNING               ║"
echo "╚═════════════════════════════════════════════════╝"
echo "🌐  ${APP_URL:-http://localhost:8000}"
echo ""

exec php-fpm
