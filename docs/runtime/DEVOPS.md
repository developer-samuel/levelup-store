# 🛠️ DevOps

This document describes **Docker setup and CI/CD pipelines** for this project.
Everything is set up to make development smooth, automated, and maintainable.

## Principles

- **Automation First** - All builds, tests, and deployments are fully automated.
- **Fast Feedback** - Each pipeline provides immediate feedback on failures.
- **Clear & Maintainable** - All steps and services are easy to understand and modify.

---

## 🐳 Docker

The project uses two Dockerfiles:

| File                     | Purpose                                                                 | Build context   |
|--------------------------|-------------------------------------------------------------------------|-----------------|
| `docker/Dockerfile`      | Local dev base image - PHP-FPM runtime only, app code is volume-mounted | `./docker`      |
| `docker/Dockerfile.prod` | Production image - PHP-FPM + Nginx + full app code baked in             | `.` (repo root) |

Production-specific configs live in `docker/_prod/`:
- `config/nginx/nginx.conf` - Nginx main config (non-root, temp paths under `/tmp`)
- `config/nginx/server.conf` - Nginx server block (port 8000, FastCGI → localhost:9000)
- `config/php/fpm-pool.conf` - PHP-FPM pool (non-root, TCP socket 127.0.0.1:9000)
- `scripts/run.sh` - Production entrypoint (starts php-fpm + nginx)

---

## 🧱 CI/CD Pipelines

All pipelines are implemented using GitHub Actions, ensuring automated builds, tests, and deploys.
PHP and Node versions are centralized as repository variables (`PHP_VERSION`, `NODE_VERSION`).

| Workflow                      | Trigger                                                                                                  | Description                                                                                                                                  |
| ----------------------------- | -------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------- |
| `ci.yml`                      | push/PR → main                                                                                           | PHP lint, static analysis, architecture check, assets lint, PHPUnit (PHP 8.3 + forward-compat 8.4/8.5), Vitest, Playwright E2E               |
| `deploy.yml`                  | push → main                                                                                              | Build `Dockerfile.prod`, push to GHCR, generate SBOM, Trivy scan, sign with Sigstore (cosign), update `values.prod.yaml` → ArgoCD auto-syncs |
| `code-quality.yml`            | push/PR → main                                                                                           | PHPUnit + Vitest coverage upload to Codecov, ESLint report, SonarCloud analysis                                                              |
| `frontend-audit.yml`          | push/PR → main (assets), schedule Mon 02:00                                                              | Lighthouse (push/schedule), axe-core + pa11y accessibility (PR only)                                                                         |
| `zap.yml`                     | push → main, schedule Mon 02:00                                                                          | OWASP ZAP DAST baseline scan                                                                                                                 |
| `sast.yml`                    | push/PR → main (src/**, config/**, public/**, templates/**, assets/**), schedule Mon 02:00               | CodeQL static security analysis                                                                                                              |
| `supply-chain.yml`            | push/PR → main, schedule Mon 02:00                                                                       | Gitleaks secrets scan + OSSF Scorecard                                                                                                       |
| `cve-scan.yml`                | PR → main (composer.lock, pnpm-lock.yaml, docker/**)                                                     | Trivy CVE scan on Docker image                                                                                                               |
| `docker-validate.yml`         | PR → main (docker/**, docker-compose*.yml, .env.example, .hadolint.yaml)                                 | Validate Dockerfiles and Docker Compose files                                                                                                |
| `infrastructure-validate.yml` | PR → main (infrastructure/helm/**, infrastructure/kubernetes/**, infrastructure/terraform/**, docker/**) | Validate Helm charts + Kubernetes manifests                                                                                                  |
| `infrastructure-lint.yml`     | PR → main (infrastructure/ansible/**, **/*.sh, bin/**)                                                   | Ansible lint + shell script lint                                                                                                             |
| `bundle-size.yml`             | PR → main (assets/**, vite/**, vite.config.ts, package.json, pnpm-lock.yaml)                             | Frontend bundle size report                                                                                                                  |
| `renovate-validate.yml`       | PR → main (renovate config files)                                                                        | Validate Renovate config                                                                                                                     |
| `validate-commits.yml`        | PR → main                                                                                                | Enforce conventional commit message format                                                                                                   |
| `labeler.yml`                 | PR opened/updated                                                                                        | Auto-label PRs based on changed paths                                                                                                        |
| `release-drafter.yml`         | push → main                                                                                              | Update release draft with changelog entries                                                                                                  |
| `release.yml`                 | GitHub Release created                                                                                   | Prepend release notes to `CHANGELOG.md`, commit to main                                                                                      |
| `notify.yml`                  | Deploy workflow completed                                                                                | Send deployment notification                                                                                                                 |

### Job dependency chain (ci.yml)

```
php-checks ──┐
deptrac ─────┴──► phpunit ──┐
                            ├──► e2e  (push only, not required)
lint-assets      vitest ───┘

php-checks + deptrac + lint-assets + phpunit + vitest
                  │
                  ▼
            ci-pass ✅  (single required status check)
```

PHPUnit runs only if php-checks and deptrac pass. E2E runs only if unit tests pass but is not a required check.
`ci-pass` is the single required check configured in branch protection rules.

---

## 🎯 DevOps Guidelines

- Every push or pull request triggers pipelines automatically.
- Builds fail if deployments or tests fail.
- Immediate feedback is provided on any errors.
- Pipelines are standardized and fully documented for consistency.
- Docker ensures all developers use the same environment, reducing “it works on my machine” issues.

---

## 📊 Diagrams

- [CI/CD Workflows](../diagrams/graphs/tooling/workflows.mmd)
- [Testing Strategy](../diagrams/graphs/tooling/testing.mmd)
- [Deployment Pipeline](../diagrams/graphs/architecture/deployment.mmd)
- [GitOps Flow](../diagrams/graphs/architecture/gitops.mmd)
- [Async Messaging](../diagrams/graphs/architecture/async-messaging.mmd)

---

See also: [Testing Tools](TESTS.md)
