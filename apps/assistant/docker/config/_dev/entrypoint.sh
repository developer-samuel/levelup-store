#!/bin/sh
set -e

if [ ! -d node_modules ]; then
    pnpm install
fi

exec "$@"
