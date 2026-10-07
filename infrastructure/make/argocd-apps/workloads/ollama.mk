# ── ollama ───────────────────────────────────────────────────────────────────

.PHONY: ollama-start ollama-down ollama-stop ollama-unsync \
        ollama-status ollama-sync ollama-pods ollama-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

ollama-start: ## Start ollama - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=ollama

ollama-down: ## Stop ollama completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=ollama NS=levelup-store

ollama-stop: ## Scale ollama pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=ollama NS=levelup-store

ollama-unsync: ## Disable ArgoCD auto-sync for ollama (pods keep running)
	$(MAKE) argocd-app-unsync APP=ollama

# ── Status ────────────────────────────────────────────────────────────────────

ollama-status: ## Show ArgoCD sync/health status for ollama
	$(MAKE) argocd-app-status APP=ollama

ollama-sync: ## Trigger ArgoCD sync for ollama (without changing sync policy)
	$(MAKE) argocd-app-sync APP=ollama

ollama-pods: ## Show pods for ollama
	$(MAKE) argocd-app-pods APP=ollama NS=levelup-store

ollama-logs: ## Tail logs for ollama (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=ollama NS=levelup-store
