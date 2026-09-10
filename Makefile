# ──────────────────────────────────────────────────────────────────────────────
# 📝 Declare all phony targets to prevent conflicts with files
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: help install cache-clear serve setup \
        clean-all build-cache \
        setup-build \
        dev dev-build-force dev-down dev-down-clean \
        dev-setup-build dev-setup-restart-build dev-setup-restart-build-without-cache \
        logs logs-dev

# ──────────────────────────────────────────────────────────────────────────────
# 🐳 Docker Compose File References
# ──────────────────────────────────────────────────────────────────────────────

DC     = docker compose
DC_DEV = docker compose -f docker-compose.yml -f docker-compose.dev.yml

# ──────────────────────────────────────────────────────────────────────────────
# 📦 App Commands
# ──────────────────────────────────────────────────────────────────────────────

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sed 's/^.*Makefile://' \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-40s\033[0m %s\n", $$1, $$2}'

install: ## Install dependencies and build assets
	@echo "📦 Installing dependencies and building assets..."
	composer install
	@if command -v pnpm > /dev/null 2>&1; then \
		pnpm install && pnpm run build; \
	else \
		npm install && npm run build; \
	fi

cache-clear: ## Clear and warmup Symfony cache (flushes Redis if available)
	@echo "🧹 Clearing and warming up cache..."
	composer cache:clear
	composer cache:warmup
	@if command -v redis-cli > /dev/null 2>&1; then \
		redis-cli -h "$$REDIS_HOST" -p "$$REDIS_PORT" flushall; \
	fi

serve: ## Start local development servers (PHP + frontend)
	@echo "🚀 Starting local development servers..."
	composer serve &
	@if command -v pnpm > /dev/null 2>&1; then \
		pnpm dev; \
	else \
		npm run dev; \
	fi

setup: ## Full local setup: install dependencies + database + cache + serve
	$(MAKE) install
	composer db-setup
	$(MAKE) cache-clear
	$(MAKE) serve

# ──────────────────────────────────────────────────────────────────────────────
# 🐳 Docker Commands
# ──────────────────────────────────────────────────────────────────────────────

# ── 🚀 Base (prod-like) ──────────────────────────────────────────────────────

clean-all: ## Clean ALL containers and images (⚠️ destructive!)
	@echo "💣 WARNING: Cleaning ALL containers and images! Full reset!"
	docker ps -q | xargs -r docker stop
	docker ps -aq | xargs -r docker rm -f
	docker images -aq | xargs -r docker rmi -f

build-cache: ## Build/rebuild base images without cache
	@echo "🧹 Building base images without cache..."
	$(DC) build --no-cache

# ── 🛠️ Setup ─────────────────────────────────────────────────────────────────

setup-build: ## Build and start setup containers (first time or Dockerfile changes)
	@echo "🛠 Setup: Building and starting setup containers..."
	$(MAKE) dev-down
	$(DC) --profile setup up --build
	$(DC) up -d

# ── 💻 Development ───────────────────────────────────────────────────────────

dev: ## Start all services (base + dev) in foreground
	@echo "▶ Starting all services (base + dev) in foreground..."
	$(MAKE) dev-down
	$(DC_DEV) up

dev-build-force: ## Force rebuild all services (base + dev)
	@echo "🛠 Force rebuild of all services (base + dev)..."
	$(MAKE) dev-down
	$(DC_DEV) build --no-cache
	$(DC_DEV) up -d --force-recreate

dev-down: ## Stop all services (base + dev)
	@echo "⏹ Stopping all services (base + dev)..."
	$(DC_DEV) down

dev-down-clean: ## Stop and clean all services including volumes (base + dev)
	@echo "⏹ Cleaning all services and volumes (base + dev)..."
	$(DC_DEV) down --volumes --remove-orphans

# ── 🔧 Dev Setup ─────────────────────────────────────────────────────────────

dev-setup-build: ## Build and start setup containers + all dev services
	@echo "💻 Dev setup: Building and starting setup containers + dev services..."
	$(MAKE) dev-down
	$(DC_DEV) --profile setup up --build
	$(DC_DEV) up -d

dev-setup-restart-build: ## Rebuild setup containers + dev services (with cache)
	@echo "🔄 Dev setup: Restarting and rebuilding setup containers + dev services (with cache)..."
	$(MAKE) dev-down-clean
	$(MAKE) dev-setup-build

dev-setup-restart-build-without-cache: ## Rebuild setup containers + dev services (without cache)
	@echo "🧹 Dev setup: Restarting setup containers + dev services without cache..."
	$(MAKE) dev-down-clean
	$(DC_DEV) build --no-cache
	$(MAKE) dev-setup-build

# ── 🔍 Utility ───────────────────────────────────────────────────────────────

logs: ## Show logs of base services
	@echo "📜 Showing logs of base services..."
	$(DC) logs -f

logs-dev: ## Show logs of all services (base + dev)
	@echo "📜 Showing logs of all services (base + dev)..."
	$(DC_DEV) logs -f
