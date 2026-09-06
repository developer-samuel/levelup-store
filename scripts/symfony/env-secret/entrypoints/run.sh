#!/bin/bash
set -e

# ====================================================
# Generate APP_SECRET if not exists or empty
# ====================================================

APP_SECRET_VAL=$(grep '^APP_SECRET=' .env | cut -d '=' -f2-)
if [ -n "$APP_SECRET_VAL" ]; then
    echo "APP_SECRET already exists, skipping generation"
else
    APP_SECRET=$(php -r "echo bin2hex(random_bytes(32));")
    sed -i "s/^APP_SECRET=$/APP_SECRET=$APP_SECRET/" .env
    echo "APP_SECRET generated and added to .env"
fi

# ====================================================
# Generate HMAC_SECRET if not exists or empty
# ====================================================

HMAC_SECRET_VAL=$(grep '^HMAC_SECRET=' .env | cut -d '=' -f2-)
if [ -n "$HMAC_SECRET_VAL" ]; then
    echo "HMAC_SECRET already exists, skipping generation"
else
    HMAC_SECRET=$(php -r "echo bin2hex(random_bytes(32));")
    sed -i "s/^HMAC_SECRET=$/HMAC_SECRET=$HMAC_SECRET/" .env
    echo "HMAC_SECRET generated and added to .env"
fi

# ====================================================
# Generate JWT_PASSPHRASE if not exists or empty
# ====================================================

JWT_PASSPHRASE_VAL=$(grep '^JWT_PASSPHRASE=' .env | cut -d '=' -f2-)
if [ -n "$JWT_PASSPHRASE_VAL" ]; then
    echo "JWT_PASSPHRASE already exists, skipping generation"
else
    JWT_PASSPHRASE=$(php -r "echo bin2hex(random_bytes(32));")
    sed -i "s/^JWT_PASSPHRASE=$/JWT_PASSPHRASE=$JWT_PASSPHRASE/" .env
    echo "JWT_PASSPHRASE generated and added to .env"
fi

# ====================================================
# Generate MERCURE_JWT_SECRET if not exists or empty
# ====================================================

MERCURE_JWT_SECRET_VAL=$(grep '^MERCURE_JWT_SECRET=' .env | cut -d '=' -f2-)
if [ -n "$MERCURE_JWT_SECRET_VAL" ]; then
    echo "MERCURE_JWT_SECRET already exists, skipping generation"
else
    MERCURE_JWT_SECRET=$(openssl rand -hex 32)
    sed -i "s/^MERCURE_JWT_SECRET=$/MERCURE_JWT_SECRET=$MERCURE_JWT_SECRET/" .env
    echo "MERCURE_JWT_SECRET generated and added to .env"
fi
