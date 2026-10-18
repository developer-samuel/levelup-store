# Security Assurance Case - Ecommerce

This document justifies why the security requirements of the ecommerce application are met, including a threat model, trust boundaries, secure design principles, and mitigations for common implementation weaknesses.

---

## Threat Model

### Assets

- User credentials (passwords, JWT tokens)
- Payment data (processed via Stripe - not stored locally)
- Order and personal data (names, addresses, emails)
- Admin access and configuration secrets

### Threat Actors

| Actor                    | Capability                         | Goal                                            |
|--------------------------|------------------------------------|-------------------------------------------------|
| Unauthenticated web user | HTTP requests, public endpoints    | Access restricted data, escalate privileges     |
| Authenticated user       | Valid JWT token                    | Access other users' data, abuse endpoints       |
| External attacker        | Network access, automated scanning | Exploit vulnerabilities, inject malicious input |
| Compromised dependency   | Supply chain access                | Execute malicious code during build or runtime  |

### Attack Vectors

- SQL injection via user-supplied input
- XSS via reflected or stored user content
- JWT token theft or forgery
- Broken authentication (brute force, credential stuffing)
- Dependency vulnerabilities (CVEs in third-party packages)
- Secrets leakage via repository or logs
- CSRF attacks on state-changing endpoints

---

## Trust Boundaries

```
[Public Internet]
      |
      | HTTPS (TLS 1.2+, Traefik)
      |
[Kubernetes Ingress] ← trust boundary: TLS termination, rate limiting
      |
[Symfony Application]
      |
      ├── Presentation Layer (controllers, templates) ← input validation here
      |         ↓ validated DTOs only
      ├── Application Layer (commands, queries)
      |         ↓ domain objects only
      ├── Domain Layer (entities, value objects) ← no external dependencies
      |
      ├── Infrastructure Layer (PostgreSQL, Redis, RabbitMQ, MinIO)
      |         ↓ internal network only, not exposed publicly
      └── External Services (Stripe, Mailer) ← HTTPS, authenticated
```

Secrets (database passwords, JWT keys, API keys) are stored in HashiCorp Vault and injected at runtime via Kubernetes secrets - never in source code or config files.

See also: [Architecture](runtime/ARCHITECTURE.md)

---

## Secure Design Principles Applied

**Least privilege** - Symfony Security roles (ROLE_USER, ROLE_ADMIN) restrict access. Each service has only the permissions it needs.

**Defense in depth** - Multiple layers of protection: TLS at ingress, authentication at firewall level, authorization at controller level, input validation at form level.

**Separation of concerns** - Hexagonal architecture enforces that business logic (Core/Domain) never directly depends on infrastructure or external systems.

**Fail securely** - Invalid inputs are rejected with 400/403 responses. Exceptions do not expose stack traces in production (Symfony production mode).

**Economy of mechanism** - Cryptographic operations are delegated to established libraries (Symfony PasswordHasher, LexikJWTBundle, OpenSSL). No custom crypto implementations.

**Open design** - Security does not rely on obscurity. All security mechanisms are based on publicly reviewed standards (Argon2id, RS256, TLS).

---

## Common Implementation Weaknesses Countered

### OWASP Top 10

| Weakness                        | Mitigation                                                                              |
|---------------------------------|-----------------------------------------------------------------------------------------|
| A01 Broken Access Control       | Symfony Security with role-based access control on all API endpoints                    |
| A02 Cryptographic Failures      | Argon2id for passwords, RS256 JWT, TLS 1.2+, no broken algorithms                       |
| A03 Injection                   | Doctrine ORM with parameterized queries; Symfony Form constraints for input validation  |
| A04 Insecure Design             | Hexagonal architecture with clear trust boundaries; threat model documented here        |
| A05 Security Misconfiguration   | Symfony production mode; secrets in Vault; Docker non-root containers                   |
| A06 Vulnerable Components       | Renovate automated dependency updates; Trivy and Grype CVE scanning on every PR         |
| A07 Authentication Failures     | JWT with short TTL; HttpOnly secure cookies; stateless API authentication               |
| A08 Software Integrity Failures | Gitleaks secret scanning; pinned GitHub Actions by commit hash; locked dependency files |
| A09 Logging Failures            | OpenTelemetry tracing; no sensitive data (passwords, tokens) in logs                    |
| A10 SSRF                        | No user-controlled URLs are fetched server-side                                         |

### Additional Mitigations

- **Secrets leakage** - Gitleaks scans every commit and PR; all credentials stored in HashiCorp Vault
- **Supply chain** - GitHub Actions pinned by digest; Renovate monitors dependencies; OpenSSF Scorecard active
- **Brute force** - Stateless JWT authentication; no persistent sessions to brute force
