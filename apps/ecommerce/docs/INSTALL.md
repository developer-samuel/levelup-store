# 📦 Ecommerce Install

This file describes the **installation steps** for the ecommerce app on a fresh checkout.

---

## 1. Install Dependencies

```bash
# Quick start - installs dependencies, sets up database, clears cache and starts servers
make setup

# or step by step:

# Install PHP + frontend dependencies and build assets
make install

# Clear and warmup cache
make cache-clear

# Start local development servers (PHP built-in server + Vite HMR)
make serve
```

#### or manually:

```bash
cd apps/ecommerce
```

```bash
# Setup database (create + migrate + seed)
composer db-setup

# Clear and warmup cache
composer cache:clear
composer cache:warmup

# Start servers
composer serve &
pnpm dev
```

---

## 2. Run Application with Docker

First time setup (stops any running stack first, then runs DB/storage initialization, then starts all services):

**Base stack:**
```bash
make setup-build
```

**With all dev tools** (Vite, pgAdmin, Elasticvue, Mailpit, Dozzle, SonarQube):
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

---

## 🐳 Production Image

Build and test the production Docker image locally before pushing to `main`.
CI builds it automatically on every push to `main`, so these commands are for local verification only.

```bash
# Build production image locally
make build-prod
# or
docker build -f apps/ecommerce/docker/Dockerfile.prod -t levelup-store-ecommerce:prod-test .

# Verify production image has bin/console (run after build-prod)
make test-prod
# or
docker run --rm --entrypoint php levelup-store-ecommerce:prod-test -l /var/www/bin/console
```

---

✅ For full environment and configuration setup see [SETUP.md](SETUP.md).

---

See also: [Platform Install](../../../docs/INSTALL.md)
