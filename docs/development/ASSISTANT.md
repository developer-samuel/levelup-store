# Assistant (FastAPI/Python)

## Dev

```bash
# Install Python dependencies
make assistant-install
# or
cd apps/assistant && uv sync --group dev

# Start dev server (uvicorn, port 8001)
make assistant-run
# or
cd apps/assistant && uv run uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload

# Ingest products into ChromaDB
make assistant-ingest
# or
cd apps/assistant && uv run python -m jobs.ingest
```

## Quality checks

```bash
# Run all checks (lint + format + type-check + dead-code)
make assistant-check

# Lint (ruff)
make assistant-lint
# or
cd apps/assistant && uv run ruff check --cache-dir .cache/ruff app/

# Format (ruff)
make assistant-format
# or
cd apps/assistant && uv run ruff format --cache-dir .cache/ruff app/

# Type check (mypy)
make assistant-type-check
# or
cd apps/assistant && uv run mypy --explicit-package-bases --cache-dir .cache/mypy app/

# Dead code (vulture)
make assistant-dead-code
# or
cd apps/assistant && uv run vulture app/ jobs/
```

## Docker

```bash
# Build assistant Docker image
make assistant-docker-build
# or
docker build -t levelup-store-assistant apps/assistant

# Run assistant Docker container
make assistant-docker-run

# Build and run
make assistant-docker-build-run

# First-time setup: install + build + run + ingest
make assistant-docker-setup-run
```

## Production image

```bash
# Build assistant production image locally (smoke test before push to main)
make assistant-build-prod
# or
docker build -f docker/Dockerfile.prod --target assistant -t levelup-store-assistant:prod-test .

# Verify production image structure (run after assistant-build-prod)
make assistant-test-prod
# or
container-structure-test test --image levelup-store-assistant:prod-test --config apps/assistant/docker/container-structure-test.yml
```
