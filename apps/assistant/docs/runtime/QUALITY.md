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

```bash
cd apps/assistant
```

```bash
# or manually:
uv run ruff check --cache-dir .cache/ruff app/
uv run ruff format --cache-dir .cache/ruff app/
uv run mypy --explicit-package-bases --cache-dir .cache/mypy app/
uv run vulture app/ jobs/
```

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
