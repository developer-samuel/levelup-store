#!/bin/bash
set -e

if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ .env file created from .env.example"
else
    echo "ℹ️ .env file already exists, skipping generation"
    exit 0
fi

generate_secret() {
    openssl rand -hex 32
}

fill_if_empty() {
    local key="$1"
    local value="$2"
    if grep -q "^${key}=$" .env; then
        sed -i "s/^${key}=$/${key}=${value}/" .env
        echo "✅ ${key} generated"
    fi
}

fill_if_empty "APP_SECRET"             "$(generate_secret)"
fill_if_empty "HMAC_SECRET"            "$(generate_secret)"
fill_if_empty "JWT_PASSPHRASE"         "$(generate_secret)"
fill_if_empty "MERCURE_JWT_SECRET"     "$(generate_secret)"
fill_if_empty "RABBITMQ_ERLANG_COOKIE" "$(generate_secret)"
