.PHONY: install run lint format typecheck docker-build docker-run

# ── Dev ────────────────────────────────────────────────────────────────────────

install:
	python3 -m venv venv
	venv/bin/pip install -r requirements.txt

run:
	venv/bin/uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload

# ── Quality ────────────────────────────────────────────────────────────────────

lint:
	venv/bin/ruff check --cache-dir .cache/ruff app/

format:
	venv/bin/ruff format --cache-dir .cache/ruff app/

typecheck:
	venv/bin/mypy --explicit-package-bases --cache-dir .cache/mypy app/

# ── Docker ─────────────────────────────────────────────────────────────────────

docker-build:
	docker build -t levelup-store-chatbot .

docker-run:
	docker run -p 8001:8001 --env-file .env levelup-store-chatbot
