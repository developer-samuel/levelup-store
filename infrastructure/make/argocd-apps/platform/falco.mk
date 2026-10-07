# ── falco ────────────────────────────────────────────────────────────────────

.PHONY: falco-start falco-down falco-stop falco-unsync \
        falco-status falco-sync falco-pods falco-logs \
        falco-rules

# ── Lifecycle ─────────────────────────────────────────────────────────────────

falco-start: ## Start falco - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=falco

falco-down: ## Stop falco completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=falco NS=falco

falco-stop: ## Scale falco pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=falco NS=falco

falco-unsync: ## Disable ArgoCD auto-sync for falco (pods keep running)
	$(MAKE) argocd-app-unsync APP=falco

# ── Status ────────────────────────────────────────────────────────────────────

falco-status: ## Show ArgoCD sync/health status for falco
	$(MAKE) argocd-app-status APP=falco

falco-sync: ## Trigger ArgoCD sync for falco (without changing sync policy)
	$(MAKE) argocd-app-sync APP=falco

falco-pods: ## Show pods for falco
	$(MAKE) argocd-app-pods APP=falco NS=falco

falco-logs: ## Tail logs for falco (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=falco NS=falco

# ── App-specific ──────────────────────────────────────────────────────────────

falco-rules: ## List loaded Falco rules
	kubectl exec -n falco $$(kubectl get pod -n falco -l 'app.kubernetes.io/name=falco' -o jsonpath='{.items[0].metadata.name}') -- falco --list 2>/dev/null | head -40
