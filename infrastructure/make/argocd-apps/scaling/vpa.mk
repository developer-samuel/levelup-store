# ── vpa ──────────────────────────────────────────────────────────────────────

.PHONY: vpa-start vpa-down vpa-stop vpa-unsync \
        vpa-status vpa-sync vpa-pods vpa-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

vpa-start: ## Start vpa - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=vpa

vpa-down: ## Stop vpa completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=vpa NS=vpa

vpa-stop: ## Scale vpa pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=vpa NS=vpa

vpa-unsync: ## Disable ArgoCD auto-sync for vpa (pods keep running)
	$(MAKE) argocd-app-unsync APP=vpa

# ── Status ────────────────────────────────────────────────────────────────────

vpa-status: ## Show ArgoCD sync/health status for vpa
	$(MAKE) argocd-app-status APP=vpa

vpa-sync: ## Trigger ArgoCD sync for vpa (without changing sync policy)
	$(MAKE) argocd-app-sync APP=vpa

vpa-pods: ## Show pods for vpa
	$(MAKE) argocd-app-pods APP=vpa NS=vpa

vpa-logs: ## Tail logs for vpa (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=vpa NS=vpa
