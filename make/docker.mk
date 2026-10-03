# ──────────────────────────────────────────────────────────────────────────────
# 🐳 Docker Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: clean-all build-cache setup-build \
        dev dev-build-force dev-down dev-down-clean \
        dev-setup-build dev-setup-restart-build dev-setup-restart-build-without-cache \
        restart-app-ecommerce restart-app-assistant \
        logs logs-dev logs-setup setup-watch status

ECOMMERCE_ENV := apps/ecommerce/.env
ASSISTANT_ENV := apps/assistant/.env

DC := docker compose --env-file $(ECOMMERCE_ENV) --env-file $(ASSISTANT_ENV)

DC_DEV := $(DC) \
	-f docker-compose.yml \
	-f docker-compose.dev.yml

# ── 🚀 Base ───────────────────────────────────────────────────────────────────

## Clean ALL containers and images (⚠️ destructive!)
clean-all:
	@echo "💣 WARNING: Cleaning ALL containers and images! Full reset!"
	docker ps -q | xargs -r docker stop
	docker ps -aq | xargs -r docker rm -f
	docker images -aq | xargs -r docker rmi -f

## Build/rebuild base images without cache
build-cache:
	@echo "🧹 Building base images without cache..."
	$(DC) build --no-cache

# ── 🛠️ Setup ──────────────────────────────────────────────────────────────────

## Build and start setup containers (first time or Dockerfile changes)
setup-build:
	@echo "🛠 Setup: Building and starting setup containers..."
	$(MAKE) dev-down
	-$(DC) --profile setup up --build
	$(DC) up -d

# ── 💻 Development ────────────────────────────────────────────────────────────

## Start all services (base + dev) in foreground
dev:
	@echo "▶ Starting all services (base + dev) in foreground..."
	$(MAKE) dev-down
	$(DC_DEV) up

dev-build-force:

dev-build-force:
	@echo "🛠 Force rebuild of all services (base + dev)..."
	$(MAKE) dev-down
	$(DC_DEV) build --no-cache
	$(DC_DEV) up -d --force-recreate

## Stop all services (base + dev)
dev-down:
	@echo "⏹ Stopping all services (base + dev)..."
	$(DC_DEV) down

## Stop and clean all services including volumes and networks (base + dev)
dev-down-clean:
	@echo "⏹ Cleaning all services, volumes and networks (base + dev)..."
	$(DC_DEV) down --volumes --remove-orphans
	docker container prune -f
	docker network prune -f

# ── 🔧 Dev Setup ──────────────────────────────────────────────────────────────

## Build and start setup containers + all dev services
dev-setup-build:
	@echo "💻 Dev setup: Building and starting setup containers + dev services..."
	$(MAKE) dev-down
	-$(DC_DEV) --profile setup up --build
	$(DC_DEV) up -d

## Rebuild setup containers + dev services (with cache)
dev-setup-restart-build:
	@echo "🔄 Dev setup: Restarting and rebuilding setup containers + dev services (with cache)..."
	$(MAKE) dev-down-clean
	$(MAKE) dev-setup-build

## Rebuild setup containers + dev services (without cache)
dev-setup-restart-build-without-cache:
	@echo "🧹 Dev setup: Restarting setup containers + dev services without cache..."
	$(MAKE) dev-down-clean
	$(DC_DEV) build --no-cache
	$(MAKE) dev-setup-build

# ── 🔄 Restart ────────────────────────────────────────────────────────────────

## Restart ecommerce app containers (reloads ENV)
restart-app-ecommerce:
	$(DC) restart ecommerce_app ecommerce_worker ecommerce_cron nginx

## Restart assistant app containers (reloads ENV)
restart-app-assistant:
	$(DC) restart assistant_app assistant_worker assistant_cron

# ── 🔍 Utility ────────────────────────────────────────────────────────────────

## Show logs of base services
logs:
	@echo "📜 Showing logs of base services..."
	$(DC) logs -f

## Show logs of all services (base + dev)
logs-dev:
	@echo "📜 Showing logs of all services (base + dev)..."
	$(DC_DEV) logs -f

## Show ecommerce + assistant setup logs - last 50 lines
logs-setup:
	@docker logs levelup_store_ecommerce_app_setup --tail 50 && \
	 docker logs levelup_store_assistant_app_setup --tail 50

## Follow ecommerce + assistant setup logs live
setup-watch:
	@docker logs levelup_store_ecommerce_app_setup -f &
	@docker logs levelup_store_assistant_app_setup -f

## Show status of all levelup containers
status:
	@docker ps -a --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | grep levelup
