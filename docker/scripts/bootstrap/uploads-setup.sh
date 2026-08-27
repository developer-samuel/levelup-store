#!/bin/bash
set -e

UPLOADS_DIR="/var/www/public/uploads"
UPLOADS_REPO="https://github.com/Developer-Samuel/levelup-store-uploads"

echo "  ⬇  Cloning uploads repo..."
TMP_DIR=$(mktemp -d)
git clone --depth=1 --quiet "$UPLOADS_REPO" "$TMP_DIR"
echo "  ✅  Clone done"

if [ "${MINIO_ENABLED}" = "true" ]; then
    echo "  ☁️  Uploading to MinIO bucket: ${MINIO_BUCKET}..."
    mc alias set local "${MINIO_ENDPOINT}" "${MINIO_ROOT_USER}" "${MINIO_ROOT_PASSWORD}" --quiet
    mc mb --ignore-existing "local/${MINIO_BUCKET}" --quiet
    mc anonymous set public "local/${MINIO_BUCKET}"
    mc rm --recursive --force "local/${MINIO_BUCKET}/uploads/" --quiet 2>/dev/null || true
    mc cp --recursive "$TMP_DIR/uploads/" "local/${MINIO_BUCKET}/uploads/" --quiet
    echo "  ✅  Uploads ready in MinIO"
else
    FILE_COUNT=$(find "$UPLOADS_DIR" -type f ! -name ".gitkeep" 2>/dev/null | wc -l)
    if [ "$FILE_COUNT" -gt 0 ]; then
        echo "  ⏭  Local uploads already exist (${FILE_COUNT} files) — skipping"
        rm -rf "$TMP_DIR"
        exit 0
    fi

    mkdir -p "$UPLOADS_DIR"
    cp -r "$TMP_DIR/uploads/." "$UPLOADS_DIR/"
    echo "  ✅  Local uploads ready"
fi

rm -rf "$TMP_DIR"
