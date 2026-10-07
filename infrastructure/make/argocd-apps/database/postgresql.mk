# ── postgresql ───────────────────────────────────────────────────────────────

.PHONY: postgresql-start postgresql-down postgresql-stop postgresql-unsync \
        postgresql-status postgresql-sync postgresql-pods postgresql-logs \
        postgresql-shell

# ── Lifecycle ─────────────────────────────────────────────────────────────────

postgresql-start: ## Start postgresql - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=postgresql

postgresql-down: ## Stop postgresql completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=postgresql NS=levelup-store

postgresql-stop: ## Scale postgresql pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=postgresql NS=levelup-store

postgresql-unsync: ## Disable ArgoCD auto-sync for postgresql (pods keep running)
	$(MAKE) argocd-app-unsync APP=postgresql

# ── Status ────────────────────────────────────────────────────────────────────

postgresql-status: ## Show ArgoCD sync/health status for postgresql
	$(MAKE) argocd-app-status APP=postgresql

postgresql-sync: ## Trigger ArgoCD sync for postgresql (without changing sync policy)
	$(MAKE) argocd-app-sync APP=postgresql

postgresql-pods: ## Show pods for postgresql
	$(MAKE) argocd-app-pods APP=postgresql NS=levelup-store

postgresql-logs: ## Tail logs for postgresql (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=postgresql NS=levelup-store

# ── App-specific ──────────────────────────────────────────────────────────────

postgresql-shell: ## Open psql shell in PostgreSQL pod
	kubectl exec -it -n levelup-store $$(kubectl get pod -n levelup-store -l 'app.kubernetes.io/name=postgresql' -o jsonpath='{.items[0].metadata.name}') -- psql -U $${POSTGRES_USER:-postgres}
