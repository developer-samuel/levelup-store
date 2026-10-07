# ── keda ─────────────────────────────────────────────────────────────────────

.PHONY: keda-start keda-down keda-stop keda-unsync \
        keda-status keda-sync keda-pods keda-logs \
        keda-scaledobjects keda-triggers

# ── Lifecycle ─────────────────────────────────────────────────────────────────

keda-start: ## Start keda - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=keda

keda-down: ## Stop keda completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=keda NS=keda

keda-stop: ## Scale keda pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=keda NS=keda

keda-unsync: ## Disable ArgoCD auto-sync for keda (pods keep running)
	$(MAKE) argocd-app-unsync APP=keda

# ── Status ────────────────────────────────────────────────────────────────────

keda-status: ## Show ArgoCD sync/health status for keda
	$(MAKE) argocd-app-status APP=keda

keda-sync: ## Trigger ArgoCD sync for keda (without changing sync policy)
	$(MAKE) argocd-app-sync APP=keda

keda-pods: ## Show pods for keda
	$(MAKE) argocd-app-pods APP=keda NS=keda

keda-logs: ## Tail logs for keda (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=keda NS=keda

# ── App-specific ──────────────────────────────────────────────────────────────

keda-scaledobjects: ## List all KEDA ScaledObjects with current/desired replicas
	kubectl get scaledobjects -A

keda-triggers: ## List all KEDA TriggerAuthentications
	kubectl get triggerauthentications -A
