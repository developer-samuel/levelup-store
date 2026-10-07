# ── kyverno ──────────────────────────────────────────────────────────────────

.PHONY: kyverno-start kyverno-down kyverno-stop kyverno-unsync \
        kyverno-status kyverno-sync kyverno-pods kyverno-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

kyverno-start: ## Start kyverno - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=kyverno

kyverno-down: ## Stop kyverno completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=kyverno NS=kyverno

kyverno-stop: ## Scale kyverno pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=kyverno NS=kyverno

kyverno-unsync: ## Disable ArgoCD auto-sync for kyverno (pods keep running)
	$(MAKE) argocd-app-unsync APP=kyverno

# ── Status ────────────────────────────────────────────────────────────────────

kyverno-status: ## Show ArgoCD sync/health status for kyverno
	$(MAKE) argocd-app-status APP=kyverno

kyverno-sync: ## Trigger ArgoCD sync for kyverno (without changing sync policy)
	$(MAKE) argocd-app-sync APP=kyverno

kyverno-pods: ## Show pods for kyverno
	$(MAKE) argocd-app-pods APP=kyverno NS=kyverno

kyverno-logs: ## Tail logs for kyverno (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=kyverno NS=kyverno
