# ── tempo ────────────────────────────────────────────────────────────────────

.PHONY: tempo-start tempo-down tempo-stop tempo-unsync \
        tempo-status tempo-sync tempo-pods tempo-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

tempo-start: ## Start tempo - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=tempo

tempo-down: ## Stop tempo completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=tempo NS=tempo

tempo-stop: ## Scale tempo pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=tempo NS=tempo

tempo-unsync: ## Disable ArgoCD auto-sync for tempo (pods keep running)
	$(MAKE) argocd-app-unsync APP=tempo

# ── Status ────────────────────────────────────────────────────────────────────

tempo-status: ## Show ArgoCD sync/health status for tempo
	$(MAKE) argocd-app-status APP=tempo

tempo-sync: ## Trigger ArgoCD sync for tempo (without changing sync policy)
	$(MAKE) argocd-app-sync APP=tempo

tempo-pods: ## Show pods for tempo
	$(MAKE) argocd-app-pods APP=tempo NS=tempo

tempo-logs: ## Tail logs for tempo (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=tempo NS=tempo
