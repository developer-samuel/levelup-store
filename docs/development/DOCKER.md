# Docker

## Setup

```bash
# First time setup - builds images, runs migrations and seed
make setup-build
# or
docker compose --env-file apps/ecommerce/.env --env-file apps/assistant/.env --profile setup up --build

# Full dev setup - setup + all dev services
make dev-setup-build
```

## Start / Stop

```bash
# Start all services (foreground)
make dev

# Stop all services
make dev-down
# or
docker compose --env-file apps/ecommerce/.env --env-file apps/assistant/.env \
  -f docker-compose.yml -f docker-compose.dev.yml down

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

## Restart app containers

```bash
# Restart ecommerce containers (reloads ENV)
make restart-app-ecommerce
# or
docker compose --env-file apps/ecommerce/.env --env-file apps/assistant/.env restart ecommerce_app ecommerce_worker ecommerce_cron nginx

# Restart assistant containers (reloads ENV)
make restart-app-assistant
# or
docker compose --env-file apps/ecommerce/.env --env-file apps/assistant/.env restart assistant_app assistant_worker assistant_cron
```

## Logs

```bash
# Show logs of base services
make logs
# or
docker compose --env-file apps/ecommerce/.env --env-file apps/assistant/.env logs -f

# Show logs of all services (base + dev)
make logs-dev

# Show last 50 lines of setup container logs (ecommerce + assistant)
make logs-setup
# or
docker logs levelup_store_ecommerce_app_setup --tail 50
docker logs levelup_store_assistant_app_setup --tail 50

# Follow setup logs live
make setup-watch
```

## Status

```bash
# Show status of all levelup containers
make status
# or
docker ps -a --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep levelup
```

## Jenkins (local CD)

```bash
# Build Jenkins image (includes yq and docker)
make jenkins-build
# or
docker compose -f docker/compose/services/jenkins/jenkins.yml build

# Start Jenkins - http://localhost:8088
make jenkins
# or
docker compose -f docker/compose/services/jenkins/jenkins.yml up -d

# Stop Jenkins
make jenkins-down
# or
docker compose -f docker/compose/services/jenkins/jenkins.yml down

# Show initial admin password (first run only)
make jenkins-password
# or
docker exec levelup_store_jenkins cat /var/jenkins_home/secrets/initialAdminPassword
```
