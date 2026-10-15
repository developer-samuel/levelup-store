# ⚒️ Assistant Development

## 📦 App Commands

```bash
# Start the FastAPI dev server (uvicorn with --reload)
make assistant-run
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
