#!/bin/bash
set -e

# Check if .env exists and if APP_SECRET is set and not empty
if [ -f .env ]; then
  CURRENT_SECRET=$(grep '^APP_SECRET=' .env | cut -d '=' -f 2-)
else
  CURRENT_SECRET=""
fi

if [ -z "$CURRENT_SECRET" ]; then
  # Generate a 64-character hexadecimal APP_SECRET using PHP's random_bytes function
  APP_SECRET=$(php -r "echo bin2hex(random_bytes(32));")

  if grep -q '^APP_SECRET=' .env 2>/dev/null; then
    sed -i "s/^APP_SECRET=.*/APP_SECRET=$APP_SECRET/" .env
  else
    echo "APP_SECRET=$APP_SECRET" >> .env
  fi

  echo "✅ APP_SECRET generated and set in .env"
else
  echo "ℹ️ APP_SECRET already set, skipping generation"
fi