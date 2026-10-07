# ── ecommerce (levelup-store) ────────────────────────────────────────────────

.PHONY: ecommerce-start ecommerce-down ecommerce-stop ecommerce-unsync \
        ecommerce-status ecommerce-sync ecommerce-pods ecommerce-logs ecommerce-vpa

# ── Lifecycle ─────────────────────────────────────────────────────────────────

ecommerce-start: ## Start ecommerce - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=levelup-store

ecommerce-down: ## Stop ecommerce completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=levelup-store NS=levelup-store

ecommerce-stop: ## Scale ecommerce pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=levelup-store NS=levelup-store

ecommerce-unsync: ## Disable ArgoCD auto-sync for ecommerce (pods keep running)
	$(MAKE) argocd-app-unsync APP=levelup-store

# ── Status ────────────────────────────────────────────────────────────────────

ecommerce-status: ## Show ArgoCD sync/health status for ecommerce
	$(MAKE) argocd-app-status APP=levelup-store

ecommerce-sync: ## Trigger ArgoCD sync for ecommerce (without changing sync policy)
	$(MAKE) argocd-app-sync APP=levelup-store

ecommerce-pods: ## Show pods for ecommerce
	$(MAKE) argocd-app-pods APP=levelup-store NS=levelup-store

ecommerce-logs: ## Tail logs for ecommerce (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=levelup-store NS=levelup-store

ecommerce-vpa: ## Show VPA resource recommendations for ecommerce
	$(MAKE) argocd-app-vpa APP=levelup-store NS=levelup-store
