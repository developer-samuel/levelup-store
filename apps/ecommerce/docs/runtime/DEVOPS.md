# 🛠️ DevOps

This document describes **Docker setup and CI/CD pipelines** for the ecommerce app.
Everything is set up to make development smooth, automated, and maintainable.

## Principles

- **Automation First** - All builds, tests, and deployments are fully automated.
- **Fast Feedback** - Each pipeline provides immediate feedback on failures.
- **Clear & Maintainable** - All steps and services are easy to understand and modify.

---

## 🐳 Docker

The project uses a shared root Dockerfile with per-app targets:

| File                               | Target                                                                  | Purpose                                                       | Build context   |
|------------------------------------|-------------------------------------------------------------------------|---------------------------------------------------------------------------------|
| `docker/Dockerfile.prod`           | `ecommerce`                                                             | Production image - PHP-FPM + Nginx + compiled assets baked in | `.` (repo root) |
| `docker/Dockerfile.prod`           | `assistant`                                                             | Production image - Python backend                             | `.` (repo root) |
| `apps/ecommerce/docker/Dockerfile` | Local dev base image - PHP-FPM runtime only, app code is volume-mounted | `./apps/ecommerce/docker`                                     |                 |

Production-specific configs live in `docker/_prod/`:
- `config/nginx/nginx.conf` - Nginx main config (non-root, temp paths under `/tmp`)
- `config/nginx/server.conf` - Nginx server block (port 8000, FastCGI → localhost:9000)
- `config/php/fpm-pool.conf` - PHP-FPM pool (non-root, TCP socket 127.0.0.1:9000)
- `scripts/run.sh` - Production entrypoint (starts php-fpm + nginx)

---

## 🧱 CI/CD Pipelines

All pipelines are implemented using GitHub Actions.
PHP and Node versions are centralized as repository variables (`PHP_VERSION`, `NODE_VERSION`).

| Workflow                        | Trigger                                                                                                                                            | Description                                                                                                                |
|---------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------|
| `ecommerce-ci.yml`              | push/PR → main                                                                                                                                     | PHP lint, static analysis, architecture check, assets lint, PHPUnit (8.3 + forward-compat 8.4/8.5), Vitest, Playwright E2E |
| `ecommerce-coverage.yml`        | push/PR → main (apps/ecommerce/**)                                                                                                                 | PHPUnit + Vitest coverage upload to Codecov, ESLint report, SonarCloud analysis                                            |
| `ecommerce-frontend-audit.yml`  | push/PR → main (assets/**, templates/**, public/**), schedule Mon 02:00                                                                            | Lighthouse (push/schedule), axe-core + pa11y accessibility (PR only)                                                       |
| `ecommerce-lighthouse.yml`      | PR → main (assets/**, templates/**, public/**, src/Presentation/**, vite.config.ts, pnpm-lock.yaml), workflow_dispatch                             | Lighthouse CI - performance scores + budget enforcement, posts results as PR comment                                       |
| `ecommerce-zap.yml`             | push → main (src/**, database/**, packages/**, config/**, bin/**, templates/**, assets/**, public/**, scripts/**, init.php), schedule Mon 02:00    | OWASP ZAP DAST baseline scan                                                                                               |
| `ecommerce-sast.yml`            | push/PR → main (src/**, database/**, packages/**, config/**, bin/**, templates/**, assets/**, public/**, scripts/**, init.php), schedule Mon 02:00 | CodeQL static security analysis                                                                                            |
| `ecommerce-docker-validate.yml` | PR → main (docker/**, apps/ecommerce/docker/**, docker-compose*.yml, .env.example, .hadolint.yaml)                                                 | Validate Dockerfiles and Docker Compose files                                                                              |
| `ecommerce-bundle-size.yml`     | PR → main (apps/ecommerce/**, pnpm-lock.yaml)                                                                                                      | Frontend bundle size report                                                                                                |

### Job dependency chain (ecommerce-ci.yml)

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
- Docker ensures all developers use the same environment, reducing "it works on my machine" issues.

---

## 📊 Diagrams

- [CI/CD Workflows](../diagrams/graphs/tooling/workflows.mmd)
- [Testing Strategy](../diagrams/graphs/tooling/testing.mmd)
- [Async Messaging](../diagrams/graphs/architecture/async-messaging.mmd)

---

See also: [Testing Tools](TESTS.md) · [Platform DevOps](../../../../docs/runtime/DEVOPS.md)
