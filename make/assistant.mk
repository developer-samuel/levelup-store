# ──────────────────────────────────────────────────────────────────────────────
# 🤖 Assistant Commands (FastAPI/Python)
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: assistant-install assistant-run assistant-ingest \
        assistant-lint assistant-format assistant-type-check assistant-dead-code assistant-check \
        assistant-docker-build assistant-docker-run assistant-docker-build-run assistant-docker-setup-run \
        assistant-build-prod assistant-test-prod

# ── 💻 Dev ────────────────────────────────────────────────────────────────────

## Install assistant Python dependencies
assistant-install:
	cd apps/assistant && uv sync --group dev

## Start assistant dev server (uvicorn)
assistant-run:
	cd apps/assistant && uv run uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload

## Run assistant product ingest into ChromaDB
assistant-ingest:
	cd apps/assistant && uv run python -m jobs.ingest

# ── 🔍 Quality ────────────────────────────────────────────────────────────────

## Lint assistant Python code (ruff)
assistant-lint:
	cd apps/assistant && uv run ruff check --cache-dir .cache/ruff app/ && echo "lint: OK"

## Format assistant Python code (ruff)
assistant-format:
	cd apps/assistant && uv run ruff format --cache-dir .cache/ruff app/ && echo "format: OK"

## Type check assistant Python code (mypy)
assistant-type-check:
	cd apps/assistant && uv run mypy --explicit-package-bases --cache-dir .cache/mypy app/ && echo "type-check: OK"

## Check for dead code in assistant (vulture)
assistant-dead-code:
	cd apps/assistant && uv run vulture app/ jobs/ && echo "dead-code: OK"

## Run all assistant quality checks
assistant-check: assistant-lint assistant-format assistant-type-check assistant-dead-code

# ── 🐳 Docker ─────────────────────────────────────────────────────────────────

## Build assistant Docker image
assistant-docker-build:
	docker build -t levelup-store-assistant apps/assistant

## Run assistant Docker container (stops any existing on port 8001)
assistant-docker-run:
	docker ps -q --filter "publish=8001" | xargs -r docker stop
	docker run --name levelup-store-assistant --rm -p 8001:8001 --env-file apps/assistant/.env levelup-store-assistant

## Build and run assistant Docker container
assistant-docker-build-run: assistant-docker-build assistant-docker-run

## First-time setup: install, build image, start container, ingest products into ChromaDB
assistant-docker-setup-run: assistant-install assistant-docker-build-run assistant-ingest

# ── 🚀 Production ─────────────────────────────────────────────────────────────

## Build assistant production image locally (smoke test before push to main)
assistant-build-prod:
	docker build -f apps/assistant/docker/Dockerfile -t levelup-store-assistant:prod-test apps/assistant

## Verify assistant production image structure (run after assistant-build-prod)
assistant-test-prod:
	container-structure-test test --image levelup-store-assistant:prod-test --config apps/assistant/docker/container-structure-test.yml
