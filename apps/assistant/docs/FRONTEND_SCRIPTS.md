# ⚡ Frontend Scripts Overview

Assistant frontend scripts defined in `apps/assistant/client/package.json`.

```bash
cd apps/assistant/client
```

---

### 🏗️ Build

| Script    | Command                            | Description                          |
|-----------|------------------------------------|--------------------------------------|
| `dev`     | `pnpm dev` / `npm run dev`         | Start Vite dev server with HMR       |
| `build`   | `pnpm build` / `npm run build`     | Production build via Vite            |
| `preview` | `pnpm preview` / `npm run preview` | Preview the production build locally |

---

### 🔎 Type Checking

| Script       | Command                                  | Description                         |
|--------------|------------------------------------------|-------------------------------------|
| `type-check` | `pnpm type-check` / `npm run type-check` | `tsc --noEmit` - catch type errors  |

---

### 🔍 Linting

| Script       | Command                              | Description                                       |
|--------------|--------------------------------------|---------------------------------------------------|
| `lint`       | `pnpm lint` / `npm run lint`         | ESLint check on `src/`                            |
| `lint:fix`   | `pnpm lint:fix` / `npm run lint:fix` | ESLint with auto-fix                              |
| `lint:report`| `pnpm lint:report`                   | ESLint JSON report → `reports/eslint.report.json` |

---

### 🎨 Formatting

| Script   | Command                          | Description                                  |
|----------|----------------------------------|----------------------------------------------|
| `format` | `pnpm format` / `npm run format` | Prettier auto-format `src/**/*.{ts,tsx,css}` |
