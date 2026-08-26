#!/bin/sh
set -e

cat > /tmp/servers.json << EOF
{
  "Servers": {
    "1": {
      "Name": "LevelUp Store (PostgreSQL ${SERVER_VERSION})",
      "Group": "Docker",
      "Host": "${DB_HOST}",
      "Port": ${DB_PORT},
      "MaintenanceDB": "${DB_DATABASE}",
      "Username": "${DB_USERNAME}",
      "Password": "${DB_PASSWORD}",
      "SSLMode": "prefer"
    }
  }
}
EOF

export PGADMIN_SERVER_JSON_FILE=/tmp/servers.json

exec /entrypoint.sh "$@"
