# ──────────────────────────────────────────────────────────────────────────────
# 📝 Declare all phony targets to prevent conflicts with files
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: install cache-clear serve setup \
		up up-detached down down-clean clean-all build force build-force build-cache restart \
        setup-build setup-up setup-restart-build setup-restart-build-without-cache setup-restart \
        dev dev-detached dev-build dev-build-force dev-down dev-restart \
        dev-setup-build dev-setup-up dev-setup-restart dev-setup-restart-build dev-setup-restart-build-without-cache \
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

# Start base services in foreground
up:
	@echo "▶ Starting base services in foreground..."
	$(DC) up

# Start base services in background (detached)
up-detached:
	@echo "▶ Starting base services in detached mode..."
	$(DC) up -d

# Stop all services (base + dev)
down:
	@echo "⏹ Stopping all services..."
	$(DC_DEV) down

# Stop and clean all services including volumes and orphan containers
down-clean:
	@echo "⏹ Cleaning all services and volumes..."
	$(DC_DEV) down --volumes --remove-orphans

# Clean ALL containers and images (⚠️ destructive!)
clean-all:
	@echo "💣 WARNING: Cleaning ALL containers and images! Full reset!"
	docker ps -q | xargs -r docker stop
	docker ps -aq | xargs -r docker rm -f
	docker images -aq | xargs -r docker rmi -f

# Build/rebuild base images
build:
	@echo "🛠 Building base images (using cache)..."
	$(DC) build

# Force recreate base services detached
force:
	@echo "⚡ Force recreation of base services in detached mode..."
	$(DC) up -d --force-recreate

# Force rebuild base images and recreate services
build-force:
	@echo "🛠 Force rebuild of base images and recreation of services..."
	$(DC) build
	$(MAKE) force

# Build/rebuild base images without cache
build-cache:
	@echo "🧹 Building base images without cache..."
	$(DC) build --no-cache

# Restart base services
restart:
	@echo "🔄 Restarting base services..."
	$(MAKE) down-clean
	$(MAKE) up

# ── 🛠️ Setup ─────────────────────────────────────────────────────────────────

# Build and start setup containers (first time or Dockerfile changes)
setup-build:
	@echo "🛠 Setup: Building and starting setup containers..."
	$(DC) --profile setup up --build

# Start setup containers without rebuilding
setup-up:
	@echo "▶ Setup: Starting setup containers without rebuild..."
	$(DC) --profile setup up

# Clean and rebuild setup containers (with cache)
setup-restart-build:
	@echo "🔄 Setup: Restarting and rebuilding setup containers (with cache)..."
	$(MAKE) down-clean
	$(MAKE) setup-build

# Clean and rebuild setup containers (without cache)
setup-restart-build-without-cache:
	@echo "🧹 Setup: Restarting setup containers without cache..."
	$(MAKE) down-clean
	$(MAKE) build-cache
	$(MAKE) setup-build

# Restart setup containers
setup-restart:
	@echo "🔄 Setup: Restarting setup containers..."
	$(MAKE) down-clean
	$(MAKE) setup-up

# ── 💻 Development ───────────────────────────────────────────────────────────

# Start all services (base + dev) in foreground
dev:
	@echo "▶ Starting all services (base + dev) in foreground..."
	$(DC_DEV) up

# Start all services (base + dev) in background
dev-detached:
	@echo "▶ Starting all services (base + dev) in detached mode..."
	$(DC_DEV) up -d

# Build and start all services (base + dev)
dev-build:
	@echo "🛠 Building and starting all services (base + dev)..."
	$(DC_DEV) up --build

# Force rebuild all services (base + dev)
dev-build-force:
	@echo "🛠 Force rebuild of all services (base + dev)..."
	$(DC_DEV) build --no-cache
	$(DC_DEV) up -d --force-recreate

# Stop all services (base + dev)
dev-down:
	@echo "⏹ Stopping all services (base + dev)..."
	$(DC_DEV) down --volumes --remove-orphans

# Restart all services (base + dev)
dev-restart:
	@echo "🔄 Restarting all services (base + dev)..."
	$(MAKE) dev-down
	$(MAKE) dev

# ── 🔧 Dev Setup ─────────────────────────────────────────────────────────────

# Build and start setup containers + all dev services (first time or Dockerfile changes)
dev-setup-build:
	@echo "💻 Dev setup: Building and starting setup containers + dev services..."
	$(DC_DEV) --profile setup up --build
	$(DC_DEV) up -d

# Start setup containers + all dev services without rebuilding
dev-setup-up:
	@echo "▶ Dev setup: Starting setup containers + dev services without rebuild..."
	$(DC_DEV) --profile setup up
	$(DC_DEV) up -d

# Clean and rebuild setup containers + dev services (with cache)
dev-setup-restart-build:
	@echo "🔄 Dev setup: Restarting and rebuilding setup containers + dev services (with cache)..."
	$(MAKE) dev-down
	$(MAKE) dev-setup-build

# Clean and rebuild setup containers + dev services (without cache)
dev-setup-restart-build-without-cache:
	@echo "🧹 Dev setup: Restarting setup containers + dev services without cache..."
	$(MAKE) dev-down
	$(DC_DEV) build --no-cache
	$(MAKE) dev-setup-build

# Restart setup containers + dev services
dev-setup-restart:
	@echo "🔄 Dev setup: Restarting setup containers + dev services..."
	$(MAKE) dev-down
	$(MAKE) dev-setup-up

# ── 🔍 Utility ───────────────────────────────────────────────────────────────

# Show logs of base services
logs:
	@echo "📜 Showing logs of base services..."
	$(DC) logs -f

# Show logs of all services (base + dev)
logs-dev:
	@echo "📜 Showing logs of all services (base + dev)..."
	$(DC_DEV) logs -f
