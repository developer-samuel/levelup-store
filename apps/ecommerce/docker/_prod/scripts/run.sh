#!/bin/bash
set -e

echo ""
echo "╔═════════════════════════════════════════════════╗"
echo "║        LEVELUP STORE - PRODUCTION START         ║"
echo "╚═════════════════════════════════════════════════╝"

# Wait for dependent services before accepting traffic
/usr/local/bin/scripts/bootstrap/check-services.sh

# Start PHP-FPM in the background (Docker image default: daemonize = no, no pid file)
echo "🚀 Starting PHP-FPM (127.0.0.1:9000)..."
php-fpm &

# Brief pause to let PHP-FPM bind its socket before nginx starts forwarding
sleep 1

# Start nginx in foreground - K8s monitors this process for liveness/readiness
echo "🌐 Starting nginx (:8000)..."
exec nginx -g "daemon off;"
