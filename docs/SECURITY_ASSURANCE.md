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

---

## Local security scanning

Run these locally before pushing to catch issues early. All tools also run automatically in CI.

### Checkov (IaC - Terraform, Kubernetes, Helm, Dockerfile)

```bash
uv tool install checkov
```

```bash
checkov -d . --config-file .checkov.yaml \
  --skip-path infrastructure/helm/.+/charts
```

> **Note:** Third-party Helm chart dependencies (`charts/` subdirectories) are not committed to the repo - skipped explicitly above. False positives for third-party charts and dev Dockerfiles are suppressed via `skip-check` in `.checkov.yaml`.

### Trivy (container image & filesystem CVEs)

```bash
curl -sfL https://raw.githubusercontent.com/aquasecurity/trivy/main/contrib/install.sh | sudo sh -s -- -b /usr/local/bin
```

```bash
trivy fs . \
  --skip-dirs .pnpm-store \
  --skip-files '**/.env,**/.env.*,**/*.pem,**/*.pem.*'
```

> **Note:** `.env*` and `*.pem*` files contain local dev secrets (not real credentials). `.pnpm-store/` is a local package cache - all are intentionally excluded from scanning.

### Grype (dependency vulnerabilities)

```bash
curl -sSfL https://raw.githubusercontent.com/anchore/grype/main/install.sh | sudo sh -s -- -b /usr/local/bin
```

```bash
grype dir:.
```

### Semgrep (SAST)

```bash
uv tool install semgrep
```

```bash
semgrep scan --config auto \
  --exclude '.github/' \
  --exclude 'scripts/' \
  --exclude 'infrastructure/kubernetes/policies/' \
  .
```

> SonarCloud and ZAP require a running server - run in CI only.
>
> **Note:** `.github/`, `scripts/` and `infrastructure/kubernetes/policies/` are excluded locally - they contain false positives (composite Actions using `inputs.*`, dev helper scripts, and Kyverno policy examples). CI runs the full scan without exclusions.

---

## Related docs

- [Secret Scanning](SECRETS.md)
- [Validation](VALIDATION.md)
