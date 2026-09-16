# Maintenance

This document describes **dependency maintenance procedures**.
It is intended for **existing projects**, not first-time setup.

⚠️ Important: Do NOT run these commands blindly in production. Always validate changes first.

---

## 1. PHP Dependencies (Composer)

#### Update dependencies

```bash
composer update
```

#### Post-update validation (required)

```bash
composer php-unit
composer php-md
composer php-stan
```

- Run the full test suite to ensure no regressions were introduced.
- Static analysis must pass before committing dependency updates.
- Treat failures as blockers, not warnings.

⚠️ If any failures occur, fix them immediately or rollback `composer.lock` before committing.

## 2. Frontend Dependencies (pnpm / npm)

#### Update dependencies

```bash
# pnpm
pnpm update

# or npm
npm run update
```

#### Post-update validation (required)

```bash
# pnpm
pnpm vitest

# or npm
npm run vitest
```

- All frontend tests must pass after dependency updates
- If failures occur, resolve them before committing

⚠️ Consider running these commands in a separate branch or environment before merging to main.

#### Quality Checks (Optional)

These checks are recommended but not mandatory for dependency updates.

```bash
# TypeScript linting (ESLint)
pnpm lint
pnpm lint:fix

# or npm
npm run lint
npm run lint:fix
```

```bash
# SCSS linting (Stylelint)
pnpm lint-scss
pnpm lint-scss:fix

# or npm
npm run lint-scss
npm run lint-scss:fix
```

⚠️ `lint:fix` and `lint-scss:fix` may modify code automatically - review changes before committing.

---

## Commit Policy

When updating dependencies, always commit together:

- `composer.json` + `composer.lock`
- `package.json` + `pnpm-lock.yaml` or `package-lock.json`

Never update dependencies without corresponding test verification.

⚠️ Dependency updates without validation are considered invalid changes.
