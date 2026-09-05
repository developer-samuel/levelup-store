# 🔎 Frontend: TypeCheck Scripts

This file documents all TypeScript type-checking frontend scripts defined in `package.json`.

---

### type-check

- **Command**: `pnpm type-check`
- **Purpose**: Runs TypeScript compiler on `assets/ts/` without emitting files to catch type errors.

---

### type-check:test

- **Command**: `pnpm type-check:test`
- **Purpose**: Runs TypeScript compiler using `tsconfig.test.json` to type-check test files separately.

---

### type-check:all

- **Command**: `pnpm type-check:all`
- **Purpose**: Runs both `type-check` and `type-check:test` in sequence - full type-check coverage across source and tests.
