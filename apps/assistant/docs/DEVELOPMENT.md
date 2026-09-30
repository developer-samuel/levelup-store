# ⚒️ Assistant Development

## 📦 App Commands

```bash
# Start the API server (development mode with auto-reload)
make serve
# or manually:
cd apps/assistant
uv run uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload
```

---

## 🩺 Health Check

Verify that all services are running correctly:

```
GET /health
```

Example response:

```json
{
  "status": "ok",
  "services": {
    "postgres": true,
    "redis": true,
    "ollama": true,
    "chromadb": true
  }
}
```

> Returns `"status": "degraded"` and HTTP 503 if any service is unavailable.

---

See also: [Platform Development](../../../docs/DEVELOPMENT.md)
