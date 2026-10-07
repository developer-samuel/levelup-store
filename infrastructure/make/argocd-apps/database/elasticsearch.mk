# ── elasticsearch ────────────────────────────────────────────────────────────

.PHONY: elasticsearch-start elasticsearch-down elasticsearch-stop elasticsearch-unsync \
        elasticsearch-status elasticsearch-sync elasticsearch-pods elasticsearch-logs \
        elasticsearch-health

# ── Lifecycle ─────────────────────────────────────────────────────────────────

elasticsearch-start: ## Start elasticsearch - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=elasticsearch

elasticsearch-down: ## Stop elasticsearch completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=elasticsearch NS=levelup-store

elasticsearch-stop: ## Scale elasticsearch pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=elasticsearch NS=levelup-store

elasticsearch-unsync: ## Disable ArgoCD auto-sync for elasticsearch (pods keep running)
	$(MAKE) argocd-app-unsync APP=elasticsearch

# ── Status ────────────────────────────────────────────────────────────────────

elasticsearch-status: ## Show ArgoCD sync/health status for elasticsearch
	$(MAKE) argocd-app-status APP=elasticsearch

elasticsearch-sync: ## Trigger ArgoCD sync for elasticsearch (without changing sync policy)
	$(MAKE) argocd-app-sync APP=elasticsearch

elasticsearch-pods: ## Show pods for elasticsearch
	$(MAKE) argocd-app-pods APP=elasticsearch NS=levelup-store

elasticsearch-logs: ## Tail logs for elasticsearch (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=elasticsearch NS=levelup-store

# ── App-specific ──────────────────────────────────────────────────────────────

elasticsearch-health: ## Show Elasticsearch cluster health
	kubectl exec -n levelup-store $$(kubectl get pod -n levelup-store -l 'app=elasticsearch-master' -o jsonpath='{.items[0].metadata.name}') -- curl -s http://localhost:9200/_cluster/health | jq .
