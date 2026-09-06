# 📦 Install

This file describes the **installation steps** on a fresh checkout.

---

## 1. Install Dependencies

```bash
# Quick start - installs dependencies, sets up database, clears cache and starts servers
make setup

# or step by step:

# Install dependencies and build assets
make install

# Setup database (without Docker)
composer db-setup

# Clear cache (also flushes Redis if available)
make cache-clear
# or
composer cache:clear
composer cache:warmup
redis-cli -h "$REDIS_HOST" -p "$REDIS_PORT" flushall

# Start local development servers
make serve

# or manually (PHP + frontend separately):
composer serve

pnpm dev
# or
npm run dev
```

---

## 2. Run Application with Docker

First time setup (stops any running stack first, then runs DB/storage initialization, then starts all services):

**Production:**
```bash
make setup-build
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down
docker compose --profile setup up --build
docker compose up -d
```

**Development:**
```bash
make dev-setup-build
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down
docker compose -f docker-compose.yml -f docker-compose.dev.yml --profile setup up --build
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

Subsequent starts:

```bash
make dev
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml up
```

---

✅ This `INSTALL.md` is your **quick-start guide** for getting the project running.

- Dependency updates are handled as part of regular maintenance. See [MAINTENANCE.md](MAINTENANCE.md).
- For full environment and configuration setup see [SETUP.md](SETUP.md).
- For complete Docker and Makefile command reference see [DEVELOPMENT.md](DEVELOPMENT.md).
