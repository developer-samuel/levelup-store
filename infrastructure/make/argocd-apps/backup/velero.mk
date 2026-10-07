# ── velero ───────────────────────────────────────────────────────────────────

.PHONY: velero-start velero-down velero-stop velero-unsync \
        velero-status velero-sync velero-pods velero-logs \
        velero-backups velero-schedules

# ── Lifecycle ─────────────────────────────────────────────────────────────────

velero-start: ## Start velero - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=velero

velero-down: ## Stop velero completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=velero NS=velero

velero-stop: ## Scale velero pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=velero NS=velero

velero-unsync: ## Disable ArgoCD auto-sync for velero (pods keep running)
	$(MAKE) argocd-app-unsync APP=velero

# ── Status ────────────────────────────────────────────────────────────────────

velero-status: ## Show ArgoCD sync/health status for velero
	$(MAKE) argocd-app-status APP=velero

velero-sync: ## Trigger ArgoCD sync for velero (without changing sync policy)
	$(MAKE) argocd-app-sync APP=velero

velero-pods: ## Show pods for velero
	$(MAKE) argocd-app-pods APP=velero NS=velero

velero-logs: ## Tail logs for velero (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=velero NS=velero

# ── App-specific ──────────────────────────────────────────────────────────────

velero-backups: ## List all Velero backups with status
	kubectl get backups -n velero

velero-schedules: ## List Velero backup schedules
	kubectl get schedules -n velero
