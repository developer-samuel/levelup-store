# ⚡ Frontend Scripts Overview

Assistant frontend scripts defined in `apps/assistant/client/package.json`.

```bash
cd apps/assistant/client
```

---

### 🏗️ Build

| Script    | Command          | Description                          |
|-----------|------------------|--------------------------------------|
| `dev`     | `pnpm dev`       | Start Vite dev server with HMR       |
| `build`   | `pnpm build`     | Production build via Vite            |
| `preview` | `pnpm preview`   | Preview the production build locally |

---

### 🔎 Type Checking

| Script       | Command            | Description                        |
|--------------|--------------------|------------------------------------|
| `type-check` | `pnpm type-check`  | `tsc --noEmit` - catch type errors |

---

### 🔍 Linting

| Script        | Command           | Description                                       |
|---------------|-------------------|---------------------------------------------------|
| `lint`        | `pnpm lint`       | ESLint check on `src/`                            |
| `lint:fix`    | `pnpm lint:fix`   | ESLint with auto-fix                              |
| `lint:report` | `pnpm lint:report`| ESLint JSON report → `reports/eslint.report.json` |

---

### 🎨 Formatting

| Script   | Command       | Description                                  |
|----------|---------------|----------------------------------------------|
| `format` | `pnpm format` | Prettier auto-format `src/**/*.{ts,tsx,css}` |
