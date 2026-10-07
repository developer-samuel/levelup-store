# ── rabbitmq ─────────────────────────────────────────────────────────────────

.PHONY: rabbitmq-start rabbitmq-down rabbitmq-stop rabbitmq-unsync \
        rabbitmq-status rabbitmq-sync rabbitmq-pods rabbitmq-logs \
        rabbitmq-queues

# ── Lifecycle ─────────────────────────────────────────────────────────────────

rabbitmq-start: ## Start rabbitmq - re-enable ArgoCD auto-sync and trigger sync
	$(MAKE) argocd-app-start APP=rabbitmq

rabbitmq-down: ## Stop rabbitmq completely - disable ArgoCD sync + scale pods to 0
	$(MAKE) argocd-app-down APP=rabbitmq NS=levelup-store

rabbitmq-stop: ## Scale rabbitmq pods to 0 (ArgoCD sync stays active)
	$(MAKE) argocd-app-stop APP=rabbitmq NS=levelup-store

rabbitmq-unsync: ## Disable ArgoCD auto-sync for rabbitmq (pods keep running)
	$(MAKE) argocd-app-unsync APP=rabbitmq

# ── Status ────────────────────────────────────────────────────────────────────

rabbitmq-status: ## Show ArgoCD sync/health status for rabbitmq
	$(MAKE) argocd-app-status APP=rabbitmq

rabbitmq-sync: ## Trigger ArgoCD sync for rabbitmq (without changing sync policy)
	$(MAKE) argocd-app-sync APP=rabbitmq

rabbitmq-pods: ## Show pods for rabbitmq
	$(MAKE) argocd-app-pods APP=rabbitmq NS=levelup-store

rabbitmq-logs: ## Tail logs for rabbitmq (last 100 lines, follow)
	$(MAKE) argocd-app-logs APP=rabbitmq NS=levelup-store

# ── App-specific ──────────────────────────────────────────────────────────────

rabbitmq-queues: ## List RabbitMQ queues with message counts
	kubectl exec -n levelup-store $$(kubectl get pod -n levelup-store -l 'app.kubernetes.io/name=rabbitmq' -o jsonpath='{.items[0].metadata.name}') -- rabbitmqctl list_queues name messages consumers
