# 🛠️ Assistant Technology Stack

This document provides a comprehensive overview of the technologies, frameworks, and libraries used in the assistant app.

---

## 1. Backend

- **Language:** Python 3.12 – 3.13
- **Framework:** FastAPI
- **ASGI Server:** Uvicorn
- **Dependency Manager:** uv
- **Data Validation:** Pydantic + pydantic-settings
- **LLM Inference & Embeddings:** Ollama
- **Vector Store:** ChromaDB (RAG pipeline)
- **Numerical Computing:** numpy (vector/embedding operations)
- **Database Access:** psycopg2 (PostgreSQL - shared ecommerce DB)
- **Async Messaging:** aio-pika (RabbitMQ - `ai_requests` queue)
- **Real-time Streaming:** Redis pub/sub (SSE responses)
- **Conversation History:** Redis
- **Rate Limiting:** SlowAPI
- **HTTP Client:** httpx
- **Error Monitoring:** Sentry (sentry-sdk[fastapi])

---

## 2. Frontend

- **Runtime:** Node.js (LTS)
- **Package Manager:** pnpm
- **Framework:** React
- **Build Tool:** Vite
- **Language:** TypeScript
- **Styling:** Tailwind CSS

---

## 3. Infrastructure & Services

- **Containerization:** Docker & Docker Compose
- **Version Control:** Git
- **Database:** PostgreSQL (shared, read from ecommerce DB)
- **Cache & Pub/Sub:** Redis (conversation history + SSE streaming)
- **Message Broker:** RabbitMQ (`ai_requests` queue)
- **LLM Runtime:** Ollama (local model inference + embeddings)
- **Vector Store:** ChromaDB
- **Error Monitoring:** Sentry
- **CI/CD:** GitHub Actions (see [DEVOPS.md](runtime/DEVOPS.md) for full pipeline details)

---

## 4. Documentation & Modeling

- **UML diagrams** are used throughout the project via:
  - **Flowcharts** - for request flows and RAG pipeline.
  - **Graphs** - for architecture and system visualization.
- Diagrams are created in **Markdown / Mermaid**, making them easy to maintain and update.

---

## 5. Data & Configuration

- **Configuration:** TOML (pyproject.toml), Dotenv (.env files for secrets)
- **Data Exchange:** JSON
- **Environment:** pydantic-settings (typed env config)

---

## 6. Automation & Tooling

- **Command Runner:** Makefile
- **Dependency Automation:** Renovate (automated dependency update PRs for uv, pnpm, Docker, Helm)
- **Scripts:** Bash

---

## 7. Quality Assurance

### Backend QA

| Tool    | Purpose                  | Execution (via make / uv) |
|---------|--------------------------|---------------------------|
| Ruff    | Linting + import sorting | make assistant-lint       |
| Ruff    | Code formatting          | make assistant-format     |
| Mypy    | Static type checking     | make assistant-type-check |
| Vulture | Dead code detection      | make assistant-dead-code  |

### Frontend QA

| Tool              | Purpose                                  | Execution       |
|-------------------|------------------------------------------|-----------------|
| TypeScript        | Static type checking                     | pnpm type-check |
| ESLint + Prettier | TS linting and automated code formatting | pnpm lint       |

### CI/CD Tooling

Tools that run exclusively in GitHub Actions pipelines - not available as local CLI commands.

| Tool                  | Purpose                                               | Workflow                     |
|-----------------------|-------------------------------------------------------|------------------------------|
| Trivy                 | CVE scan on Docker images and dependencies            | `cve-scan.yml`, `deploy.yml` |
| Gitleaks              | Secrets detection in commits                          | `supply-chain.yml`           |
| OSSF Scorecard        | Supply chain security scoring                         | `supply-chain.yml`           |
| cosign / Sigstore     | Production image signing                              | `deploy.yml`                 |
| SBOM (Syft / Anchore) | Software Bill of Materials generation and attestation | `deploy.yml`                 |
| commitlint            | Conventional commit message format enforcement        | `validate-commits.yml`       |

---

See also: [Production Infrastructure](../../../docs/TECHSTACK.md)
