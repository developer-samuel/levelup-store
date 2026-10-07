# ── vault ────────────────────────────────────────────────────────────────────

.PHONY: vault-start vault-down vault-stop vault-unsync \
        vault-status vault-sync vault-pods vault-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

vault-start: ## Start vault - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=vault

vault-down: ## Stop vault completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=vault NS=vault

vault-stop: ## Scale vault pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=vault NS=vault

vault-unsync: ## Disable ArgoCD auto-sync for vault (pods keep running)
	$(MAKE) argocd-app-unsync APP=vault

# ── Status ────────────────────────────────────────────────────────────────────

vault-status: ## Show ArgoCD sync/health status for vault
	$(MAKE) argocd-app-status APP=vault

vault-sync: ## Trigger ArgoCD sync for vault (without changing sync policy)
	$(MAKE) argocd-app-sync APP=vault

vault-pods: ## Show pods for vault
	$(MAKE) argocd-app-pods APP=vault NS=vault

vault-logs: ## Tail logs for vault (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=vault NS=vault
