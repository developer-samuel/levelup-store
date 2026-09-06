#!/bin/bash
set -e

# Ensure .env exists
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ .env file created from .env.example"
else
    echo "ℹ️ .env file already exists, skipping creation"
fi
