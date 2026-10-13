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
ollama pull mistral:7b-instruct
ollama pull nomic-embed-text
```

- PostgreSQL and ChromaDB running (via docker-compose)

## Quick start

```bash
cp .env.example .env
make assistant-install
make assistant-run
```

API available at `http://localhost:8001`.  
Swagger docs at `http://localhost:8001/docs`.

## RAG setup

Products must be ingested into ChromaDB before the assistant can answer product questions:

```bash
make assistant-ingest
```

Re-run after product catalog changes to keep the vector store up to date.

## Environment variables

| Variable                       | Default                  | Required | Description                                            |
|--------------------------------|--------------------------|----------|--------------------------------------------------------|
| `ASSISTANT_TYPE`               | `ollama`                 |          | Backend type: `ollama` or `api`                        |
| `ASSISTANT_API_KEY`            |                          |          | API key for cloud LLM (used when `ASSISTANT_TYPE=api`) |
| `ASSISTANT_MODEL`              | `gemini-3.5-flash-lite`  |          | Primary cloud model                                    |
| `ASSISTANT_FALLBACK_MODEL`     | `gemini-3.1-flash-lite`  |          | Fallback cloud model                                   |
| `ASSISTANT_PRIMARY_LIMIT`      | `20`                     |          | Primary model request limit per window                 |
| `ASSISTANT_FALLBACK_LIMIT`     | `30`                     |          | Fallback model request limit per window                |
| `ASSISTANT_OLLAMA_HOST`        | `http://localhost:11434` |          | Ollama service URL                                     |
| `ASSISTANT_OLLAMA_MODEL`       | `mistral:7b-instruct`    |          | LLM used for chat responses (Ollama)                   |
| `ASSISTANT_OLLAMA_EMBED_MODEL` | `nomic-embed-text`       |          | Model used for generating embeddings                   |
| `ASSISTANT_OLLAMA_LIMIT`       | `50`                     |          | Request limit per window (Ollama)                      |
| `ASSISTANT_ACCESS_KEY`         |                          |          | Access key for chat endpoints (leave empty to disable) |
| `ECOMMERCE_URL`                |                          | yes      | Ecommerce store URL (used to generate product links)   |
| `CHROMA_HOST`                  | `http://localhost:8010`  |          | ChromaDB service URL                                   |
| `CORS_ORIGINS`                 |                          | yes      | Allowed CORS origins (regex pattern)                   |
| `DATABASE_URL`                 |                          | yes      | PostgreSQL connection URL                              |
| `REDIS_URL`                    |                          | yes      | Redis connection URL                                   |
| `CONVERSATION_TTL_GUEST`       | `86400`                  |          | Conversation history TTL for guests (seconds)          |
| `CONVERSATION_TTL_USER`        | `2592000`                |          | Conversation history TTL for logged-in users (seconds) |
| `MINIO_ENABLED`                | `false`                  |          | Serve product images from MinIO                        |
| `MINIO_PUBLIC_URL`             |                          |          | MinIO public URL (used when `MINIO_ENABLED=true`)      |
| `MINIO_BUCKET`                 |                          |          | MinIO bucket name                                      |
| `SUPPORT_EMAIL`                |                          |          | Support email shown in chat escalation responses       |
| `SENTRY_DSN`                   |                          |          | Sentry DSN for error tracking (leave empty to disable) |

## Commands

| Command                           | Description                                                  |
|-----------------------------------|--------------------------------------------------------------|
| `make assistant-install`          | Install dependencies via uv                                  |
| `make assistant-run`              | Start dev server with hot reload                             |
| `make assistant-ingest`           | Ingest products from PostgreSQL into ChromaDB                |
| `make assistant-lint`             | Run ruff linter                                              |
| `make assistant-format`           | Run ruff formatter                                           |
| `make assistant-type-check`       | Run mypy type checker                                        |
| `make assistant-dead-code`        | Run vulture dead code detector                               |
| `make assistant-check`            | Run all quality checks (lint, format, type-check, dead-code) |
| `make assistant-docker-build`     | Build Docker image                                           |
| `make assistant-docker-run`       | Run Docker container                                         |
| `make assistant-docker-build-run` | Build and run Docker container                               |
| `make assistant-docker-setup-run` | Install deps, build image, run container, ingest products    |

## Endpoints

| Method   | Path                      | Auth        | Description                                        |
|----------|---------------------------|-------------|----------------------------------------------------|
| `POST`   | `/chat`                   | `X-Api-Key` | Send message, receive SSE stream                   |
| `DELETE` | `/chat/{conversation_id}` | `X-Api-Key` | Delete conversation history                        |
| `GET`    | `/health`                 |             | Health check (Ollama, Redis, ChromaDB, PostgreSQL) |
| `GET`    | `/info`                   |             | Returns active model name                          |

## Authentication

When `ASSISTANT_ACCESS_KEY` is set, all chat endpoints require the `X-Api-Key` header:

```bash
curl -X POST http://localhost:8001/chat \
  -H "Content-Type: application/json" \
  -H "X-Api-Key: your-access-key" \
  -d '{"message": "What laptops do you have?"}'
```

Generate a key with:

```bash
openssl rand -hex 32
```

## Rate limiting

Chat endpoint is limited to **50 requests per 5 hours** per IP. Exceeding the limit returns:

```json
{"success": false, "message": "Rate limit exceeded. Please try again in 4 hours 53 minutes."}
```

## Conversation history

Pass `conversation_id` from a previous response to continue a conversation. History is stored in Redis with configurable TTL (1 day for guests, 30 days for logged-in users):

```json
{"message": "Which one has the best reviews?", "conversation_id": "abc123..."}
```
