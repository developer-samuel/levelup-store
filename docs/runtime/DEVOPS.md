# 🛠️ DevOps

This document describes **platform-level CI/CD pipelines** shared across all apps.

---

## 🧱 CI/CD Pipelines

All pipelines are implemented using GitHub Actions.

| Workflow                         | Trigger                                                                                                                        | Description                                                                             |
|----------------------------------|--------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------|
| `deploy.yml` + `build-image.yml` | push → main                                                                                                                    | Build + push both app images to GHCR, SBOM, Trivy scan, cosign sign, update Helm values |
| `cve-scan.yml`                   | PR → main/staging (composer.lock, uv.lock, pnpm-lock.yaml)                                                                     | Trivy + Grype CVE scan; HIGH/CRITICAL findings block                                     |
| `dependency-review.yml`          | PR → main/staging                                                                                                               | GitHub Dependency Review; HIGH or worse findings block                                   |
| `supply-chain.yml`               | push/PR → main/staging, schedule Mon 02:00                                                                                     | Gitleaks secrets detection, OSSF Scorecard                                              |
| `staging-tls-scan.yml`           | schedule Mon 02:00, workflow_dispatch                                                                                          | testssl.sh against staging; HIGH/CRITICAL findings block                               |
| `release.yml`                    | release created                                                                                                                | Publish release artifacts                                                               |
| `release-drafter.yml`            | push → main                                                                                                                    | Auto-draft next release notes                                                           |
| `infrastructure-lint.yml`        | PR → main (infrastructure/ansible/**, **/*.sh)                                                                                 | ansible-lint, ShellCheck                                                                |
| `infrastructure-validate.yml`    | PR → main (infrastructure/terraform/**, kubernetes/**, helm/**, docker/**, apps/ecommerce/docker/**, apps/assistant/docker/**) | Checkov IaC security scan, Hadolint                                                     |
| `validate-commits.yml`           | PR → main (opened, edited, reopened, synchronize)                                                                              | commitlint - conventional commit message format enforcement                             |

---

## 🔧 Shared Tooling

Tools used across all apps - not specific to ecommerce or assistant.

| Tool                     | Purpose                                               | Workflow                       |
|--------------------------|-------------------------------------------------------|--------------------------------|
| Trivy                    | CVE scan on Docker images and dependencies            | `cve-scan.yml`, `deploy.yml`   |
| Grype (Anchore)          | Vulnerability scan on built image                     | `cve-scan.yml`                 |
| GitHub Dependency Review | Dependency vulnerability review on PRs                | `cve-scan.yml`                 |
| Gitleaks                 | Secrets detection in commits                          | `supply-chain.yml`             |
| OSSF Scorecard           | Supply chain security scoring                         | `supply-chain.yml`             |
| cosign / Sigstore        | Production image signing                              | `deploy.yml`                   |
| SBOM (Syft / Anchore)    | Software Bill of Materials generation and attestation | `deploy.yml`                   |
| Checkov                  | IaC security scanning (Terraform, Helm, K8s); HIGH/CRITICAL findings block | `infrastructure-validate.yml`  |
| testssl.sh               | Staging TLS configuration scan; HIGH/CRITICAL findings block | `staging-tls-scan.yml`          |
| ShellCheck               | Shell script static analysis                          | `infrastructure-lint.yml`      |
| ansible-lint             | Ansible playbook linting                              | `infrastructure-lint.yml`      |
| commitlint               | Conventional commit message format enforcement        | `validate-commits.yml`         |

---

## 📊 Diagrams

- [Deployment Pipeline](../diagrams/graphs/architecture/deployment.mmd)
- [CI/CD Workflows](../diagrams/graphs/tooling/workflows.mmd)

---

See also: [Ecommerce DevOps](../../apps/ecommerce/docs/runtime/DEVOPS.md) · [Assistant DevOps](../../apps/assistant/docs/runtime/DEVOPS.md)
