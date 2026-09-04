# ──────────────────────────────────────────────────────────────────────────────
# 📝 Declare all phony targets to prevent conflicts with files
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: install cache-clear serve setup \
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

# Install dependencies and build assets
install:
	@echo "📦 Installing dependencies and building assets..."
	composer install
	@if command -v pnpm > /dev/null 2>&1; then \
		pnpm install && pnpm run build; \
	else \
		npm install && npm run build; \
	fi

# Clear and warmup Symfony cache (flushes Redis if available)
cache-clear:
	@echo "🧹 Clearing and warming up cache..."
	composer cache:clear
	composer cache:warmup
	@if command -v redis-cli > /dev/null 2>&1; then \
		redis-cli -h "$$REDIS_HOST" -p "$$REDIS_PORT" flushall; \
	fi

# Start local development servers (PHP + frontend)
serve:
	@echo "🚀 Starting local development servers..."
	composer serve &
	@if command -v pnpm > /dev/null 2>&1; then \
		pnpm dev; \
	else \
		npm run dev; \
	fi

# Full local setup: install dependencies + database + cache + serve
setup:
	$(MAKE) install
	composer db-setup
	$(MAKE) cache-clear
	$(MAKE) serve

# ──────────────────────────────────────────────────────────────────────────────
# 🐳 Docker Commands
# ──────────────────────────────────────────────────────────────────────────────

# ── 🚀 Base (prod-like) ──────────────────────────────────────────────────────

# Clean ALL containers and images (⚠️ destructive!)
clean-all:
	@echo "💣 WARNING: Cleaning ALL containers and images! Full reset!"
	docker ps -q | xargs -r docker stop
	docker ps -aq | xargs -r docker rm -f
	docker images -aq | xargs -r docker rmi -f

# Build/rebuild base images without cache
build-cache:
	@echo "🧹 Building base images without cache..."
	$(DC) build --no-cache

# ── 🛠️ Setup ─────────────────────────────────────────────────────────────────

# Build and start setup containers (first time or Dockerfile changes)
setup-build:
	@echo "🛠 Setup: Building and starting setup containers..."
	$(MAKE) dev-down
	$(DC) --profile setup up --build
	$(DC) up -d

# ── 💻 Development ───────────────────────────────────────────────────────────

# Start all services (base + dev) in foreground
dev:
	@echo "▶ Starting all services (base + dev) in foreground..."
	$(MAKE) dev-down
	$(DC_DEV) up

# Force rebuild all services (base + dev)
dev-build-force:
	@echo "🛠 Force rebuild of all services (base + dev)..."
	$(MAKE) dev-down
	$(DC_DEV) build --no-cache
	$(DC_DEV) up -d --force-recreate

# Stop all services (base + dev)
dev-down:
	@echo "⏹ Stopping all services (base + dev)..."
	$(DC_DEV) down

# Stop and clean all services including volumes and orphan containers (base + dev)
dev-down-clean:
	@echo "⏹ Cleaning all services and volumes (base + dev)..."
	$(DC_DEV) down --volumes --remove-orphans

# ── 🔧 Dev Setup ─────────────────────────────────────────────────────────────

# Build and start setup containers + all dev services (first time or Dockerfile changes)
dev-setup-build:
	@echo "💻 Dev setup: Building and starting setup containers + dev services..."
	$(MAKE) dev-down
	$(DC_DEV) --profile setup up --build
	$(DC_DEV) up -d

# Clean and rebuild setup containers + dev services (with cache)
dev-setup-restart-build:
	@echo "🔄 Dev setup: Restarting and rebuilding setup containers + dev services (with cache)..."
	$(MAKE) dev-down-clean
	$(MAKE) dev-setup-build

# Clean and rebuild setup containers + dev services (without cache)
dev-setup-restart-build-without-cache:
	@echo "🧹 Dev setup: Restarting setup containers + dev services without cache..."
	$(MAKE) dev-down-clean
	$(DC_DEV) build --no-cache
	$(MAKE) dev-setup-build

# ── 🔍 Utility ───────────────────────────────────────────────────────────────

# Show logs of base services
logs:
	@echo "📜 Showing logs of base services..."
	$(DC) logs -f

# Show logs of all services (base + dev)
logs-dev:
	@echo "📜 Showing logs of all services (base + dev)..."
	$(DC_DEV) logs -f
