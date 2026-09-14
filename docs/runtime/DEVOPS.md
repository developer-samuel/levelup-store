# 🛠️ DevOps

This document describes **Docker services, CI/CD pipelines, and testing tools** for this project.
Everything is set up to make development smooth, automated, and maintainable.

## Principles

- **Automation First** - All builds, tests, and deployments are fully automated.
- **Fast Feedback** - Each pipeline provides immediate feedback on failures.
- **Clear & Maintainable** - All steps and services are easy to understand and modify.

---

## 🐳 Docker

The project uses two Dockerfiles:

| File | Purpose | Build context |
|------|---------|---------------|
| `docker/Dockerfile` | Local dev base image - PHP-FPM runtime only, app code is volume-mounted | `./docker` |
| `docker/Dockerfile.prod` | Production image - PHP-FPM + Nginx + full app code baked in | `.` (repo root) |

Production-specific configs live in `docker/_prod/`:
- `config/nginx/nginx.conf` - Nginx main config (non-root, temp paths under `/tmp`)
- `config/nginx/server.conf` - Nginx server block (port 8000, FastCGI → localhost:9000)
- `config/php/fpm-pool.conf` - PHP-FPM pool (non-root, TCP socket 127.0.0.1:9000)
- `scripts/run.sh` - Production entrypoint (starts php-fpm + nginx)

---

## 🧱 CI/CD Pipelines

All pipelines are implemented using GitHub Actions, ensuring automated builds, tests, and deploys.

#### Pipeline Details:

- **ci.yml** - Triggered on every push/PR to main. Runs PHP lint & static analysis, architecture check, assets lint, PHPUnit (PHP 8.2 / 8.3), Vitest, and Playwright E2E.
- **deploy.yml** - Triggered on every push to main (after CI passes). Builds the production Docker image (`Dockerfile.prod`), pushes to GHCR, signs with Sigstore, and updates `values.prod.yaml` - ArgoCD auto-syncs to Kubernetes.
- **release.yml** - Triggered on GitHub Release creation. Automatically prepends release notes to `CHANGELOG.md` and commits it to main.

Pipelines ensure early detection of issues and maintain a deployable state at all times.

---

## 🧪 Testing

Developers can run local testing tools to verify code before pushing:

**Backend**
- **PHPStan** - Static analysis (Level 10)
- **PHPMD** - Mess detection
- **Deptrac** - Architecture dependency enforcement
- **PHPUnit** - Unit, integration & feature tests

**Frontend**
- **TypeScript** - Type checking
- **ESLint / Stylelint** - Linting
- **Vitest** - Unit, integration & functional tests
- **Playwright** - End-to-end tests (auth flows)

Local testing mirrors CI/CD pipelines to prevent failing builds or broken deployments.

---

## 🎯 DevOps Guidelines

- Every push or pull request triggers pipelines automatically.
- Builds fail if deployments or tests fail.
- Immediate feedback is provided on any errors.
- Pipelines are standardized and fully documented for consistency.
- Docker ensures all developers use the same environment, reducing “it works on my machine” issues.

---

## 📊 Diagrams

- [Docker Services](../diagrams/graphs/devops/docker.mmd)
- [CI/CD Pipelines](../diagrams/graphs/devops/pipelines.mmd)
