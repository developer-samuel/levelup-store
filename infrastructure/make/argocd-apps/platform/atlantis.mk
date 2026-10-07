# ── atlantis ─────────────────────────────────────────────────────────────────

.PHONY: atlantis-start atlantis-down atlantis-stop atlantis-unsync \
        atlantis-status atlantis-sync atlantis-pods atlantis-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

atlantis-start: ## Start atlantis - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=atlantis

atlantis-down: ## Stop atlantis completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=atlantis NS=atlantis

atlantis-stop: ## Scale atlantis pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=atlantis NS=atlantis

atlantis-unsync: ## Disable ArgoCD auto-sync for atlantis (pods keep running)
	$(MAKE) argocd-app-unsync APP=atlantis

# ── Status ────────────────────────────────────────────────────────────────────

atlantis-status: ## Show ArgoCD sync/health status for atlantis
	$(MAKE) argocd-app-status APP=atlantis

atlantis-sync: ## Trigger ArgoCD sync for atlantis (without changing sync policy)
	$(MAKE) argocd-app-sync APP=atlantis

atlantis-pods: ## Show pods for atlantis
	$(MAKE) argocd-app-pods APP=atlantis NS=atlantis

atlantis-logs: ## Tail logs for atlantis (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=atlantis NS=atlantis
