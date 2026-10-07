# ── mercure ──────────────────────────────────────────────────────────────────

.PHONY: mercure-start mercure-down mercure-stop mercure-unsync \
        mercure-status mercure-sync mercure-pods mercure-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

mercure-start: ## Start mercure - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=mercure

mercure-down: ## Stop mercure completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=mercure NS=levelup-store

mercure-stop: ## Scale mercure pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=mercure NS=levelup-store

mercure-unsync: ## Disable ArgoCD auto-sync for mercure (pods keep running)
	$(MAKE) argocd-app-unsync APP=mercure

# ── Status ────────────────────────────────────────────────────────────────────

mercure-status: ## Show ArgoCD sync/health status for mercure
	$(MAKE) argocd-app-status APP=mercure

mercure-sync: ## Trigger ArgoCD sync for mercure (without changing sync policy)
	$(MAKE) argocd-app-sync APP=mercure

mercure-pods: ## Show pods for mercure
	$(MAKE) argocd-app-pods APP=mercure NS=levelup-store

mercure-logs: ## Tail logs for mercure (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=mercure NS=levelup-store
