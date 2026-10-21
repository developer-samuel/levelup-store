# 🧪 Testing Tools

This document describes the main **testing tools** used in the assistant app.  
Testing ensures code correctness, prevents regressions, and improves maintainability.

---

## Backend

- **pytest** - Unit, API, Integration, and Jobs testing for Python.  
  Run tests via `make assistant-test` or `make assistant-test-coverage`.  
  Tests are organized into **unit, api, integration, and jobs** folders according to their purpose.

- **pytest-cov** - Coverage reporting for `app/` and `jobs/`.

- **httpx** - HTTP client used in API tests via `AsyncClient` + `ASGITransport`.

Tests are structured to mirror the source layout:

```
tests/
  unit/         # app/ unit tests (mocked dependencies), mirrors app/ structure
  api/          # HTTP endpoint tests via AsyncClient
  integration/  # repository tests against real DB (requires Docker)
  jobs/         # jobs/ unit tests (mocked dependencies)
```

### Integration Tests - Database

Integration tests run against a dedicated `levelup_store_test` database - never the main `levelup_store`.  
They require a running Docker environment and auto-skip when the database is unavailable:

```bash
make dev
```

Credentials are configured in `.env.test`:

```env
DB_HOST=postgres
DB_DATABASE=levelup_store_test
DB_USERNAME=postgres
DB_PASSWORD=secret
```

---

## 📊 Diagrams

- [Testing Tools](../diagrams/graphs/tooling/testing.mmd)
