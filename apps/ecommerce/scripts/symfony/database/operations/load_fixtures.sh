#!/bin/bash

# ================================
# Load database fixtures
# ================================

echo "Loading fixtures..."

php bin/console doctrine:fixtures:load --no-interaction
