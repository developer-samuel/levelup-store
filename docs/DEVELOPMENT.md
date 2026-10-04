# ⚒️ Development

Platform-level development guide covering the shared Docker stack used by all apps.
For app-specific commands and health checks see the links at the bottom of this page.

---

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

# Stop and clean all services including volumes, orphan containers and networks (base + dev)
make dev-down-clean
# or
docker compose -f docker-compose.yml -f docker-compose.dev.yml down --volumes --remove-orphans
docker container prune -f
docker network prune -f
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

### CD Commands

```bash
# Build Jenkins image (first time or after Dockerfile changes)
make jenkins-build
# or
docker compose -f docker/compose/services/jenkins/jenkins.yml build

# Start Jenkins (http://localhost:8088)
make jenkins
# or
docker compose -f docker/compose/services/jenkins/jenkins.yml up -d

# Stop Jenkins
make jenkins-down
# or
docker compose -f docker/compose/services/jenkins/jenkins.yml down

# Show initial admin password (first run)
make jenkins-password
# or
docker exec levelup_store_jenkins cat /var/jenkins_home/secrets/initialAdminPassword
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

# Show last 50 lines of setup container logs (ecommerce + assistant)
make logs-setup
# or
docker logs levelup_store_ecommerce_app_setup --tail 50
docker logs levelup_store_assistant_app_setup --tail 50

# Watch setup container logs live (ecommerce + assistant)
make setup-watch
# or
docker logs levelup_store_ecommerce_app_setup -f &
docker logs levelup_store_assistant_app_setup -f

# Restart ecommerce app containers (app + worker + cron + nginx)
make restart-app-ecommerce
# or
docker compose --env-file apps/ecommerce/.env --env-file apps/assistant/.env restart ecommerce_app ecommerce_worker ecommerce_cron nginx

# Restart assistant app containers (app + worker + cron)
make restart-app-assistant
# or
docker compose --env-file apps/ecommerce/.env --env-file apps/assistant/.env restart assistant_app assistant_worker assistant_cron
```

---

See also: [Ecommerce Development](../apps/ecommerce/docs/DEVELOPMENT.md) · [Assistant Development](../apps/assistant/docs/DEVELOPMENT.md)

