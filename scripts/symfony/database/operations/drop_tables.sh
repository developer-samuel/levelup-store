#!/bin/bash

# ================================
# Drop all tables from the database
# ================================

echo "Dropping all tables..."

php bin/console doctrine:schema:drop --force --full-database
