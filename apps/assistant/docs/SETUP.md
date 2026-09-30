# ⚙️ Assistant Setup

Minimal setup guide for **environment variables and runtime configuration**.

**Source of truth:**
- `apps/assistant/.env.example` (Docker / shared setup)

This file explains **only the steps required to prepare configuration**.  
It does not document individual variable meanings - those live inline in the `.env.example` file.

---

## 1. Preparation

Before configuring environment variables, make sure dependencies are installed.

Complete **step 1** from [INSTALL.md](INSTALL.md) first.

---

## 2. Environment Variables

All environment variables are documented inline in `apps/assistant/.env.example`.  
Review comments carefully before editing.

Core variables to check / configure:

- **Ollama**  
  `OLLAMA_HOST`, `OLLAMA_MODEL`, `OLLAMA_EMBED_MODEL`  
  LLM inference server host and model names. Make sure the models are pulled before starting:
  ```bash
  ollama pull mistral:7b
  ollama pull nomic-embed-text
  ```

- **ChromaDB**  
  `CHROMA_HOST`  
  Vector store host for the RAG pipeline.

- **Database** ⚠️ *Must match ecommerce app credentials*  
  `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DATABASE_URL`  
  Shared PostgreSQL instance - use the same credentials as the ecommerce app.

- **Redis** ⚠️ *Must match ecommerce app credentials*  
  `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD`, `REDIS_URL`  
  Used for conversation history and SSE pub/sub streaming.

- **RabbitMQ** ⚠️ *Must match ecommerce app credentials*  
  `RABBITMQ_HOST`, `RABBITMQ_PORT`, `RABBITMQ_USER`, `RABBITMQ_PASS`, `RABBITMQ_VHOST`, `RABBITMQ_URL`  
  Used for the `ai_requests` queue.

- **CORS**  
  `CORS_ORIGINS`  
  Allowed origins for the API. Set to your frontend URL in production.

- **API Key**  
  `API_KEY`  
  Protects the chat endpoint. Leave empty to disable. Generate with:
  ```bash
  openssl rand -hex 32
  ```

- **Error Monitoring**  
  `SENTRY_DSN`  
  Sentry error tracking. Leave empty to disable.

- **Support Email**  
  `SUPPORT_EMAIL`  
  Shown to customers when no relevant products are found.

---

> ⚠️ Reminder:
- `.env.example` is the primary source of documentation.
- Redis, RabbitMQ, and PostgreSQL credentials must match the ecommerce app - they share the same Docker services.

---

See also: [Platform Setup](../../../docs/SETUP.md)
