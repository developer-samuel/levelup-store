# 🛠️ DevOps

This document describes **Docker setup and CI/CD pipelines** for the assistant app.

## Principles

- **Automation First** - All builds, tests, and deployments are fully automated.
- **Fast Feedback** - Each pipeline provides immediate feedback on failures.
- **Clear & Maintainable** - All steps and services are easy to understand and modify.

---

## 🐳 Docker

The assistant app uses a single Dockerfile:

| File                | Purpose                                                    | Build context     |
|---------------------|------------------------------------------------------------|-------------------|
| `docker/Dockerfile` | Production image - Python runtime + full app code baked in | `apps/assistant/` |

---

## 🧱 CI/CD Pipelines

All pipelines are implemented using GitHub Actions.
Python and Node versions are centralized as repository variables (`PYTHON_VERSION`, `NODE_VERSION`).

| Workflow                        | Trigger                                                                                                          | Description                                                                |
|---------------------------------|------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------|
| `assistant-ci.yml`              | push/PR → main (apps/assistant/**)                                                                               | Ruff lint + format check, Mypy type check, Vulture dead code, Docker build |
| `assistant-bundle-size.yml`     | PR → main (apps/assistant/client/**, pnpm-lock.yaml)                                                             | Frontend bundle size report                                                |
| `assistant-docker-validate.yml` | PR → main (apps/assistant/docker/**, .hadolint.yaml)                                                             | Validate Dockerfile with Hadolint                                          |
| `assistant-frontend-audit.yml`  | push/PR → main (apps/assistant/client/**), schedule Mon 02:00                                                    | Lighthouse (push/schedule), axe-core + pa11y accessibility (PR only)       |
| `assistant-sast.yml`            | push/PR → main (apps/assistant/app/**, apps/assistant/jobs/**, apps/assistant/client/src/**), schedule Mon 02:00 | CodeQL static security analysis                                            |
| `assistant-zap.yml`             | push → main (apps/assistant/app/**, apps/assistant/jobs/**), schedule Mon 02:00                                  | OWASP ZAP DAST baseline scan                                               |

### Job dependency chain (assistant-ci.yml)

```
lint
type-check      (all parallel, no dependencies)
dead-code
docker-build
```

All four jobs run fully in parallel - no gates, no required checks.

---

## 🎯 DevOps Guidelines

- Every push or pull request triggers pipelines automatically.
- Builds fail if deployments or tests fail.
- Immediate feedback is provided on any errors.

---

## 📊 Diagrams

- [CI/CD Workflows](../diagrams/graphs/tooling/workflows.mmd)

---

See also: [Platform DevOps](../../../../docs/runtime/DEVOPS.md)

