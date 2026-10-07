# Database

> See also: [Makefile Command Reference](../../COMMANDS.md)


**Namespace:** `levelup-store`

---

## postgresql

Primary relational database.

| Command | Description |
|---|---|
| `postgresql-start/down/stop/unsync` | Lifecycle |
| `postgresql-status/sync/pods/logs` | Status |
| `postgresql-shell` | Open psql shell in PostgreSQL pod |

---

## elasticsearch

Full-text search engine.

| Command | Description |
|---|---|
| `elasticsearch-start/down/stop/unsync` | Lifecycle |
| `elasticsearch-status/sync/pods/logs` | Status |
| `elasticsearch-health` | Show cluster health (green/yellow/red) |
