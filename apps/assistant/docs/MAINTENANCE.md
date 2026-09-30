# Maintenance

This document describes **dependency maintenance procedures** for the assistant app.  
It is intended for **existing projects**, not first-time setup.

⚠️ Important: Do NOT run these commands blindly in production. Always validate changes first.

---

## 1. Python Dependencies (uv)

#### Update dependencies

```bash
cd apps/assistant && uv sync --group dev
# or via make
make assistant-install
```

#### Post-update validation (required)

```bash
# Lint
make assistant-lint
# or
cd apps/assistant && uv run ruff check --cache-dir .cache/ruff app/

# Type check
make assistant-type-check
# or
cd apps/assistant && uv run mypy --explicit-package-bases --cache-dir .cache/mypy app/

# Dead code detection
make assistant-dead-code
# or
cd apps/assistant && uv run vulture app/ jobs/

# Run all checks at once
make assistant-check
```

- All checks must pass before committing dependency updates.
- Treat failures as blockers, not warnings.

⚠️ If any failures occur, fix them immediately or rollback `uv.lock` before committing.

---

## 2. Frontend Dependencies

```bash
cd apps/assistant/client
```

#### Update dependencies

```bash
pnpm update

# or npm
npm update
```

#### Post-update validation (required)

```bash
pnpm type-check
pnpm lint

# or npm
npm run type-check
npm run lint
```

#### Formatting (Optional)

```bash
pnpm format

# or npm
npm run format
```

⚠️ `format` may modify code automatically - review changes before committing.

---

## Commit Policy

When updating dependencies, always commit together:

- `pyproject.toml` + `uv.lock`
- `package.json` + `pnpm-lock.yaml`

Never update dependencies without corresponding validation.

⚠️ Dependency updates without validation are considered invalid changes.

---

See also: [Platform Maintenance](../../../docs/MAINTENANCE.md)
