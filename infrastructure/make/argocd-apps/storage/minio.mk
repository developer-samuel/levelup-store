# ── minio ────────────────────────────────────────────────────────────────────

.PHONY: minio-start minio-down minio-stop minio-unsync \
        minio-status minio-sync minio-pods minio-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

minio-start: ## Start minio - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=minio

minio-down: ## Stop minio completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=minio NS=levelup-store

minio-stop: ## Scale minio pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=minio NS=levelup-store

minio-unsync: ## Disable ArgoCD auto-sync for minio (pods keep running)
	$(MAKE) argocd-app-unsync APP=minio

# ── Status ────────────────────────────────────────────────────────────────────

minio-status: ## Show ArgoCD sync/health status for minio
	$(MAKE) argocd-app-status APP=minio

minio-sync: ## Trigger ArgoCD sync for minio (without changing sync policy)
	$(MAKE) argocd-app-sync APP=minio

minio-pods: ## Show pods for minio
	$(MAKE) argocd-app-pods APP=minio NS=levelup-store

minio-logs: ## Tail logs for minio (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=minio NS=levelup-store
