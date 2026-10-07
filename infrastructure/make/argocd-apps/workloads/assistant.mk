# ── assistant (levelup-store-assistant) ──────────────────────────────────────

.PHONY: assistant-start assistant-down assistant-stop assistant-unsync \
        assistant-status assistant-sync assistant-pods assistant-logs assistant-vpa

# ── Lifecycle ─────────────────────────────────────────────────────────────────

assistant-start: ## Start assistant - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=levelup-store-assistant

assistant-down: ## Stop assistant completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=levelup-store-assistant NS=levelup-store-assistant

assistant-stop: ## Scale assistant pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=levelup-store-assistant NS=levelup-store-assistant

assistant-unsync: ## Disable ArgoCD auto-sync for assistant (pods keep running)
	$(MAKE) argocd-app-unsync APP=levelup-store-assistant

# ── Status ────────────────────────────────────────────────────────────────────

assistant-status: ## Show ArgoCD sync/health status for assistant
	$(MAKE) argocd-app-status APP=levelup-store-assistant

assistant-sync: ## Trigger ArgoCD sync for assistant (without changing sync policy)
	$(MAKE) argocd-app-sync APP=levelup-store-assistant

assistant-pods: ## Show pods for assistant
	$(MAKE) argocd-app-pods APP=levelup-store-assistant NS=levelup-store-assistant

assistant-logs: ## Tail logs for assistant (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=levelup-store-assistant NS=levelup-store-assistant

assistant-vpa: ## Show VPA resource recommendations for assistant
	$(MAKE) argocd-app-vpa APP=levelup-store-assistant NS=levelup-store-assistant
