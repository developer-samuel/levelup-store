#!/bin/bash
set -e

/usr/local/bin/scripts/bootstrap/permissions.sh
/usr/local/bin/scripts/bootstrap/check-services.sh

if [ "$1" = "setup" ]; then
    exec /usr/local/bin/scripts/entrypoints/setup.sh
fi

/usr/local/bin/scripts/entrypoints/server/start.sh
