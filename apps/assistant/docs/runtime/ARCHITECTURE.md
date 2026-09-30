# ARCHITECTURE

> This document describes the **assistant** application architecture.

## 🏗️ Principles

- **Layered Architecture** - Transport → Application → Infrastructure → External.
- **RAG (Retrieval-Augmented Generation)** - Product catalog embedded into ChromaDB, retrieved at query time to ground LLM responses.
- **Event-Driven Streaming** - Responses streamed via Redis pub/sub → SSE to the browser.
- **Queue-Based Processing** - Heavy requests queued via RabbitMQ, processed by a dedicated AI worker.

## 🧱 Assistant Backend Structure

```
app/
├── routers/                    # HTTP endpoints (chat, health)
├── services/                   # Orchestration: chat_service coordinates RAG + queue + streaming
├── repositories/               # PostgreSQL product catalog access
├── config.py                   # pydantic-settings environment config
├── models.py                   # Pydantic request/response models
├── middleware.py               # Middleware registration
├── rate_limiter.py             # SlowAPI rate limiting
├── rag.py                      # RAG pipeline: ChromaDB retrieval + Ollama embeddings
├── conversation.py             # Redis-backed conversation history
├── queue.py                    # Redis pub/sub slot management + SSE streaming
├── rabbitmq.py                 # aio-pika publish to ai_requests queue
├── prompts.py                  # LLM prompt templates
├── responses.py                # SSE response helpers
└── main.py                     # FastAPI app entry point
```

---

## 📊 Diagrams

- [Layers](../diagrams/graphs/architecture/layers.mmd)
- [Async Messaging](../diagrams/graphs/architecture/async-messaging.mmd)
- [Chat Request Flow](../diagrams/flowcharts/chat-request.mmd)
- [RAG Pipeline](../diagrams/flowcharts/rag-pipeline.mmd)

---

See also: [System Context](../../../../docs/diagrams/graphs/architecture/system-context.mmd) · [Production Architecture](../../../../docs/diagrams/graphs/architecture/architecture.mmd) · [Deployment Pipeline](../../../../docs/diagrams/graphs/architecture/deployment.mmd)
