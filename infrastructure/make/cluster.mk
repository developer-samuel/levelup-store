# ──────────────────────────────────────────────────────────────────────────────
# Cluster Status & Monitoring
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: cluster-pods cluster-apps cluster-nodes cluster-events \
        cluster-top-pods cluster-top-pods-cpu cluster-top-nodes cluster-top-ns cluster-pressure \
        cluster-pvc cluster-pv cluster-errors cluster-hpa cluster-vpa \
        cluster-rollout-restart cluster-rollout-status cluster-rollout-undo \
        cluster-logs cluster-logs-prev cluster-cp-logs \
        cluster-describe-pod cluster-describe-app \
        cluster-resources cluster-secrets cluster-configmaps \
        cluster-ingress cluster-services cluster-endpoints cluster-netpol \
        cluster-sa cluster-certs cluster-exec \
        cluster-namespaces cluster-crds cluster-images

# ── Status ────────────────────────────────────────────────────────────────────

cluster-pods: ## Show all pods across all namespaces
	kubectl get pods -A

cluster-apps: ## Show all ArgoCD applications with sync/health status
	kubectl get applications -n argocd

cluster-nodes: ## Show nodes with status and roles
	kubectl get nodes -o wide

cluster-events: ## Show recent cluster events sorted by time (last 40)
	kubectl get events -A --sort-by='.lastTimestamp' | tail -40

cluster-errors: ## Show only pods that are not Running or Completed
	@kubectl get pods -A | grep -vE 'Running|Completed|Terminating|NAME' || echo "All pods healthy."

cluster-pvc: ## Show all PersistentVolumeClaims and their status
	kubectl get pvc -A

cluster-hpa: ## Show all HorizontalPodAutoscalers with current/desired replicas
	kubectl get hpa -A

cluster-vpa: ## Show all VerticalPodAutoscalers with recommendations
	kubectl get vpa -A

# ── Resource Usage ────────────────────────────────────────────────────────────

cluster-top-pods: ## Show CPU/RAM usage per pod sorted by memory (highest first)
	kubectl top pods -A --sort-by=memory

cluster-top-pods-cpu: ## Show CPU/RAM usage per pod sorted by CPU (highest first)
	kubectl top pods -A --sort-by=cpu

cluster-top-nodes: ## Show CPU/RAM usage per node
	kubectl top nodes

cluster-pressure: ## Show node resource pressure conditions (MemoryPressure, DiskPressure, PIDPressure)
	kubectl get nodes -o custom-columns=\
'NODE:.metadata.name,MEMORY_PRESSURE:.status.conditions[?(@.type=="MemoryPressure")].status,DISK_PRESSURE:.status.conditions[?(@.type=="DiskPressure")].status,PID_PRESSURE:.status.conditions[?(@.type=="PIDPressure")].status,READY:.status.conditions[?(@.type=="Ready")].status'

cluster-top-ns: ## Show CPU/RAM usage summed per namespace
	kubectl top pods -A --no-headers \
		| awk '{ns[$$1]+=substr($$3,1,length($$3)-1); ram[$$1]+=substr($$4,1,length($$4)-2)} END {for (n in ns) printf "%-35s CPU: %5dm  RAM: %5dMi\n", n, ns[n], ram[n]}' \
		| sort -k5 -rn

cluster-resources: ## Show resource requests/limits per pod in a namespace - Usage: make cluster-resources NS=levelup-store
	$(call require,NS)
	kubectl get pods -n $(NS) -o custom-columns=\
'NAME:.metadata.name,CPU_REQ:.spec.containers[*].resources.requests.cpu,MEM_REQ:.spec.containers[*].resources.requests.memory,CPU_LIM:.spec.containers[*].resources.limits.cpu,MEM_LIM:.spec.containers[*].resources.limits.memory'

# ── Rollout ───────────────────────────────────────────────────────────────────

cluster-rollout-restart: ## Restart a deployment - Usage: make cluster-rollout-restart APP=levelup-store NS=levelup-store
	$(call require,APP)
	$(call require,NS)
	kubectl rollout restart deployment/$(APP) -n $(NS)
	kubectl rollout status deployment/$(APP) -n $(NS)

cluster-rollout-status: ## Watch rollout progress - Usage: make cluster-rollout-status APP=levelup-store NS=levelup-store
	$(call require,APP)
	$(call require,NS)
	kubectl rollout status deployment/$(APP) -n $(NS)

cluster-rollout-undo: ## Rollback deployment to previous version - Usage: make cluster-rollout-undo APP=levelup-store NS=levelup-store
	$(call require,APP)
	$(call require,NS)
	kubectl rollout undo deployment/$(APP) -n $(NS)
	kubectl rollout status deployment/$(APP) -n $(NS)

# ── Logs ──────────────────────────────────────────────────────────────────────

cluster-logs: ## Tail logs of a deployment (last 100 lines) - Usage: make cluster-logs APP=levelup-store NS=levelup-store
	$(call require,APP)
	$(call require,NS)
	kubectl logs -n $(NS) -l "app.kubernetes.io/instance=$(APP)" --tail=100 -f --max-log-requests=10

cluster-logs-prev: ## Show logs of previously crashed pod - Usage: make cluster-logs-prev APP=levelup-store NS=levelup-store
	$(call require,APP)
	$(call require,NS)
	kubectl logs -n $(NS) -l "app.kubernetes.io/instance=$(APP)" --previous --tail=100

# ── Debug ─────────────────────────────────────────────────────────────────────

cluster-describe-pod: ## Describe a specific pod - Usage: make cluster-describe-pod POD=podname NS=levelup-store
	$(call require,POD)
	$(call require,NS)
	kubectl describe pod $(POD) -n $(NS)

cluster-describe-app: ## Describe a deployment - Usage: make cluster-describe-app APP=levelup-store NS=levelup-store
	$(call require,APP)
	$(call require,NS)
	kubectl describe deployment $(APP) -n $(NS)

# ── Config ────────────────────────────────────────────────────────────────────

cluster-secrets: ## List secrets in a namespace - Usage: make cluster-secrets NS=levelup-store
	$(call require,NS)
	kubectl get secrets -n $(NS)

cluster-configmaps: ## List configmaps in a namespace - Usage: make cluster-configmaps NS=levelup-store
	$(call require,NS)
	kubectl get configmaps -n $(NS)

# ── Networking ────────────────────────────────────────────────────────────────

cluster-ingress: ## Show all Ingress rules and hostnames
	kubectl get ingress -A

cluster-services: ## Show services with ports in a namespace - Usage: make cluster-services NS=levelup-store
	$(call require,NS)
	kubectl get svc -n $(NS) -o wide

cluster-endpoints: ## Show endpoints in a namespace (debug DNS/connectivity) - Usage: make cluster-endpoints NS=levelup-store
	$(call require,NS)
	kubectl get endpoints -n $(NS)

cluster-netpol: ## Show all NetworkPolicies
	kubectl get networkpolicies -A

# ── Storage ───────────────────────────────────────────────────────────────────

cluster-pv: ## Show all PersistentVolumes with capacity and status
	kubectl get pv

# ── RBAC / Security ───────────────────────────────────────────────────────────

cluster-sa: ## List ServiceAccounts in a namespace - Usage: make cluster-sa NS=levelup-store
	$(call require,NS)
	kubectl get serviceaccounts -n $(NS)

cluster-certs: ## Show cert-manager Certificate objects with expiry and ready status
	kubectl get certificates -A 2>/dev/null || echo "cert-manager not installed."

# ── Exec ──────────────────────────────────────────────────────────────────────

cluster-exec: ## Open shell in a pod - Usage: make cluster-exec APP=levelup-store NS=levelup-store
	$(call require,APP)
	$(call require,NS)
	kubectl exec -it -n $(NS) \
		$$(kubectl get pod -n $(NS) -l "app.kubernetes.io/instance=$(APP)" -o jsonpath='{.items[0].metadata.name}') \
		-- sh 2>/dev/null || bash

cluster-namespaces: ## List all namespaces
	kubectl get namespaces

cluster-crds: ## List all CustomResourceDefinitions installed in the cluster
	kubectl get crds

cluster-images: ## List all container images running in a namespace - Usage: make cluster-images NS=levelup-store
	$(call require,NS)
	kubectl get pods -n $(NS) -o jsonpath='{range .items[*]}{.metadata.name}{"\t"}{range .spec.containers[*]}{.image}{"\n"}{end}{end}'

cluster-cp-logs: ## Download pod logs to a local file - Usage: make cluster-cp-logs APP=levelup-store NS=levelup-store
	$(call require,APP)
	$(call require,NS)
	kubectl logs -n $(NS) -l "app.kubernetes.io/instance=$(APP)" --tail=5000 > /tmp/$(APP)-$(NS).log
	@echo "✓ Logs saved to /tmp/$(APP)-$(NS).log"
