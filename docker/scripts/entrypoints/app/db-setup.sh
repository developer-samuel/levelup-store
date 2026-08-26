#!/bin/bash
set -e

composer db-setup

if [ "${ELASTICSEARCH_ENABLED}" = "true" ]; then
  echo "🔍 Reindexing Elasticsearch..."
  composer elasticsearch:reindex
  echo "✅ Elasticsearch reindexed."
else
  echo "⏭️ Elasticsearch disabled, skipping reindex."
fi

echo "✅ Done! Database is ready."
