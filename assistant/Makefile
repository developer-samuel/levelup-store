.PHONY: install run ingest \
        lint format type-check dead-code check \
        docker-build docker-run docker-build-run docker-setup-run

# ── Dev ────────────────────────────────────────────────────────────────────────

install:
	python3 -m venv venv
	venv/bin/pip install -r requirements.txt

run:
	venv/bin/uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload

ingest:
	venv/bin/python -m jobs.ingest

# ── Quality ────────────────────────────────────────────────────────────────────

lint:
	venv/bin/ruff check --cache-dir .cache/ruff app/ && echo "lint: OK"

format:
	venv/bin/ruff format --cache-dir .cache/ruff app/ && echo "format: OK"

type-check:
	venv/bin/mypy --explicit-package-bases --cache-dir .cache/mypy app/ && echo "type-check: OK"

dead-code:
	venv/bin/vulture app/ && echo "dead-code: OK"

check: lint format type-check dead-code

# ── Docker ─────────────────────────────────────────────────────────────────────

docker-build:
	docker build -t levelup-store-assistant .

docker-run:
	docker ps -q --filter "publish=8001" | xargs -r docker stop
	docker run --name levelup-store-assistant --rm -p 8001:8001 --env-file .env levelup-store-assistant

docker-build-run: docker-build docker-run

# First-time setup: build image, start container, ingest products into ChromaDB
docker-setup-run: install docker-build-run ingest
