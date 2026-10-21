# Quality Tools

This document describes the **quality and static analysis tools** for the assistant app.  
All tools are designed to ensure **clean, maintainable, and consistent code** across backend and frontend.

---

## Backend

### 🛡️ Quality

- **Ruff (lint)** - Fast Python linter enforcing PEP 8, Pyflakes, isort, and modern Python syntax (`pyupgrade`).
- **Ruff (format)** - Code formatter (replaces Black).
- **Mypy (strict)** - Static type checker with `strict = true` - enforces type annotations on all functions.
- **Vulture** - Dead code detection (unused functions, variables, imports).

```bash
# Run all checks at once
make assistant-check

# or individually:
make assistant-lint
make assistant-format
make assistant-type-check
make assistant-dead-code
```

### 🧪 Tests

- **pytest** - unit, api, and integration tests
- **pytest-cov** - coverage reporting for `app/` and `jobs/`
- Integration tests require a running Docker environment (`make dev`)

```bash
# All tests in Docker (unit + api + integration)
make assistant-test

# All tests with coverage report
make assistant-test-coverage
```

Test structure mirrors the source layout:

```
tests/
  unit/         # app/ unit tests (mocked dependencies)
  api/          # HTTP endpoint tests via AsyncClient
  integration/  # repository tests against real DB (requires Docker)
  jobs/         # jobs/ unit tests (mocked dependencies)
```

See also: [Testing Tools](TESTS.md)

---

## Frontend

### 🔍 Type Checking

- **TypeScript Type Check** - Static type checking via `pnpm type-check`.  
  Runs `tsc --noEmit` - catches type errors without emitting output files.

### ✨ Linting & Formatting

- **ESLint + Prettier** - Linting and static analysis for TypeScript with Prettier formatting rules.
- **Prettier** - Code formatter for TypeScript, TSX, and CSS.

```bash
cd apps/assistant/client
```

```bash
pnpm type-check
pnpm lint
pnpm lint:fix
pnpm format
```

---

## 📊 Diagrams

- [Backend Quality Tools](../diagrams/graphs/tooling/backend.mmd)
- [Frontend Quality Tools](../diagrams/graphs/tooling/frontend.mmd)
