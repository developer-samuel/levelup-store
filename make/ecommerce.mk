# ──────────────────────────────────────────────────────────────────────────────
# 🛒 Ecommerce Commands (Symfony/PHP)
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: install cache-clear routes \
        serve setup \
        build-prod test-prod

# ── 💻 Dev ────────────────────────────────────────────────────────────────────

## Install ecommerce + frontend dependencies and build assets
install:
	@echo "📦 Installing dependencies and building assets..."
	@if [ -d "node_modules" ] && command -v sudo > /dev/null 2>&1; then \
		sudo chown -R $$(id -u):$$(id -g) node_modules/; \
	fi
	cd apps/ecommerce && composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=ext-opentelemetry
	@if command -v pnpm > /dev/null 2>&1; then \
		pnpm install && pnpm build; \
	else \
		npm install && npm run build; \
	fi
	git config core.hooksPath .githooks
	git config blame.ignoreRevsFile .git-blame-ignore-revs
	@echo "✅ Git hooks enabled."

## Clear and warmup Symfony cache (flushes Redis if available)
cache-clear:
	@echo "🧹 Clearing and warming up cache..."
	cd apps/ecommerce && composer cache:clear
	cd apps/ecommerce && composer cache:warmup
	@if command -v redis-cli > /dev/null 2>&1; then \
		redis-cli -h "$$REDIS_HOST" -p "$$REDIS_PORT" flushall 2>/dev/null || true; \
	fi

## List all registered routes (name, method, path)
routes:
	cd apps/ecommerce && php bin/console debug:router

## Start local development servers (PHP + frontend)
serve:
	@echo "🚀 Starting local development servers..."
	cd apps/ecommerce && composer serve &
	@if command -v pnpm > /dev/null 2>&1; then \
		pnpm dev; \
	else \
		npm run dev; \
	fi

## Full local setup: install dependencies + permissions + database + serve
setup:
	$(MAKE) install
	$(MAKE) fix-permissions
	cd apps/ecommerce && composer db-setup
	$(MAKE) serve

# ── 🐳 Docker ─────────────────────────────────────────────────────────────────

## Build ecommerce production image locally (smoke test before push to main)
build-prod:
	docker build -f docker/Dockerfile.prod --target ecommerce -t levelup-store-ecommerce:prod-test .

## Verify ecommerce production image has bin/console (run after build-prod)
test-prod:
	docker run --rm --entrypoint php levelup-store-ecommerce:prod-test -l /var/www/bin/console
