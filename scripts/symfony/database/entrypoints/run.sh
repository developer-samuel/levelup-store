#!/bin/bash

# ================================
# Check if the database exists
# ================================
echo "Checking if database exists..."

php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    echo "Database exists, dropping all tables..."
    scripts/symfony/database/operations/drop_tables.sh
else
    echo "Database does not exist, creating..."
    scripts/symfony/database/operations/create_database.sh
fi

# ================================
# Run migrations
# ================================
scripts/symfony/database/operations/run_migrations.sh

# ================================
# Load fixtures
# ================================
scripts/symfony/database/operations/load_fixtures.sh

echo "Done! Database is ready."
