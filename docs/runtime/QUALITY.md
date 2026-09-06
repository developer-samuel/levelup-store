# Quality Tools

This document describes the **quality, refactoring and statistics tools** for the project.  
All tools are designed to ensure **clean, maintainable, and consistent code** across backend and frontend.

---

## Backend

### 🛡️ Quality

These tools enforce architecture rules, detect issues early, and keep codebases healthy:

- **Deptrac** - Ensures proper layer dependencies and prevents forbidden coupling.
- **PHP MD (PHP Mess Detector)** - Detects potential problems, unused code, and complexity issues.
- **PHPStan (Level 10)** - Static analysis tool for catching type errors, invalid code, and potential bugs.
- **SonarQube** - Web dashboard for static analysis with persistent issue tracking, code smell detection, and quality gate enforcement. Run via `composer sonar`.

---

### 📊 Metrics / Statistics

These tools provide insights into code size, complexity, duplication, and overall project health:

- **PHPCPD** - Detects copy-pasted code and duplicate blocks to improve maintainability.
- **PHPLoc** - Counts lines of PHP code, measures project size, structure, and complexity.
- **PHP-Metrics** - Generates maintainability and complexity metrics for the codebase.
- **PDepend** - Analyzes dependencies and metrics for better architecture understanding.

---

### 🛠️ Refactoring

- **Rector** - Performs automated code refactoring and modernization.

---

### 🎨 Formatting

- **PHP CS Fixer** - Automatically formats PHP code to comply with defined coding standards.

---

### 🔍 Linting

- **Twig Lint** - Validates Twig templates for syntax errors via `composer lint:twig`.

---

## Frontend

### 🔍 Type Checking

- **TypeScript Type Check** - Static type checking via `pnpm type-check` / `npm run type-check`.  
  Runs `tsc --noEmit` - catches type errors without emitting output files.  
  Use `type-check:all` to also check test files.

### ✨ Linting & Formatting

- **ESLint + Prettier** - Linting and static analysis for TypeScript with Prettier formatting rules.  
  Run via `pnpm lint` / `npm run lint`. Use `lint:all` to include test files.
- **Stylelint** - Quality and style checks for SCSS stylesheets.  
  Run via `pnpm lint-scss` / `npm run lint-scss`.

### 📊 Metrics

- **SLOC (TS / SCSS)** - Counts lines of code for frontend assets to track growth and complexity trends.

---

## 📊 Diagrams

- [Backend Quality Tools](../diagrams/graphs/tools/backend.mmd)
- [Frontend Quality Tools](../diagrams/graphs/tools/frontend.mmd)
