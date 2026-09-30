# 📦 Install

This file describes the **installation steps** on a fresh checkout.

---

## Ecommerce

```bash
# Quick start - installs dependencies, sets up database, clears cache and starts servers
make setup

# or step by step:
make install
make cache-clear
make serve
```

See: [Ecommerce Install](../apps/ecommerce/docs/INSTALL.md)

---

## Assistant

```bash
make assistant-install
make assistant-run
```

See: [Assistant Install](../apps/assistant/docs/INSTALL.md)

---

## Docker (both apps)

First time setup:

```bash
make setup-build
```

Development with all dev tools:

```bash
make dev-setup-build
```

Subsequent starts:

```bash
make dev
```

---

## WSL2 - File Permission Issues

Docker Desktop on WSL2 runs containers as `root`. Any files or directories created
by Docker inside bind-mounted paths (`node_modules`, `vendor`, `var`, `dist`)
end up owned by root on the host, which causes `Permission denied` errors when
running `pnpm`, `composer`, or similar tools directly on the host.

**Fix:**
```bash
make fix-permissions
```

Run this whenever you get `Permission denied` on files under the project root after
a Docker build or setup.

---

## Production Deployment

Push to `main` - ArgoCD auto-syncs the Helm releases to the K3s cluster.

---

✅ This `INSTALL.md` is your **quick-start guide** for getting the project running.

- For full environment and configuration setup see [SETUP.md](SETUP.md).
- For complete Docker and Makefile command reference see [DEVELOPMENT.md](DEVELOPMENT.md).
- Dependency updates are handled as part of regular maintenance. See [MAINTENANCE.md](MAINTENANCE.md).

---

See also: [Ecommerce Install](../apps/ecommerce/docs/INSTALL.md) · [Assistant Install](../apps/assistant/docs/INSTALL.md)
