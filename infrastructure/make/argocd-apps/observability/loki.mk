# ── loki ─────────────────────────────────────────────────────────────────────

.PHONY: loki-start loki-down loki-stop loki-unsync \
        loki-status loki-sync loki-pods loki-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

loki-start: ## Start loki - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=loki

loki-down: ## Stop loki completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=loki NS=monitoring

loki-stop: ## Scale loki pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=loki NS=monitoring

loki-unsync: ## Disable ArgoCD auto-sync for loki (pods keep running)
	$(MAKE) argocd-app-unsync APP=loki

# ── Status ────────────────────────────────────────────────────────────────────

loki-status: ## Show ArgoCD sync/health status for loki
	$(MAKE) argocd-app-status APP=loki

loki-sync: ## Trigger ArgoCD sync for loki (without changing sync policy)
	$(MAKE) argocd-app-sync APP=loki

loki-pods: ## Show pods for loki
	$(MAKE) argocd-app-pods APP=loki NS=monitoring

loki-logs: ## Tail logs for loki (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=loki NS=monitoring
