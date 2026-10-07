# ──────────────────────────────────────────────────────────────────────────────
# 🚀 ArgoCD Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: argocd-install argocd-configure argocd-repo-add argocd-bootstrap argocd-notifications \
        argocd-app-start argocd-app-down argocd-app-stop argocd-app-unsync \
        argocd-app-status argocd-app-sync argocd-app-pods argocd-app-logs argocd-app-vpa

## Install ArgoCD into cluster via Helm
argocd-install:
	$(call require_bin,helm,curl https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash)
	$(call require_bin,kubectl,https://kubernetes.io/docs/tasks/tools/)
	helm repo add argo https://argoproj.github.io/argo-helm
	helm repo update
	helm upgrade --install argocd helm/platform/argocd \
		--namespace argocd \
		--create-namespace \
		--set argo-cd.server.ingress.hostname=argocd.$(APP_DOMAIN) \
		--wait

## Set repoURL in kubernetes/apps/ from GITHUB_REPO_URL (.env) - run once before first git push
argocd-configure:
	$(call require,GITHUB_REPO_URL)
	@GITHUB_REPO_URL="$(REPO_URL)"; export GITHUB_REPO_URL; \
	for f in $$(find $(K8S_APPS_DIR) -name "*.yaml") kubernetes/root-app.yaml; do \
		envsubst '$$GITHUB_REPO_URL' < "$$f" > "$$f.tmp" && mv "$$f.tmp" "$$f"; \
	done
	@echo "✓ repoURL set in $(K8S_APPS_DIR)/ and kubernetes/root-app.yaml to: $(REPO_URL)"
	@echo "  Commit: git add kubernetes/ && git commit -m 'chore: set repo URL'"

## Add GitHub repo credentials to ArgoCD (required for private repos)
argocd-repo-add:
	$(call require,GITHUB_REPO_URL)
	$(call require,ATLANTIS_GH_TOKEN)
	$(call require,GITHUB_USERNAME)
	$(call argocd_login)
	argocd repo add $(REPO_URL) $(ARGOCD_FLAGS) \
		--username $(GITHUB_USERNAME) \
		--password $(ATLANTIS_GH_TOKEN)
	@echo "✓ GitHub repo added to ArgoCD."

## Deploy all ArgoCD Applications via App of Apps (one command)
argocd-bootstrap:
	$(call require,GITHUB_REPO_URL)
	@if grep -rl 'GITHUB_REPO_URL' $(K8S_APPS_DIR)/ > /dev/null 2>&1; then \
		echo "ERROR: yaml files contain unset repoURL."; \
		echo "  Run: make argocd-configure && git commit -m 'chore: set repo URL' && git push"; \
		exit 1; \
	fi
	# Root Application - ArgoCD deploys the rest automatically
	envsubst < kubernetes/root-app.yaml | kubectl apply --validate=false -f -
	@echo "✓ Bootstrap complete. ArgoCD deploys all apps automatically."
	@echo "  Watch: argocd app list"
	@echo "  After sync: make services-secrets && make secrets && make monitoring-secrets"

## Start an app - re-enable ArgoCD auto-sync and trigger sync - Usage: make argocd-app-start APP=jenkins
argocd-app-start:
	$(call require,APP)
	argocd app set $(APP) --sync-policy automated --self-heal $(ARGOCD_FLAGS)
	argocd app sync $(APP) $(ARGOCD_FLAGS)
	@echo "✓ $(APP): started."

## Stop an app completely - disable ArgoCD sync + scale pods to 0 - Usage: make argocd-app-down APP=jenkins NS=jenkins
argocd-app-down:
	$(call require,APP)
	$(call require,NS)
	argocd app set $(APP) --sync-policy none $(ARGOCD_FLAGS)
	kubectl scale deployment,statefulset,daemonset \
		-l "app.kubernetes.io/instance=$(APP)" \
		-n $(NS) --replicas=0 2>/dev/null || true
	@echo "✓ $(APP): down."

## Scale pods to 0 only (ArgoCD sync stays active) - Usage: make argocd-app-stop APP=jenkins NS=jenkins
argocd-app-stop:
	$(call require,APP)
	$(call require,NS)
	kubectl scale deployment,statefulset,daemonset \
		-l "app.kubernetes.io/instance=$(APP)" \
		-n $(NS) --replicas=0 2>/dev/null || true
	@echo "✓ $(APP): pods stopped."

## Disable ArgoCD auto-sync only (pods keep running) - Usage: make argocd-app-unsync APP=jenkins
argocd-app-unsync:
	$(call require,APP)
	argocd app set $(APP) --sync-policy none $(ARGOCD_FLAGS)
	@echo "✓ $(APP): auto-sync disabled."

## Show ArgoCD sync/health status - Usage: make argocd-app-status APP=jenkins
argocd-app-status:
	$(call require,APP)
	argocd app get $(APP) $(ARGOCD_FLAGS)

## Trigger ArgoCD sync (without changing sync policy) - Usage: make argocd-app-sync APP=jenkins
argocd-app-sync:
	$(call require,APP)
	argocd app sync $(APP) $(ARGOCD_FLAGS)

## Show pods for an app - Usage: make argocd-app-pods APP=jenkins NS=jenkins
argocd-app-pods:
	$(call require,APP)
	$(call require,NS)
	kubectl get pods -n $(NS) -l "app.kubernetes.io/instance=$(APP)"

## Tail logs for an app (last 100 lines, follow) - Usage: make argocd-app-logs APP=jenkins NS=jenkins
argocd-app-logs:
	$(call require,APP)
	$(call require,NS)
	kubectl logs -n $(NS) -l "app.kubernetes.io/instance=$(APP)" --tail=100 -f --max-log-requests=10

## Show VPA resource recommendations - Usage: make argocd-app-vpa APP=levelup-store NS=levelup-store
argocd-app-vpa:
	$(call require,APP)
	$(call require,NS)
	kubectl describe vpa $(APP) -n $(NS) 2>/dev/null || echo "No VPA found for $(APP)."

## Configure ArgoCD email notifications
argocd-notifications:
	$(call require,MAILER_PASS)
	kubectl create secret generic argocd-notifications-secret \
		--namespace argocd \
		--from-literal=email-password="$(MAILER_PASS)" \
		--dry-run=client -o yaml | kubectl apply --validate=false -f -
	envsubst < kubernetes/cluster/argocd-notifications-cm.yaml | kubectl apply --validate=false -f -
	@echo "✓ ArgoCD Notifications configured."
