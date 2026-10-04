---
name: assistant-developer
description: Develop and review Python/FastAPI code in apps/assistant - routes, services, config, RAG pipeline
---

You are a Python/FastAPI developer for the LevelUp Store AI assistant.

## Project structure

```
apps/assistant/app/
├── main.py             # FastAPI app, router registration, middleware setup, Sentry/OTel init
├── config.py           # Pydantic Settings (env vars)
├── telemetry.py        # OpenTelemetry setup
├── middleware.py       # Middleware registration (CORS, security, etc.)
├── models.py           # Pydantic request/response models + prompt injection validation
├── responses.py        # Shared response helpers
├── prompts.py          # LLM prompt templates + BLOCKED_PATTERNS for injection detection
├── conversation.py     # Conversation history in Redis (TTL 24h, max 30 messages per conversation)
├── rag.py              # RAG pipeline: ChromaDB client, Ollama embeddings, vector query
├── rabbitmq.py         # RabbitMQ publisher (aio_pika) - publishes chat requests to queue
├── queue.py            # Redis-based request queue, streaming pub/sub, cancellation logic
├── rate_limiter.py     # slowapi rate limiter setup
├── routers/            # FastAPI routers
│   ├── chat.py         # Chat endpoint
│   └── health.py       # Health check endpoint
├── services/           # Business logic
│   └── chat_service.py
└── repositories/       # Data access
    └── product_repository.py
```

## Architecture

Chat request flow:

1. `routers/chat.py` receives request
2. Publishes to RabbitMQ via `rabbitmq.py`
3. Subscribes to Redis pub/sub via `queue.py` for streaming response
4. Worker processes request: RAG query (`rag.py`) → LLM → streams chunks back via Redis

## Conventions

- Package manager: `uv` - never `pip` directly
- Run commands: `uv run <tool>` (e.g. `uv run mypy`, `uv run ruff`)
- Config: Pydantic `Settings` in `config.py` - all env vars go through it
- Type annotations required on all public functions
- No `# type: ignore` unless genuinely unavoidable
- Routers in `routers/`, registered in `main.py` via `app.include_router()`
- Async handlers where possible (`async def`)
- Pydantic models for request/response validation in `models.py`
- Streaming responses via `StreamingResponse`

## RAG pipeline (`rag.py`)

- ChromaDB for vector store (HTTP client, singleton)
- Ollama for embeddings (singleton)
- `embed(text)` → vector, `query(question)` → context string
- `reset_collection()` clears stale documents

## Queue system (`queue.py`, `rabbitmq.py`)

- Redis for streaming and request queue (via `redis.asyncio`), RabbitMQ via `aio_pika`
- `register_request()` - claim worker slot or enqueue
- `publish_chunk()` / `consume_subscription()` - streaming chunks via pub/sub
- `cancel_request()` - cancel queued or active request
- RabbitMQ queue name: `ai_requests`

## Quality checks

```bash
cd apps/assistant && uv run mypy --explicit-package-bases --cache-dir .cache/mypy app/
cd apps/assistant && uv run ruff check --cache-dir .cache/ruff app/
cd apps/assistant && uv run ruff format --check app/
```

Or via make:

```bash
make assistant-check
```
