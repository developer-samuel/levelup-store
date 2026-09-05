# 🔍 Frontend: Lint Scripts

This file documents all linting frontend scripts defined in `package.json`.

---

### lint

- **Command**: `pnpm lint`
- **Purpose**: Runs ESLint on `assets/ts/` source files and reports violations.

---

### lint:fix

- **Command**: `pnpm lint:fix`
- **Purpose**: Runs ESLint on `assets/ts/` source files and automatically fixes fixable violations.

---

### lint:all

- **Command**: `pnpm lint:all`
- **Purpose**: Runs ESLint on both `assets/ts/` and `assets/tests/` and reports violations.

---

### lint:all:fix

- **Command**: `pnpm lint:all:fix`
- **Purpose**: Runs ESLint on both `assets/ts/` and `assets/tests/` and automatically fixes fixable violations.

---

### lint:report

- **Command**: `pnpm lint:report`
- **Purpose**: Runs ESLint on all source and test files and outputs a JSON report to `var/tools/eslint/report.json`. Errors do not fail the command (`|| true`).

---

### lint-scss

- **Command**: `pnpm lint-scss`
- **Purpose**: Runs Stylelint on `assets/scss/` and reports SCSS violations.

---

### lint-scss:fix

- **Command**: `pnpm lint-scss:fix`
- **Purpose**: Runs Stylelint on `assets/scss/` and automatically fixes fixable SCSS violations.
