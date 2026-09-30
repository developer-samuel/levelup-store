# 🔎 Frontend: TypeCheck Scripts

This file documents all TypeScript type-checking scripts defined in root `package.json`.  
Each command runs across both apps (ecommerce + assistant).

---

### type-check

- **Command**: `pnpm type-check` / `npm run type-check`
- **Purpose**: Runs `tsc --noEmit` for both apps - catches type errors without emitting output files.

---

### type-check:all

- **Command**: `pnpm type-check:all`
- **Purpose**: Same as `type-check` but also includes test files (`tsconfig.test.json`) for ecommerce.
