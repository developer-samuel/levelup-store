# ── kyverno-policies ─────────────────────────────────────────────────────────

.PHONY: kyverno-policies-start kyverno-policies-down kyverno-policies-stop kyverno-policies-unsync \
        kyverno-policies-status kyverno-policies-sync kyverno-policies-pods kyverno-policies-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

kyverno-policies-start: ## Start kyverno-policies - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=kyverno-policies

kyverno-policies-down: ## Stop kyverno-policies completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=kyverno-policies NS=kyverno

kyverno-policies-stop: ## Scale kyverno-policies pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=kyverno-policies NS=kyverno

kyverno-policies-unsync: ## Disable ArgoCD auto-sync for kyverno-policies (pods keep running)
	$(MAKE) argocd-app-unsync APP=kyverno-policies

# ── Status ────────────────────────────────────────────────────────────────────

kyverno-policies-status: ## Show ArgoCD sync/health status for kyverno-policies
	$(MAKE) argocd-app-status APP=kyverno-policies

kyverno-policies-sync: ## Trigger ArgoCD sync for kyverno-policies (without changing sync policy)
	$(MAKE) argocd-app-sync APP=kyverno-policies

kyverno-policies-pods: ## Show pods for kyverno-policies
	$(MAKE) argocd-app-pods APP=kyverno-policies NS=kyverno

kyverno-policies-logs: ## Tail logs for kyverno-policies (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=kyverno-policies NS=kyverno
