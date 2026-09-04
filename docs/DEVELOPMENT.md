# ⚒️ Development

## 📦 App Commands

```bash
# Full local setup: install dependencies + database + cache + serve
make setup

# Install dependencies and build assets
make install

# Clear and warmup cache (also flushes Redis if available)
make cache-clear

# Start local development servers (PHP + frontend)
make serve
```

## 🐳 Docker Commands

If you have a `Makefile` or want to manage Docker manually, these commands cover **all typical operations**:

### Core Commands

```bash
# Clean ALL containers and images (⚠️ destructive!)
make clean-all
# or
docker ps -q | xargs -r docker stop
docker ps -aq | xargs -r docker rm -f
docker images -aq | xargs -r docker rmi -f

# Build/rebuild base images without cache
make build-cache
# or
docker compose build --no-cache
```

### Setup Commands

```bash
# Build and start setup containers (first time or Dockerfile changes)
# Stops any running stack first to avoid stale unhealthy containers
make setup-build
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down
docker compose --profile setup up --build
docker compose up -d
```

### Development Commands

All services including dev tools - Vite, pgAdmin, Elasticvue, Mailpit, Dozzle, SonarQube.

```bash
# Start all services (base + dev) in foreground
# Stops any running stack first to avoid stale unhealthy containers
make dev
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down
docker compose -f docker-compose.yml -f docker-compose.dev.yml up

# Force rebuild all services (base + dev)
make dev-build-force
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down
docker compose -f docker-compose.yml -f docker-compose.dev.yml build --no-cache
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --force-recreate

# Stop all services (base + dev)
make dev-down
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down

# Stop and clean all services including volumes and orphan containers (base + dev)
make dev-down-clean
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down --volumes --remove-orphans
```

### Dev Setup Commands

```bash
# Build and start setup containers + all dev services (first time or Dockerfile changes)
# Stops any running stack first to avoid stale unhealthy containers
make dev-setup-build
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down
docker compose -f docker-compose.yml -f docker-compose.dev.yml --profile setup up --build
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# Clean and rebuild setup containers + dev services (with cache)
make dev-setup-restart-build
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down --volumes --remove-orphans
docker compose -f docker-compose.yml -f docker-compose.dev.yml --profile setup up --build
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d

# Clean and rebuild setup containers + dev services (without cache)
make dev-setup-restart-build-without-cache
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down --volumes --remove-orphans
docker compose -f docker-compose.yml -f docker-compose.dev.yml build --no-cache
docker compose -f docker-compose.yml -f docker-compose.dev.yml --profile setup up --build
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d
```

### Utility Commands

```bash
# Show logs of base services
make logs
# or
docker compose logs -f

# Show logs of all services (base + dev)
make logs-dev
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml logs -f
```

## 🩺 Health Check

Verify that all services (database, cache, mailer, Stripe, disk, wkhtmltopdf) are running correctly:

```
GET /api/dev/health-check
```

Example response:

```json
{
  "status": "ok",
  "database": "ok",
  "cache": "ok",
  "disk": "ok",
  "mailer": "ok",
  "stripe": "ok",
  "wkhtmltopdf": "ok"
}
```

> `wkhtmltopdf` returns `"disabled"` if `WKHTMLTOPDF_ENABLED=false` and does not affect the overall `status`.
