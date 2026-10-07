# ── otel-collector ───────────────────────────────────────────────────────────

.PHONY: otel-collector-start otel-collector-down otel-collector-stop otel-collector-unsync \
        otel-collector-status otel-collector-sync otel-collector-pods otel-collector-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

otel-collector-start: ## Start otel-collector - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=otel-collector

otel-collector-down: ## Stop otel-collector completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=otel-collector NS=monitoring

otel-collector-stop: ## Scale otel-collector pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=otel-collector NS=monitoring

otel-collector-unsync: ## Disable ArgoCD auto-sync for otel-collector (pods keep running)
	$(MAKE) argocd-app-unsync APP=otel-collector

# ── Status ────────────────────────────────────────────────────────────────────

otel-collector-status: ## Show ArgoCD sync/health status for otel-collector
	$(MAKE) argocd-app-status APP=otel-collector

otel-collector-sync: ## Trigger ArgoCD sync for otel-collector (without changing sync policy)
	$(MAKE) argocd-app-sync APP=otel-collector

otel-collector-pods: ## Show pods for otel-collector
	$(MAKE) argocd-app-pods APP=otel-collector NS=monitoring

otel-collector-logs: ## Tail logs for otel-collector (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=otel-collector NS=monitoring
