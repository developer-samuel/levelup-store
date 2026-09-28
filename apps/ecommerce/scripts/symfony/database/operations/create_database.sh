#!/bin/bash

# ================================
# Create the database if it doesn't exist
# ================================

echo "Creating database..."

php bin/console doctrine:database:create
