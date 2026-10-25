# 🔒 Assistant Requirements

**Purpose:** Minimum requirements for development, testing, and optional `Docker` services.

---

## 1. Mandatory Requirements

- **Python** 3.12 - 3.13
- **uv** for dependency management
- **Node.js** (LTS) + **pnpm** for frontend assets (Vite, TS build)
- **Git** version control
- **PostgreSQL** 17+ for shared data access (read-only from ecommerce DB)
- **Redis** for conversation history and pub/sub streaming
- **ChromaDB** for vector store (RAG pipeline)

### Choose an inference provider

Configure one provider with `ASSISTANT_TYPE`; **Ollama and the external API provider are alternatives, not simultaneous requirements**:

- **`ollama`** - Install Ollama, pull the configured LLM and embedding models, and set `ASSISTANT_OLLAMA_HOST`. RabbitMQ is optional; when configured, Ollama requests are queued through it.
- **`api`** - Configure the external provider credentials, model and embedding endpoint. Ollama is not required, and requests stream directly without RabbitMQ.

> ⚠️ PostgreSQL, Redis and ChromaDB are required for the full assistant experience. RabbitMQ is not required to process API-provider requests; it is only needed for queued Ollama processing.

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
- The selected inference provider and its model/embedding configuration must be available before the assistant can respond
- Redis is required for SSE streaming - without it, real-time responses are unavailable
- **For a comprehensive overview of the full [Tech Stack](TECHSTACK.md), architecture, and all Quality Assurance tools, please refer to the documentation.**

---

See also: [Platform Requirements](../../../docs/REQUIREMENTS.md)
