#!/bin/bash
set -e

echo "📦 Installing Composer dependencies..."
rm -rf vendor/*
composer install --no-interaction --prefer-dist --optimize-autoloader