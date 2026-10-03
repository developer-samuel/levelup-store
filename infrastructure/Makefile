# ── LevelUp Store - Infrastructure Makefile ───────────────────────────────────
#
# Prerequisites: terraform, ansible, helm, kubectl, argocd
#
# Copy .env.example to .env and fill in the values.
# Makefile loads .env automatically - no manual exports needed.
#
# Full bootstrap (from scratch):
#   make bootstrap
#
# Or step by step:
#   make tf-apply             → VM on OCI
#   make ansible-install      → install Ansible collections
#   make k3s-install          → k3s on VPS
#   make server-harden        → SSH hardening, fail2ban
#   make helm-deps-update     → download Helm chart dependencies
#   make cert-manager-install → Let's Encrypt TLS
#   make argocd-install       → ArgoCD
#   make argocd-repo-add      → add GitHub repo credentials to ArgoCD (private repo access)
#   make argocd-bootstrap     → root-app → ArgoCD deploys everything automatically
#   make velero-install       → configure Velero backups (after ArgoCD syncs)
#   make services-secrets     → K8s secrets for PostgreSQL, Redis, RabbitMQ...
#   make ecommerce-secrets    → production secrets for the ecommerce app
#   make assistant-secrets    → production secrets for the assistant app
#   make monitoring-secrets   → Grafana password + Alertmanager email
# ─────────────────────────────────────────────────────────────────────────────

include make/config.mk
include make/terraform.mk
include make/ansible.mk
include make/argocd.mk
include make/helm.mk
include make/secrets.mk
include make/vault.mk
include make/addons.mk
include make/k6.mk

# ── Definitions ───────────────────────────────────────────────────────────────

.PHONY: help check-deps install-deps bootstrap

# ── Targets ───────────────────────────────────────────────────────────────────

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sed 's/^.*Makefile://' \
		| sed 's/^.*\.mk://' \
		| sort \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-26s\033[0m %s\n", $$1, $$2}'

check-deps: ## Check all required tools are installed
	@which terraform    > /dev/null 2>&1 && echo "  ✓ terraform"    || echo "  ✗ terraform    → make install-deps"
	@which ansible      > /dev/null 2>&1 && echo "  ✓ ansible"      || echo "  ✗ ansible      → make install-deps"
	@which helm         > /dev/null 2>&1 && echo "  ✓ helm"         || echo "  ✗ helm         → make install-deps"
	@which kubectl      > /dev/null 2>&1 && echo "  ✓ kubectl"      || echo "  ✗ kubectl      → make install-deps"
	@which argocd       > /dev/null 2>&1 && echo "  ✓ argocd"       || echo "  ✗ argocd       → make install-deps"

install-deps: ## Install all required tools (terraform, ansible, helm, kubectl, argocd)
	$(call install_bin,terraform,\
		wget -O- https://apt.releases.hashicorp.com/gpg | sudo gpg --dearmor -o /usr/share/keyrings/hashicorp-archive-keyring.gpg && \
		echo "deb [signed-by=/usr/share/keyrings/hashicorp-archive-keyring.gpg] https://apt.releases.hashicorp.com $$(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/hashicorp.list && \
		sudo apt-get update && sudo apt-get install -y terraform)
	$(call install_bin,ansible,sudo apt-get update && sudo apt-get install -y ansible)
	$(call install_bin,helm,curl https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash)
	$(call install_bin,kubectl,\
		curl -LO "https://dl.k8s.io/release/$$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl" && \
		sudo install -o root -g root -m 0755 kubectl /usr/local/bin/kubectl && \
		rm kubectl)
	$(call install_bin,argocd,\
		curl -sSL https://github.com/argoproj/argo-cd/releases/latest/download/argocd-linux-amd64 -o /tmp/argocd && \
		sudo install -m 555 /tmp/argocd /usr/local/bin/argocd && \
		rm /tmp/argocd)
	@echo "✓ All tools installed. Run: make check-deps"

bootstrap: ## Full bootstrap from scratch (tf-apply → k3s → cert-manager → argocd → secrets)
	@if grep -rl 'GITHUB_REPO_URL' $(K8S_APPS_DIR)/ > /dev/null 2>&1; then \
		echo "ERROR: Before bootstrap, run once:"; \
		echo "  make argocd-configure && git commit -m 'chore: set repo URL' && git push"; \
		exit 1; \
	fi
	$(MAKE) tf-apply
	$(MAKE) ansible-install
	$(MAKE) k3s-install
	$(MAKE) server-harden
	$(MAKE) helm-deps-update
	$(MAKE) cert-manager-install
	$(MAKE) argocd-install
	$(MAKE) argocd-notifications
	$(MAKE) argocd-repo-add
	$(MAKE) argocd-bootstrap
	@echo "  Waiting for ArgoCD to sync all apps (this may take a few minutes)..."
	kubectl wait --for=condition=available deployment/argocd-server -n argocd --timeout=300s
	argocd app wait root --sync --health --timeout 300
	$(MAKE) services-secrets
	$(MAKE) secrets
	$(MAKE) monitoring-secrets
	$(MAKE) blackbox-install
	$(MAKE) velero-install
	@echo "✓ Bootstrap complete. App running at https://$(APP_DOMAIN)"
