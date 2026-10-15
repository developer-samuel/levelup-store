# ⚒️ Ecommerce Development

## 📦 App Commands

```bash
# Full local setup: install dependencies + database + cache + serve
make setup  # from project root

# Install dependencies, build assets and enable git hooks
make install  # from project root

# Clear and warmup cache (also flushes Redis if available)
make cache-clear

# List all registered routes (name, method, path)
make routes

# Start local development servers (PHP + frontend)
make serve
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
