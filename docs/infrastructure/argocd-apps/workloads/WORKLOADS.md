# Workloads

> See also: [Makefile Command Reference](../../COMMANDS.md)


Main application workloads.

---

## ecommerce (ArgoCD app: `levelup-store`)

Symfony PHP ecommerce application.

**Namespace:** `levelup-store`

| Command            | Description                          |
|--------------------|--------------------------------------|
| `ecommerce-start`  | Re-enable ArgoCD sync + trigger sync |
| `ecommerce-down`   | Disable sync + scale pods to 0       |
| `ecommerce-stop`   | Scale pods to 0 (sync stays active)  |
| `ecommerce-unsync` | Disable ArgoCD sync only             |
| `ecommerce-status` | ArgoCD sync/health status            |
| `ecommerce-sync`   | Trigger ArgoCD sync                  |
| `ecommerce-pods`   | Show pods                            |
| `ecommerce-logs`   | Tail logs (last 100 lines, follow)   |
| `ecommerce-vpa`    | VPA resource recommendations         |

---

## assistant (ArgoCD app: `levelup-store-assistant`)

AI assistant - FastAPI + React + ChromaDB.

**Namespace:** `levelup-store-assistant`

| Command            | Description                          |
|--------------------|--------------------------------------|
| `assistant-start`  | Re-enable ArgoCD sync + trigger sync |
| `assistant-down`   | Disable sync + scale pods to 0       |
| `assistant-stop`   | Scale pods to 0 (sync stays active)  |
| `assistant-unsync` | Disable ArgoCD sync only             |
| `assistant-status` | ArgoCD sync/health status            |
| `assistant-sync`   | Trigger ArgoCD sync                  |
| `assistant-pods`   | Show pods                            |
| `assistant-logs`   | Tail logs (last 100 lines, follow)   |
| `assistant-vpa`    | VPA resource recommendations         |

---

## ollama

Local LLM inference used by the assistant.

**Namespace:** `levelup-store`

| Command                         | Description |
|---------------------------------|-------------|
| `ollama-start/down/stop/unsync` | Lifecycle   |
| `ollama-status/sync/pods/logs`  | Status      |

> Ollama API: `make pf-ollama` → http://localhost:11434
