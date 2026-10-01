# LevelUp Store Assistant

FastAPI assistant service with Ollama + RAG (ChromaDB).

## Prerequisites

- Python 3.12
- [uv](https://docs.astral.sh/uv/getting-started/installation/)

```bash
curl -LsSf https://astral.sh/uv/install.sh | sh
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

| Variable             | Default                  | Required | Description                                            |
|----------------------|--------------------------|----------|--------------------------------------------------------|
| `OLLAMA_HOST`        | `http://localhost:11434` |          | Ollama service URL                                     |
| `OLLAMA_MODEL`       | `mistral:7b`             |          | LLM used for chat responses                            |
| `OLLAMA_EMBED_MODEL` | `nomic-embed-text`       |          | Model used for generating embeddings                   |
| `CHROMA_HOST`        | `http://localhost:8010`  |          | ChromaDB service URL                                   |
| `CORS_ORIGINS`       |                          | yes      | Allowed CORS origins (comma-separated)                 |
| `DB_HOST`            | `localhost`              |          | PostgreSQL host                                        |
| `DB_PORT`            | `5432`                   |          | PostgreSQL port                                        |
| `DB_DATABASE`        |                          | yes      | PostgreSQL database name                               |
| `DB_USERNAME`        |                          | yes      | PostgreSQL username                                    |
| `DB_PASSWORD`        |                          | yes      | PostgreSQL password                                    |
| `REDIS_URL`          |                          | yes      | Redis connection URL                                   |
| `SENTRY_DSN`         |                          |          | Sentry DSN for error tracking (leave empty to disable) |
| `API_KEY`            |                          |          | API key for chat endpoint (leave empty to disable)     |

## Commands

| Command                 | Description                                                  |
|-------------------------|--------------------------------------------------------------|
| `make install`          | Install dependencies via uv                                  |
| `make run`              | Start dev server with hot reload                             |
| `make ingest`           | Ingest products from PostgreSQL into ChromaDB                |
| `make lint`             | Run ruff linter                                              |
| `make format`           | Run ruff formatter                                           |
| `make type-check`       | Run mypy type checker                                        |
| `make dead-code`        | Run vulture dead code detector                               |
| `make check`            | Run all quality checks (lint, format, type-check, dead-code) |
| `make docker-build`     | Build Docker image                                           |
| `make docker-run`       | Run Docker container                                         |
| `make docker-build-run` | Build and run Docker container                               |
| `make docker-setup-run` | Install deps, build image, run container, ingest products    |

## Endpoints

| Method   | Path                      | Auth        | Description                                        |
|----------|---------------------------|-------------|----------------------------------------------------|
| `POST`   | `/chat`                   | `X-Api-Key` | Send message, receive SSE stream                   |
| `DELETE` | `/chat/{conversation_id}` | `X-Api-Key` | Delete conversation history                        |
| `GET`    | `/health`                 |             | Health check (Ollama, Redis, ChromaDB, PostgreSQL) |

## Authentication

When `API_KEY` is set, all chat endpoints require the `X-Api-Key` header:

```bash
curl -X POST http://localhost:8001/chat \
  -H "Content-Type: application/json" \
  -H "X-Api-Key: your-api-key" \
  -d '{"message": "What laptops do you have?"}'
```

Generate a key with:

```bash
openssl rand -hex 32
```

## Rate limiting

Chat endpoint is limited to **100 requests per 5 hours** per IP. Exceeding the limit returns:

```json
{"success": false, "message": "Rate limit exceeded. Please try again in 4 hours 53 minutes."}
```

## Conversation history

Pass `conversation_id` from a previous response to continue a conversation (stored in Redis, 24h TTL):

```json
{"message": "Which one has the best reviews?", "conversation_id": "abc123..."}
```
