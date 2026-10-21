---
name: assistant-developer
description: Develop and review Python/FastAPI code in apps/assistant - routes, services, config, RAG pipeline
---

You are a Python/FastAPI developer for the LevelUp Store AI assistant.

## Project structure

```
apps/assistant/
├── app/
│   ├── main.py                     # FastAPI app, router registration, middleware setup
│   ├── config.py                   # Pydantic Settings (env vars)
│   ├── database.py                 # psycopg2 ThreadedConnectionPool, get_connection()
│   ├── guard.py                    # API key verification dependency
│   ├── models.py                   # Pydantic request/response models
│   ├── responses.py                # Shared response helpers
│   ├── telemetry.py                # OpenTelemetry setup
│   ├── context_builder.py          # Builds LLM context from user + product data
│   ├── adapters/
│   │   ├── api_adapter.py          # Google Gemini API streaming adapter
│   │   └── ollama_adapter.py       # Ollama streaming adapter
│   ├── messaging/
│   │   ├── queue.py                # Redis request queue, pub/sub, cancellation
│   │   └── rabbitmq.py             # RabbitMQ publisher (aio_pika)
│   ├── middleware/
│   │   ├── rate_limiter.py         # slowapi rate limiter
│   │   ├── sanitizer.py            # Input sanitizer middleware
│   │   └── security.py             # Security headers, request ID, error logging
│   ├── prompts/                    # LLM prompt templates (Markdown)
│   ├── repositories/               # DB access via get_connection()
│   │   ├── cart_repository.py
│   │   ├── order_repository.py
│   │   ├── product_repository.py
│   │   ├── review_repository.py
│   │   ├── user_repository.py
│   │   └── wishlist_repository.py
│   ├── routers/
│   │   ├── chat.py                 # Chat SSE endpoint
│   │   ├── chat_queue.py           # Queue/reattach/cancel endpoints
│   │   ├── health.py               # Health check
│   │   └── info.py                 # Info endpoint
│   └── services/
│       ├── chat_service.py         # Conversation history, stream orchestration
│       ├── guard_service.py        # Prompt injection detection
│       ├── llm_service.py          # LLM adapter selection + streaming
│       ├── model_usage_service.py  # Per-model Redis rate counters
│       └── rag_service.py          # ChromaDB + Ollama embeddings, vector query
├── jobs/
│   ├── ingest.py                   # Ingest products/reviews/PDFs into ChromaDB
│   ├── scheduler.py                # Runs ingest on 15-minute interval
│   └── worker.py                   # RabbitMQ consumer, streams via Redis pub/sub
└── tests/
    ├── unit/                       # app/ unit tests (mocked), mirrors app/ structure
    ├── api/                        # HTTP endpoint tests via AsyncClient
    ├── integration/                # repository tests against real DB
    └── jobs/                       # jobs/ unit tests (mocked)
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

## Tests

```
tests/
  unit/         # app/ unit tests (mocked dependencies), mirrors app/ structure
  api/          # HTTP endpoint tests via AsyncClient
  integration/  # repository tests against real DB (requires Docker)
  jobs/         # jobs/ unit tests (mocked dependencies)
```

```bash
# All tests in Docker (unit + api + integration)
make assistant-test

# All tests with coverage
make assistant-test-coverage

# Run any command in Docker container
apps/assistant/bin/run <cmd>
```

Rules:
- No `# type: ignore` - use proper types always
- No `# pragma: no cover` in production code - only in tests for unreachable branches (e.g. `asyncio.CancelledError` on SSI disconnect, `__main__` blocks)
- Async generator fakes use `AsyncGenerator[str, None]` return type
- Integration tests auto-skip when DB unavailable
