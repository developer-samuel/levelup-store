# ── monitoring ───────────────────────────────────────────────────────────────

.PHONY: monitoring-start monitoring-down monitoring-stop monitoring-unsync \
        monitoring-status monitoring-sync monitoring-pods monitoring-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

monitoring-start: ## Start monitoring - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=monitoring

monitoring-down: ## Stop monitoring completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=monitoring NS=monitoring

monitoring-stop: ## Scale monitoring pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=monitoring NS=monitoring

monitoring-unsync: ## Disable ArgoCD auto-sync for monitoring (pods keep running)
	$(MAKE) argocd-app-unsync APP=monitoring

# ── Status ────────────────────────────────────────────────────────────────────

monitoring-status: ## Show ArgoCD sync/health status for monitoring
	$(MAKE) argocd-app-status APP=monitoring

monitoring-sync: ## Trigger ArgoCD sync for monitoring (without changing sync policy)
	$(MAKE) argocd-app-sync APP=monitoring

monitoring-pods: ## Show pods for monitoring
	$(MAKE) argocd-app-pods APP=monitoring NS=monitoring

monitoring-logs: ## Tail logs for monitoring (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=monitoring NS=monitoring
