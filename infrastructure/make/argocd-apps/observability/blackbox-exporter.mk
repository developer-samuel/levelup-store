# ── blackbox-exporter ────────────────────────────────────────────────────────

.PHONY: blackbox-exporter-start blackbox-exporter-down blackbox-exporter-stop blackbox-exporter-unsync \
        blackbox-exporter-status blackbox-exporter-sync blackbox-exporter-pods blackbox-exporter-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

blackbox-exporter-start: ## Start blackbox-exporter - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=blackbox-exporter

blackbox-exporter-down: ## Stop blackbox-exporter completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=blackbox-exporter NS=monitoring

blackbox-exporter-stop: ## Scale blackbox-exporter pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=blackbox-exporter NS=monitoring

blackbox-exporter-unsync: ## Disable ArgoCD auto-sync for blackbox-exporter (pods keep running)
	$(MAKE) argocd-app-unsync APP=blackbox-exporter

# ── Status ────────────────────────────────────────────────────────────────────

blackbox-exporter-status: ## Show ArgoCD sync/health status for blackbox-exporter
	$(MAKE) argocd-app-status APP=blackbox-exporter

blackbox-exporter-sync: ## Trigger ArgoCD sync for blackbox-exporter (without changing sync policy)
	$(MAKE) argocd-app-sync APP=blackbox-exporter

blackbox-exporter-pods: ## Show pods for blackbox-exporter
	$(MAKE) argocd-app-pods APP=blackbox-exporter NS=monitoring

blackbox-exporter-logs: ## Tail logs for blackbox-exporter (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=blackbox-exporter NS=monitoring
