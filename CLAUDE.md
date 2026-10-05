# CLAUDE.md

Project-specific instructions for Claude Code.

---

## Stack

- **Ecommerce** - PHP 8.3 (Symfony), TypeScript, SCSS, PostgreSQL, Redis, RabbitMQ, Elasticsearch, Mercure, MinIO, OpenTelemetry; event-driven via Symfony Messenger + domain events
- **Assistant** - Python (FastAPI), TypeScript (React), ChromaDB, Ollama, OpenTelemetry
- **Infrastructure** - Docker, Kubernetes (k3s), Helm, ArgoCD, Terraform (Atlantis), Ansible, Vault, Jenkins
- **Package manager** - `pnpm` (never `npm`) for JS/TS; `uv` for Python

---

## Architecture

Ecommerce follows **Hexagonal Architecture + DDD + CQRS**. See `apps/ecommerce/docs/runtime/ARCHITECTURE.md`.

Key rule: `Core/` never depends on `Infrastructure/` or `Presentation/` - only through `Ports/` contracts.

Layers: `Core/Domain` → `Core/Application` → `Core/Ports` ← `Infrastructure` / `Presentation` / `Adapters`

---

## Conventions

### PHP

- No redundant PHPDoc - only when type cannot be expressed in PHP (array shapes, generics like `string[]`, tuples)
- No method-level `@param`/`@return` if types are already in signature
- Enums over string literals
- `Abstract/` folders for base classes, `Shared/` for cross-cutting concerns
- Imports grouped and sorted alphabetically, use grouped `use` with `{}`

### Python

- Package manager: `uv` (never `pip` directly)
- Run tools via `uv run` (e.g. `uv run mypy`, `uv run ruff`)
- Type annotations required on all public functions
- No `# type: ignore` unless genuinely unavoidable - add a comment explaining why
- Config in `apps/assistant/app/config.py` via Pydantic `Settings`

### TypeScript / SCSS

- `_foldername` prefix for grouping folders (e.g. `_handlers`, `_services`)
- No prefix for concrete domain folders (e.g. `rating`, `review`)
- Use existing mixins - check `abstracts/mixins/` before writing inline CSS

### Git

- Conventional Commits enforced in CI (`validate-commits.yml`)
- Prefixes: `feat`, `fix`, `refactor`, `style`, `chore`, `docs`, `test`, `perf`, `ci`, `build`, `revert`
- `style:` = formatting/whitespace only (not CSS)
- `feat:` = new user-visible behavior
- `refactor:` = restructuring without behavior change

### Database

- Migrations grouped by domain segment (one file per segment)
- `down()` must drop tables in reverse order of `up()` (FK constraints)
- `countries` migration must run before `users` (FK dependency)
- Schema folders use singular domain names: `User`, `Order`, `Product`, `Review`

---

## Commands

### Docker

```bash
make dev              # Start dev environment
make setup-build      # Full setup (migrations + seed)
make fix-permissions  # Fix var/ permissions
make logs             # Tail logs
make status           # Container status
```

### Ecommerce

```bash
make install          # Install dependencies
make cache-clear      # Clear Symfony cache
make routes           # List routes
bin/run <cmd>         # Run any command inside the ecommerce Docker container
```

### Assistant

```bash
make assistant-check        # lint + format + type-check + dead-code
make assistant-type-check   # mypy
make assistant-lint         # ruff
```

### Git hooks

Both are enabled automatically by `make install`. To enable manually (run once):

```bash
git config core.hooksPath .githooks
git config blame.ignoreRevsFile .git-blame-ignore-revs
```

Hooks: `commit-msg` (commitlint), `pre-push` (PHPStan, PHP MD, Deptrac, mypy, TS type-check)

---

## What NOT to do

- Never use `npm` - always `pnpm`; never use `pip` directly - always `uv`
- Never add PHPDoc that duplicates type hints
- Never put business logic in `Infrastructure/` or `Presentation/`
- Never skip `down()` in migrations
- Never add comments explaining WHAT code does - only WHY if non-obvious
