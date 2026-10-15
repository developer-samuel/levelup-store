# Assistant (FastAPI/Python)

## Dev

```bash
# Install Python dependencies
make assistant-install

# Start dev server (uvicorn, port 8001)
make assistant-run

# Ingest products into ChromaDB
make assistant-ingest
```

## Quality checks

```bash
# Run all checks (lint + format + type-check + dead-code)
make assistant-check

# Lint (ruff)
make assistant-lint

# Format (ruff)
make assistant-format

# Type check (mypy)
make assistant-type-check

# Dead code (vulture)
make assistant-dead-code
```

## Docker

```bash
# Build assistant Docker image
make assistant-docker-build

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

# Verify production image structure (run after assistant-build-prod)
make assistant-test-prod
```
