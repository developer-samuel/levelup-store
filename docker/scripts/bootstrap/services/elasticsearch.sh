#!/bin/bash
set -e

echo "📡 Checking Elasticsearch availability..."

until curl -s "http://${ELASTICSEARCH_HOST:-elasticsearch}:${ELASTICSEARCH_PORT:-9200}/_cluster/health" | grep -q '"status":"green"\|"status":"yellow"'; do
    echo "⏳ Elasticsearch not ready yet, waiting..."
    sleep 3
done

echo "✅ Elasticsearch is ready!"
