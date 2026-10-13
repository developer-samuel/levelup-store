# Docker

## Setup

```bash
# First time setup - builds images, runs migrations and seed
make setup-build

# Full dev setup - setup + all dev services
make dev-setup-build
```

## Start / Stop

```bash
# Start all services (foreground)
make dev

# Stop all services
make dev-down

# Stop and clean volumes + networks
make dev-down-clean
```

## Rebuild

```bash
# Rebuild without cache - base images only
make build-cache

# Full rebuild + restart dev setup (with cache)
make dev-setup-restart-build

# Full rebuild + restart dev setup (without cache)
make dev-setup-restart-build-without-cache
```

## Recreate app containers

Use when `.env` changes must take effect. Recreates containers without rebuilding the image.

```bash
# Ecommerce
make recreate-app-ecommerce

# Assistant
make recreate-app-assistant
```

## Rebuild and recreate app containers

Use when code changes must take effect. Rebuilds the image and recreates containers.

```bash
# Ecommerce
make recreate-build-app-ecommerce

# Assistant
make recreate-build-app-assistant
```

## Logs

```bash
# Show logs of base services
make logs

# Show logs of all services (base + dev)
make logs-dev

# Show last 50 lines of setup container logs (ecommerce + assistant)
make logs-setup

# Follow setup logs live
make setup-watch
```

## Status

```bash
# Show status of all levelup containers
make status
```

## Jenkins (local CD)

```bash
# Build Jenkins image (includes yq and docker)
make jenkins-build

# Start Jenkins - http://localhost:8088
make jenkins

# Stop Jenkins
make jenkins-down

# Show initial admin password (first run only)
make jenkins-password
```
