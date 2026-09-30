# 🔍 Frontend: Lint Scripts

This file documents all linting frontend scripts defined in `package.json`.

---
```bash
cd apps/ecommerce
```


### lint

- **Command**: `pnpm lint`
- **Purpose**: Runs ESLint on `assets/ts/` source files and reports violations.

---
```bash
cd apps/ecommerce
```


### lint:fix

- **Command**: `pnpm lint:fix`
- **Purpose**: Runs ESLint on `assets/ts/` source files and automatically fixes fixable violations.

---
```bash
cd apps/ecommerce
```


### lint:all

- **Command**: `pnpm lint:all`
- **Purpose**: Runs ESLint on both `assets/ts/` and `tests/` and reports violations.

---
```bash
cd apps/ecommerce
```


### lint:all:fix

- **Command**: `pnpm lint:all:fix`
- **Purpose**: Runs ESLint on both `assets/ts/` and `tests/` and automatically fixes fixable violations.

---
```bash
cd apps/ecommerce
```


### lint:report

- **Command**: `pnpm lint:report`
- **Purpose**: Runs ESLint on all source and test files and outputs a JSON report to `var/tools/eslint/report.json`. Errors do not fail the command (`|| true`).

---
```bash
cd apps/ecommerce
```


### lint-scss

- **Command**: `pnpm lint-scss`
- **Purpose**: Runs Stylelint on `assets/scss/` and reports SCSS violations.

---
```bash
cd apps/ecommerce
```


### lint-scss:fix

- **Command**: `pnpm lint-scss:fix`
- **Purpose**: Runs Stylelint on `assets/scss/` and automatically fixes fixable SCSS violations.
