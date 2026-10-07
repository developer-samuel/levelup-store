# ── jenkins ──────────────────────────────────────────────────────────────────

.PHONY: jenkins-start jenkins-down jenkins-stop jenkins-unsync \
        jenkins-status jenkins-sync jenkins-pods jenkins-logs

# ── Lifecycle ─────────────────────────────────────────────────────────────────

jenkins-start: ## Start jenkins - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=jenkins

jenkins-down: ## Stop jenkins completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=jenkins NS=jenkins

jenkins-stop: ## Scale jenkins pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=jenkins NS=jenkins

jenkins-unsync: ## Disable ArgoCD auto-sync for jenkins (pods keep running)
	$(MAKE) argocd-app-unsync APP=jenkins

# ── Status ────────────────────────────────────────────────────────────────────

jenkins-status: ## Show ArgoCD sync/health status for jenkins
	$(MAKE) argocd-app-status APP=jenkins

jenkins-sync: ## Trigger ArgoCD sync for jenkins (without changing sync policy)
	$(MAKE) argocd-app-sync APP=jenkins

jenkins-pods: ## Show pods for jenkins
	$(MAKE) argocd-app-pods APP=jenkins NS=jenkins

jenkins-logs: ## Tail logs for jenkins (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=jenkins NS=jenkins
