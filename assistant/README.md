# LevelUp Store Assistant

FastAPI assistant service with Ollama + RAG (ChromaDB).

## Prerequisites

- Python 3.12 + venv

```bash
sudo apt install python3-pip python3.12-venv
```

- [Ollama](https://ollama.com) running locally with models pulled

```bash
sudo snap install ollama
ollama pull mistral:7b
ollama pull nomic-embed-text
```

- PostgreSQL and ChromaDB running (via docker-compose)

## Quick start

```bash
cp .env.example .env
# fill in DB_USERNAME, DB_PASSWORD, CORS_ORIGINS
make install
make run
```

API available at `http://localhost:8001`.  
Swagger docs at `http://localhost:8001/docs`.

## RAG setup

Products must be ingested into ChromaDB before the assistant can answer product questions:

```bash
make ingest
```

Re-run after product catalog changes to keep the vector store up to date.

## Environment variables

| Variable             | Default                  | Required | Description                            |
|----------------------|--------------------------|----------|----------------------------------------|
| `OLLAMA_HOST`        | `http://localhost:11434` |          | Ollama service URL                     |
| `OLLAMA_MODEL`       | `mistral:7b`             |          | LLM used for chat responses            |
| `OLLAMA_EMBED_MODEL` | `nomic-embed-text`       |          | Model used for generating embeddings   |
| `CHROMA_HOST`        | `http://localhost:8010`  |          | ChromaDB service URL                   |
| `CORS_ORIGINS`       |                          | yes      | Allowed CORS origins (comma-separated) |
| `DB_HOST`            | `localhost`              |          | PostgreSQL host                        |
| `DB_PORT`            | `5432`                   |          | PostgreSQL port                        |
| `DB_DATABASE`        |                          | yes      | PostgreSQL database name               |
| `DB_USERNAME`        |                          | yes      | PostgreSQL username                    |
| `DB_PASSWORD`        |                          | yes      | PostgreSQL password                    |

## Commands

| Command                 | Description                                   |
|-------------------------|-----------------------------------------------|
| `make install`          | Create venv and install dependencies          |
| `make run`              | Start dev server with hot reload              |
| `make ingest`           | Ingest products from PostgreSQL into ChromaDB |
| `make lint`             | Run ruff linter                               |
| `make format`           | Run ruff formatter                            |
| `make type-check`       | Run mypy type checker                         |
| `make dead-code`        | Run vulture dead code detector                |
| `make check`            | Run all quality checks (lint, format, type-check, dead-code) |
| `make docker-build`     | Build Docker image                            |
| `make docker-run`       | Run Docker container                          |
| `make docker-build-run` | Build and run Docker container                |
| `make docker-setup-run` | Install deps, build image, run container, ingest products |

## Endpoints

| Method | Path      | Description                      |
|--------|-----------|----------------------------------|
| `POST` | `/chat`   | Send message, receive SSE stream |
| `GET`  | `/health` | Health check                     |
