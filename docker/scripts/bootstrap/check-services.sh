#!/bin/bash
set -e

bash "$(dirname "$0")/services/database.sh"
bash "$(dirname "$0")/services/redis.sh"
bash "$(dirname "$0")/services/elasticsearch.sh"
bash "$(dirname "$0")/services/rabbitmq.sh"

echo "✅ All required services are ready."
