# ── cert-manager ─────────────────────────────────────────────────────────────

.PHONY: cert-manager-start cert-manager-down cert-manager-stop cert-manager-unsync \
        cert-manager-status cert-manager-sync cert-manager-pods cert-manager-logs \
        cert-manager-certs cert-manager-issuers

# ── Lifecycle ─────────────────────────────────────────────────────────────────

cert-manager-start: ## Start cert-manager - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=cert-manager

cert-manager-down: ## Stop cert-manager completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=cert-manager NS=cert-manager

cert-manager-stop: ## Scale cert-manager pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=cert-manager NS=cert-manager

cert-manager-unsync: ## Disable ArgoCD auto-sync for cert-manager (pods keep running)
	$(MAKE) argocd-app-unsync APP=cert-manager

# ── Status ────────────────────────────────────────────────────────────────────

cert-manager-status: ## Show ArgoCD sync/health status for cert-manager
	$(MAKE) argocd-app-status APP=cert-manager

cert-manager-sync: ## Trigger ArgoCD sync for cert-manager (without changing sync policy)
	$(MAKE) argocd-app-sync APP=cert-manager

cert-manager-pods: ## Show pods for cert-manager
	$(MAKE) argocd-app-pods APP=cert-manager NS=cert-manager

cert-manager-logs: ## Tail logs for cert-manager (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=cert-manager NS=cert-manager

# ── App-specific ──────────────────────────────────────────────────────────────

cert-manager-certs: ## List all certificates with expiry and ready status
	kubectl get certificates -A

cert-manager-issuers: ## List all ClusterIssuers and Issuers
	kubectl get clusterissuers,issuers -A 2>/dev/null
