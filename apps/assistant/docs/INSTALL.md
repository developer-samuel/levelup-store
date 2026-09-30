# 📦 Assistant Install

This file describes the **installation steps** for the assistant app on a fresh checkout.

---

## 1. Install Dependencies

```bash
# Install Python + frontend dependencies
make assistant-install

# or manually:
cd apps/assistant && uv sync --group dev
cd apps/assistant/client && pnpm install
```

---

## 2. Run

```bash
# Start the FastAPI dev server (uvicorn with --reload)
make assistant-run

# or manually:
cd apps/assistant && uv run uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload
```

Frontend (Vite dev server):

```bash
cd apps/assistant/client && pnpm dev
```

---

## 3. Run with Docker

First time setup (stops any running stack first, then runs initialization):

```bash
make setup-build
```

With all dev tools:

```bash
make dev-setup-build
```

Subsequent starts:

```bash
make dev
```

---

✅ For environment variables and configuration setup see [SETUP.md](SETUP.md).

---

See also: [Platform Install](../../../docs/INSTALL.md)
