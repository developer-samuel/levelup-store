# ──────────────────────────────────────────────────────────────────────────────
# 🚀 ArgoCD Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: argocd-install argocd-configure argocd-repo-add argocd-bootstrap argocd-notifications

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
	envsubst < kubernetes/root-app.yaml | kubectl apply -f -
	@echo "✓ Bootstrap complete. ArgoCD deploys all apps automatically."
	@echo "  Watch: argocd app list"
	@echo "  After sync: make services-secrets && make secrets && make monitoring-secrets"

## Configure ArgoCD email notifications
argocd-notifications:
	$(call require,MAILER_PASS)
	kubectl create secret generic argocd-notifications-secret \
		--namespace argocd \
		--from-literal=email-password="$(MAILER_PASS)" \
		--dry-run=client -o yaml | kubectl apply -f -
	envsubst < kubernetes/cluster/argocd-notifications-cm.yaml | kubectl apply -f -
	@echo "✓ ArgoCD Notifications configured."
