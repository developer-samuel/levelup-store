#!/usr/bin/env bash
set -euo pipefail

/usr/local/bin/scripts/setup.sh

if [ $# -gt 0 ]; then
    exec "$@"
else
    exec uvicorn app.main:app --host 0.0.0.0 --port 8001
fi
