# Security Assurance Case - Assistant

This document justifies why the security requirements of the assistant application are met, including a threat model, trust boundaries, secure design principles, and mitigations for common implementation weaknesses.

---

## Threat Model

### Assets

- Conversation history (Redis-backed, per session)
- Product catalog data (ChromaDB embeddings)
- API keys for Ollama and external services
- RabbitMQ queue access

### Threat Actors

| Actor                    | Capability                          | Goal                                           |
|--------------------------|-------------------------------------|------------------------------------------------|
| Unauthenticated web user | HTTP requests, public chat endpoint | Abuse AI resources, extract product data       |
| External attacker        | Network access, automated requests  | Rate limit bypass, prompt injection            |
| Compromised dependency   | Supply chain access                 | Execute malicious code during build or runtime |

### Attack Vectors

- Prompt injection via user-supplied chat messages
- Rate limit bypass (excessive AI resource usage)
- Dependency vulnerabilities (CVEs in third-party packages)
- Secrets leakage via repository or logs

---

## Trust Boundaries

```
[Public Internet]
      |
      | HTTPS (TLS 1.2+, Traefik)
      |
[Kubernetes Ingress] ← trust boundary: TLS termination, rate limiting
      |
[FastAPI Application]
      |
      ├── Routers (HTTP endpoints) ← input validation via Pydantic models
      |         ↓ validated models only
      ├── Services (chat orchestration, RAG pipeline)
      |         ↓ internal calls only
      └── Infrastructure (PostgreSQL, ChromaDB, Redis, RabbitMQ, Ollama)
                ↓ internal network only, not exposed publicly
```

Secrets (API keys, database passwords) are stored in HashiCorp Vault and injected at runtime via Kubernetes secrets - never in source code or config files.

See also: [Architecture](runtime/ARCHITECTURE.md)

---

## Secure Design Principles Applied

**Least privilege** - The assistant app has read-only access to the product catalog. Each service has only the permissions it needs.

**Defense in depth** - Rate limiting via SlowAPI at the application level, TLS at ingress, internal network isolation for all backend services.

**Separation of concerns** - Layered architecture (routers → services → infrastructure) enforces that business logic does not directly depend on transport or infrastructure details.

**Fail securely** - Invalid inputs are rejected via Pydantic validation before processing. Exceptions do not expose stack traces in production.

**Economy of mechanism** - No custom cryptographic implementations. TLS and secrets management are handled by Traefik and HashiCorp Vault.

---

## Common Implementation Weaknesses Countered

### OWASP Top 10

| Weakness                        | Mitigation                                                                                        |
|---------------------------------|---------------------------------------------------------------------------------------------------|
| A01 Broken Access Control       | Chat session is scoped per user session; no cross-session data access                             |
| A02 Cryptographic Failures      | TLS 1.2+ for all network communication; secrets in Vault                                          |
| A03 Injection                   | Pydantic models validate all inputs; parameterized queries via psycopg2 for all PostgreSQL access |
| A04 Insecure Design             | Layered architecture with clear trust boundaries; threat model documented here                    |
| A05 Security Misconfiguration   | Secrets in Vault; Docker non-root containers; internal services not exposed                       |
| A06 Vulnerable Components       | Renovate automated dependency updates; Trivy and Grype CVE scanning on every PR                   |
| A07 Authentication Failures     | Session-based chat scoping; rate limiting via SlowAPI                                             |
| A08 Software Integrity Failures | Gitleaks secret scanning; pinned GitHub Actions by commit hash; uv.lock for reproducible builds   |
| A09 Logging Failures            | OpenTelemetry tracing; no sensitive data (API keys, conversation content) in logs                 |
| A10 SSRF                        | No user-controlled URLs are fetched server-side                                                   |

### Additional Mitigations

- **Prompt injection** - LLM prompts use structured templates; user input is passed as data, not instructions
- **Rate limiting** - SlowAPI enforces per-IP rate limits on chat endpoints
- **Supply chain** - GitHub Actions pinned by digest; Renovate monitors dependencies
