# Maintenance

This document describes **dependency maintenance procedures** for the monorepo root.  
It is intended for **existing projects**, not first-time setup.

⚠️ Important: Do NOT run these commands blindly in production. Always validate changes first.

---

## 1. PHP Dependencies (Composer)

#### Update dependencies

```bash
composer update
```

Root `composer.json` manages only monorepo-level tools (code statistics).  
No post-update validation required beyond verifying the tools still run.

⚠️ If any failures occur, fix them immediately or rollback `composer.lock` before committing.

---

## 2. Frontend Dependencies

#### Update dependencies

```bash
pnpm update
```

#### Post-update validation (required)

```bash
# TypeScript linting (ESLint) - both apps
pnpm lint

# or npm
npm run lint
```

⚠️ Consider running these commands in a separate branch before merging to main.

---

## Commit Policy

When updating dependencies, always commit together:

- `composer.json` + `composer.lock`
- `package.json` + `pnpm-lock.yaml`

Never update dependencies without corresponding validation.

⚠️ Dependency updates without validation are considered invalid changes.

---

See also: [Ecommerce Maintenance](../apps/ecommerce/docs/MAINTENANCE.md) · [Assistant Maintenance](../apps/assistant/docs/MAINTENANCE.md)
