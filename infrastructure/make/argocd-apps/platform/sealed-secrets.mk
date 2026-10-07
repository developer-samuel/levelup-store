# ── sealed-secrets ───────────────────────────────────────────────────────────

.PHONY: sealed-secrets-start sealed-secrets-down sealed-secrets-stop sealed-secrets-unsync \
        sealed-secrets-status sealed-secrets-sync sealed-secrets-pods sealed-secrets-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

sealed-secrets-start: ## Start sealed-secrets - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=sealed-secrets

sealed-secrets-down: ## Stop sealed-secrets completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=sealed-secrets NS=sealed-secrets

sealed-secrets-stop: ## Scale sealed-secrets pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=sealed-secrets NS=sealed-secrets

sealed-secrets-unsync: ## Disable ArgoCD auto-sync for sealed-secrets (pods keep running)
	$(MAKE) argocd-app-unsync APP=sealed-secrets

# ── Status ────────────────────────────────────────────────────────────────────

sealed-secrets-status: ## Show ArgoCD sync/health status for sealed-secrets
	$(MAKE) argocd-app-status APP=sealed-secrets

sealed-secrets-sync: ## Trigger ArgoCD sync for sealed-secrets (without changing sync policy)
	$(MAKE) argocd-app-sync APP=sealed-secrets

sealed-secrets-pods: ## Show pods for sealed-secrets
	$(MAKE) argocd-app-pods APP=sealed-secrets NS=sealed-secrets

sealed-secrets-logs: ## Tail logs for sealed-secrets (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=sealed-secrets NS=sealed-secrets
