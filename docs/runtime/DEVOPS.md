# 🛠️ DevOps

This document describes **platform-level CI/CD pipelines** shared across all apps.

---

## 🧱 CI/CD Pipelines

All pipelines are implemented using GitHub Actions.

| Workflow                         | Trigger                                                                                                                        | Description                                                                             |
|----------------------------------|--------------------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------|
| `deploy.yml` + `build-image.yml` | push → main                                                                                                                    | Build + push both app images to GHCR, SBOM, Trivy scan, cosign sign, update Helm values |
| `cve-scan.yml`                   | PR → main (composer.lock, uv.lock, pnpm-lock.yaml)                                                                             | Trivy + Grype CVE scan, GitHub Dependency Review                                        |
| `supply-chain.yml`               | push/PR → main, schedule Mon 02:00                                                                                             | Gitleaks secrets detection, OSSF Scorecard                                              |
| `release.yml`                    | release created                                                                                                                | Publish release artifacts                                                               |
| `release-drafter.yml`            | push → main                                                                                                                    | Auto-draft next release notes                                                           |
| `infrastructure-lint.yml`        | PR → main (infrastructure/ansible/**, **/*.sh)                                                                                 | ansible-lint, ShellCheck                                                                |
| `infrastructure-validate.yml`    | PR → main (infrastructure/terraform/**, kubernetes/**, helm/**, docker/**, apps/ecommerce/docker/**, apps/assistant/docker/**) | Checkov IaC security scan, Hadolint                                                     |
| `validate-commits.yml`           | PR → main (opened, edited, reopened, synchronize)                                                                              | commitlint - conventional commit message format enforcement                             |

---

## 📊 Diagrams

- [Deployment Pipeline](../diagrams/graphs/architecture/deployment.mmd)
- [CI/CD Workflows](../diagrams/graphs/tooling/workflows.mmd)

---

See also: [Ecommerce DevOps](../../apps/ecommerce/docs/runtime/DEVOPS.md) · [Assistant DevOps](../../apps/assistant/docs/runtime/DEVOPS.md)
