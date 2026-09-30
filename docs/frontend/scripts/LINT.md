# 🔍 Frontend: Lint Scripts

This file documents all lint scripts defined in root `package.json`.  
Each command runs ESLint across both apps (ecommerce + assistant).

---

### lint

- **Command**: `pnpm lint` / `npm run lint`
- **Purpose**: ESLint check on source files for both apps.

---

### lint:fix

- **Command**: `pnpm lint:fix`
- **Purpose**: ESLint with auto-fix on source files for both apps.

---

### lint:all

- **Command**: `pnpm lint:all`
- **Purpose**: ESLint check including test files for both apps.

---

### lint:all:fix

- **Command**: `pnpm lint:all:fix`
- **Purpose**: ESLint with auto-fix including test files for both apps.

---

### lint:report

- **Command**: `pnpm lint:report`
- **Purpose**: ESLint JSON report output for both apps. Used in CI for SonarCloud analysis.
