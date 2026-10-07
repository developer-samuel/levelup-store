# ── external-secrets ─────────────────────────────────────────────────────────

.PHONY: external-secrets-start external-secrets-down external-secrets-stop external-secrets-unsync \
        external-secrets-status external-secrets-sync external-secrets-pods external-secrets-logs \
        external-secrets-stores external-secrets-sync-status

# ── Lifecycle ─────────────────────────────────────────────────────────────────

external-secrets-start: ## Start external-secrets - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=external-secrets

external-secrets-down: ## Stop external-secrets completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=external-secrets NS=external-secrets

external-secrets-stop: ## Scale external-secrets pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=external-secrets NS=external-secrets

external-secrets-unsync: ## Disable ArgoCD auto-sync for external-secrets (pods keep running)
	$(MAKE) argocd-app-unsync APP=external-secrets

# ── Status ────────────────────────────────────────────────────────────────────

external-secrets-status: ## Show ArgoCD sync/health status for external-secrets
	$(MAKE) argocd-app-status APP=external-secrets

external-secrets-sync: ## Trigger ArgoCD sync for external-secrets (without changing sync policy)
	$(MAKE) argocd-app-sync APP=external-secrets

external-secrets-pods: ## Show pods for external-secrets
	$(MAKE) argocd-app-pods APP=external-secrets NS=external-secrets

external-secrets-logs: ## Tail logs for external-secrets (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=external-secrets NS=external-secrets

# ── App-specific ──────────────────────────────────────────────────────────────

external-secrets-stores: ## List all SecretStores and ClusterSecretStores
	kubectl get clustersecretstores,secretstores -A 2>/dev/null

external-secrets-sync-status: ## Show sync status of all ExternalSecrets
	kubectl get externalsecrets -A
