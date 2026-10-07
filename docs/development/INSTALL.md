# Install

## Dependencies

```bash
# Install all dependencies (PHP + frontend) + enable git hooks
make install

# Install PHP dependencies only
make install-php

# Install frontend dependencies only (pnpm)
make install-frontend
```

## Git hooks

Hooks are enabled automatically by `make install`. To enable manually:

```bash
git config core.hooksPath .githooks
git config blame.ignoreRevsFile .git-blame-ignore-revs
```

Hooks: `commit-msg` (commitlint), `pre-push` (PHPStan, PHP MD, Deptrac, mypy, TS type-check).
