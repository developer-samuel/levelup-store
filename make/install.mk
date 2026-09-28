# ──────────────────────────────────────────────────────────────────────────────
# 📦 Install Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: install-php install-frontend install

## Install PHP dependencies (root + ecommerce)
install-php:
	@echo "📦 Installing PHP dependencies..."
	@if [ -d "node_modules" ] && command -v sudo > /dev/null 2>&1; then \
		sudo chown -R $$(id -u):$$(id -g) node_modules/; \
	fi
	composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=ext-opentelemetry
	cd apps/ecommerce && composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=ext-opentelemetry

## Install frontend dependencies (root + ecommerce + assistant/client)
install-frontend:
	@echo "📦 Installing frontend dependencies..."
	@if command -v pnpm > /dev/null 2>&1; then \
		pnpm install; \
	else \
		npm install; \
	fi

## Install all dependencies (PHP + frontend)
install: install-php install-frontend
