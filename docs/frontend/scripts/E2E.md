# 🎭 Frontend: E2E Scripts

This file documents all end-to-end test frontend scripts defined in `package.json`.

---

### e2e

- **Command**: `pnpm e2e`
- **Purpose**: Builds assets first, then runs all Playwright E2E tests. Use for a full clean test run.

---

### e2e:fast

- **Command**: `pnpm e2e:fast`
- **Purpose**: Runs Playwright E2E tests without rebuilding assets first. Use when assets are already up to date.

---

### e2e:ui

- **Command**: `pnpm e2e:ui`
- **Purpose**: Builds assets first, then opens the Playwright UI for interactive test exploration and debugging.

---

### e2e:debug

- **Command**: `pnpm e2e:debug`
- **Purpose**: Runs Playwright tests in debug mode - opens inspector for step-by-step test execution.

---

### e2e:report

- **Command**: `pnpm e2e:report`
- **Purpose**: Opens the Playwright HTML report from `var/tools/playwright/html` in the browser.
