#!/bin/bash
set -e

# ================================
# Generate JWT PEM keys
# ================================

JWT_DIR="config/jwt"

if [ ! -f ".env" ]; then
    echo "ERROR: .env file not found. Run env-generate and env-secret first."
    exit 1
fi

JWT_PASSPHRASE=$(grep '^JWT_PASSPHRASE=' .env | cut -d '=' -f2-)

if [ -z "$JWT_PASSPHRASE" ]; then
    echo "ERROR: JWT_PASSPHRASE not found in .env. Run env-secret first."
    exit 1
fi

mkdir -p "$JWT_DIR"

rm -f "$JWT_DIR/private.pem" "$JWT_DIR/public.pem"

openssl genpkey -algorithm RSA \
    -out "$JWT_DIR/private.pem" \
    -aes256 \
    -pass pass:"$JWT_PASSPHRASE" \
    -pkeyopt rsa_keygen_bits:4096

openssl pkey \
    -in "$JWT_DIR/private.pem" \
    -out "$JWT_DIR/public.pem" \
    -pubout \
    -passin pass:"$JWT_PASSPHRASE"

echo "JWT private.pem and public.pem generated in $JWT_DIR"
