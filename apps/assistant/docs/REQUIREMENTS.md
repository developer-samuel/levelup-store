# 🔒 Assistant Requirements

**Purpose:** Minimum requirements for development, testing, and optional `Docker` services.

---

## 1. Mandatory Requirements

- **Python** 3.12 – 3.13
- **uv** for dependency management
- **Node.js** (LTS) + **pnpm** or **npm** for frontend assets (Vite, TS build)
- **Git** version control
- **PostgreSQL** 17+ for shared data access (read-only from ecommerce DB)
- **Redis** for conversation history and pub/sub streaming
- **RabbitMQ** for AI request queue (`ai_requests`)
- **Ollama** for LLM inference and embeddings
- **ChromaDB** for vector store (RAG pipeline)

> ⚠️ PostgreSQL, Redis, RabbitMQ, Ollama, and ChromaDB are all required for full functionality.

---

## 2. Optional Requirements (Recommended)

These improve developer experience or enable optional features. Not required for core application functionality:

- **Docker** for containerized environment
- **WSL 2** strongly recommended for **Windows users** to run Docker and Linux-based tools (Ubuntu) with native performance.
- **Make** (GNU Make) required to run project commands
- **Sentry** account for error monitoring

---

## 3. Notes

- `Docker` is optional; all services can run locally if preferred
- Ollama must have at least one model pulled before the assistant can respond
- Redis is required for SSE streaming - without it, real-time responses are unavailable
- **For a comprehensive overview of the full [Tech Stack](TECHSTACK.md), architecture, and all Quality Assurance tools, please refer to the documentation.**

---

See also: [Platform Requirements](../../../docs/REQUIREMENTS.md)
