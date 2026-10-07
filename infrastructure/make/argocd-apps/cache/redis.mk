# ── redis ────────────────────────────────────────────────────────────────────

.PHONY: redis-start redis-down redis-stop redis-unsync \
        redis-status redis-sync redis-pods redis-logs \
        redis-cli

# ── Lifecycle ─────────────────────────────────────────────────────────────────

redis-start: ## Start redis - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=redis

redis-down: ## Stop redis completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=redis NS=levelup-store

redis-stop: ## Scale redis pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=redis NS=levelup-store

redis-unsync: ## Disable ArgoCD auto-sync for redis (pods keep running)
	$(MAKE) argocd-app-unsync APP=redis

# ── Status ────────────────────────────────────────────────────────────────────

redis-status: ## Show ArgoCD sync/health status for redis
	$(MAKE) argocd-app-status APP=redis

redis-sync: ## Trigger ArgoCD sync for redis (without changing sync policy)
	$(MAKE) argocd-app-sync APP=redis

redis-pods: ## Show pods for redis
	$(MAKE) argocd-app-pods APP=redis NS=levelup-store

redis-logs: ## Tail logs for redis (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=redis NS=levelup-store

# ── App-specific ──────────────────────────────────────────────────────────────

redis-cli: ## Open redis-cli in Redis pod
	kubectl exec -it -n levelup-store $$(kubectl get pod -n levelup-store -l 'app.kubernetes.io/name=redis' -o jsonpath='{.items[0].metadata.name}') -- redis-cli
