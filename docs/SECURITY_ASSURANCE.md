# Security Assurance Case

This document provides an overview of the security assurance for the LevelUp Store monorepo. Detailed assurance cases for each application are maintained separately:

- [Ecommerce Security Assurance](../apps/ecommerce/docs/SECURITY_ASSURANCE.md)
- [Assistant Security Assurance](../apps/assistant/docs/SECURITY_ASSURANCE.md)

---

## Shared Security Measures

The following security measures apply across the entire monorepo:

- **TLS 1.2+** via Traefik ingress for all network communication
- **Secrets management** via HashiCorp Vault, injected at runtime via Kubernetes secrets
- **Dependency monitoring** via Renovate, Trivy, and Grype
- **Secret scanning** via Gitleaks on every commit and PR
- **Supply chain security** via pinned GitHub Actions digests and OpenSSF Scorecard
- **SAST** via CodeQL and Semgrep on every PR
- **DAST** via OWASP ZAP on every push to main
