#!/bin/bash

# ================================
# Run database migrations
# ================================

echo "Running migrations..."

php bin/console doctrine:migrations:migrate --no-interaction
