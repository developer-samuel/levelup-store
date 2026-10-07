# ── reloader ─────────────────────────────────────────────────────────────────

.PHONY: reloader-start reloader-down reloader-stop reloader-unsync \
        reloader-status reloader-sync reloader-pods reloader-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

reloader-start: ## Start reloader - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=reloader

reloader-down: ## Stop reloader completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=reloader NS=reloader

reloader-stop: ## Scale reloader pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=reloader NS=reloader

reloader-unsync: ## Disable ArgoCD auto-sync for reloader (pods keep running)
	$(MAKE) argocd-app-unsync APP=reloader

# ── Status ────────────────────────────────────────────────────────────────────

reloader-status: ## Show ArgoCD sync/health status for reloader
	$(MAKE) argocd-app-status APP=reloader

reloader-sync: ## Trigger ArgoCD sync for reloader (without changing sync policy)
	$(MAKE) argocd-app-sync APP=reloader

reloader-pods: ## Show pods for reloader
	$(MAKE) argocd-app-pods APP=reloader NS=reloader

reloader-logs: ## Tail logs for reloader (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=reloader NS=reloader
