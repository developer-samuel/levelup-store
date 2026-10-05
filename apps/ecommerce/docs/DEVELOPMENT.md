# ⚒️ Ecommerce Development

## 📦 App Commands

```bash
# Full local setup: install dependencies + database + cache + serve
make setup  # from project root
# or manually:
composer install --ignore-platform-req=ext-opentelemetry
pnpm install
composer db-setup

# Install dependencies, build assets and enable git hooks
make install  # from project root
# or manually:
composer install --ignore-platform-req=ext-opentelemetry
pnpm install
git config core.hooksPath .githooks
git config blame.ignoreRevsFile .git-blame-ignore-revs

# Clear and warmup cache (also flushes Redis if available)
make cache-clear
# or manually:
cd apps/ecommerce
php bin/console cache:clear
php bin/console cache:warmup

# List all registered routes (name, method, path)
make routes
# or manually:
cd apps/ecommerce && php bin/console debug:router

# Start local development servers (PHP + frontend)
make serve
# or manually:
cd apps/ecommerce
php -S 127.0.0.1:8000 -t public &
pnpm dev
```

---

## 🩺 Health Check

Verify that all services are running correctly:

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
  "rabbitmq": "ok",
  "elasticsearch": "ok",
  "minio": "ok",
  "mercure": "ok",
  "wkhtmltopdf": "ok"
}
```

> `wkhtmltopdf` returns `"disabled"` if `WKHTMLTOPDF_ENABLED=false` and does not affect the overall `status`.

---

See also: [Platform Development](../../../docs/DEVELOPMENT.md)
