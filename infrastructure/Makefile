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
#   make secrets              → production secrets for the app
#   make monitoring-secrets   → Grafana password + Alertmanager email
# ─────────────────────────────────────────────────────────────────────────────

# Loads root .env (one file for both app and infrastructure)
ifneq (,$(wildcard ../.env))
  include ../.env
  export
endif

# SSH public key - evaluated as shell command (not Makefile expression)
TF_VAR_ssh_public_key := $(shell cat $(ANSIBLE_SSH_KEY).pub 2>/dev/null)

# ── Definitions ──────────────────────────────────────────────────────────────

.PHONY: help check-deps install-deps bootstrap \
        tf-init tf-init-remote tf-plan tf-apply tf-destroy tf-output \
        ansible-install k3s-install server-harden \
        helm-deps-update cert-manager-install \
        argocd-install argocd-configure argocd-repo-add argocd-bootstrap \
        velero-secret velero-install \
        services-secrets secrets monitoring-secrets argocd-notifications \
        atlantis-secret atlantis-install blackbox-install \
        sealed-secrets-cert sealed-secrets-generate \
        k3s-upgrade-plan tf-lock

# ── Variables ─────────────────────────────────────────────────────────────────

# dirs
TERRAFORM_DIR := terraform/oracle
ANSIBLE_DIR   := ansible
K8S_APPS_DIR  := kubernetes/apps
# app
APP_NAME      := levelup-store
ARGOCD_SERVER ?= argocd.$(APP_DOMAIN)
ARGOCD_FLAGS  ?= --port-forward --port-forward-namespace argocd --insecure --grpc-web
# Strip .git suffix - ArgoCD requires URL without it
REPO_URL      := $(shell echo "$(GITHUB_REPO_URL)" | sed 's/\.git$$//')

# ── Macros ────────────────────────────────────────────────────────────────────

# require VAR - error if not set in .env - $(call require,VAR_NAME)
define require
  @test -n "$($(1))" || (echo "ERROR: $(1) not set in .env"; exit 1)
endef

define require_bin
  @which $(1) > /dev/null 2>&1 || (echo "ERROR: '$(1)' not installed. Install: $(2)"; exit 1)
endef

define install_bin
  @which $(1) > /dev/null 2>&1 || (echo "→ Installing $(1)..."; $(2))
endef

# argocd_login - authenticate against ArgoCD - $(call argocd_login)
# Tries domain first, falls back to --port-forward (no manual port-forward needed)
define argocd_login
  argocd login $(ARGOCD_SERVER) --grpc-web --username admin --password "$(ARGOCD_PASSWORD)" 2>/dev/null || \
  argocd login --port-forward --port-forward-namespace argocd --insecure --grpc-web --username admin --password "$(ARGOCD_PASSWORD)"
endef

# argocd_exec - run argocd command, auto port-forward if domain unreachable - $(call argocd_exec,ARGS)
define argocd_exec
  argocd $(1) --server $(ARGOCD_SERVER) --grpc-web 2>/dev/null || \
  argocd $(1) --port-forward --port-forward-namespace argocd --insecure --grpc-web
endef

# get_vm_ip - fetch public IP from Terraform into VM_IP - $(call get_vm_ip)
define get_vm_ip
  $(eval VM_IP := $(shell cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform output -raw public_ip))
endef

# ── Targets ───────────────────────────────────────────────────────────────────

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sed 's/^.*Makefile://' \
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
	$(MAKE) velero-install
	$(MAKE) services-secrets
	$(MAKE) secrets
	$(MAKE) monitoring-secrets
	$(MAKE) blackbox-install
	@echo "✓ Bootstrap complete. App running at https://$(APP_DOMAIN)"

# ── Terraform ─────────────────────────────────────────────────────────────────
# TF_VAR_* from infrastructure/.env is loaded by Terraform automatically - no -var-file needed
# Backend config: secrets/backend.config.hcl + secrets/backend.credentials.hcl (copy from *.example)

tf-lock: ## Generate .terraform.lock.hcl - run once and commit to git
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform providers lock \
		-platform=linux_arm64 \
		-platform=linux_amd64 \
		-platform=darwin_arm64
	@echo "✓ .terraform.lock.hcl updated. Commit: git add $(TERRAFORM_DIR)/.terraform.lock.hcl"

tf-init: ## Initialize Terraform - local state (default)
	$(call require_bin,terraform,https://developer.hashicorp.com/terraform/install)
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform init

tf-init-remote: ## Initialize Terraform - OCI Object Storage remote state
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform init \
		-backend-config=secrets/backend.config.hcl \
		-backend-config=secrets/backend.credentials.hcl

tf-plan: ## Terraform plan - shows what will change on OCI
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform plan

tf-apply: ## Terraform apply - creates VM on OCI
	$(call require_bin,terraform,https://developer.hashicorp.com/terraform/install)
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform apply

tf-import-velero: ## Import existing Velero OCI bucket into Terraform state
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform import oci_objectstorage_bucket.velero "n/$(OCI_NAMESPACE)/b/$(TF_VAR_velero_bucket)"

tf-destroy: ## Terraform destroy - deletes VM on OCI
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform destroy

tf-output: ## Show terraform outputs (e.g. public IP)
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform output

# ── Ansible ───────────────────────────────────────────────────────────────────

ansible-install: ## Install Ansible collections (run once before k3s-install)
	$(call require_bin,ansible-galaxy,sudo apt install -y ansible)
	cd $(ANSIBLE_DIR) && ansible-galaxy collection install -r requirements.yml

k3s-install: ## Install k3s on server via Ansible (IP from terraform output, key from .env)
	$(call require_bin,ansible-playbook,sudo apt install -y ansible)
	$(call get_vm_ip)
	cd $(ANSIBLE_DIR) && ansible-playbook \
		-i "$(VM_IP)," \
		-e "ansible_user=$(ANSIBLE_USER) ansible_ssh_private_key_file=$(ANSIBLE_SSH_KEY)" \
		playbooks/k3s.yml

server-harden: ## Harden server - SSH, fail2ban, auto security updates (IP from terraform output, key from .env)
	$(call require_bin,ansible-playbook,sudo apt install -y ansible)
	$(call get_vm_ip)
	cd $(ANSIBLE_DIR) && ansible-playbook \
		-i "$(VM_IP)," \
		-e "ansible_user=$(ANSIBLE_USER) ansible_ssh_private_key_file=$(ANSIBLE_SSH_KEY)" \
		playbooks/hardening.yml

# ── cert-manager ──────────────────────────────────────────────────────────────

cert-manager-install: ## Install cert-manager + ClusterIssuer (Let's Encrypt)
	$(call require_bin,helm,curl https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash)
	$(call require_bin,kubectl,https://kubernetes.io/docs/tasks/tools/)
	$(call require,MAILER_USER)
	helm repo add jetstack https://charts.jetstack.io
	helm repo update
	helm upgrade --install cert-manager helm/cert-manager \
		--namespace cert-manager \
		--create-namespace \
		--wait
	envsubst < kubernetes/cluster/cluster-issuer.yaml | kubectl apply -f -

# ── ArgoCD ────────────────────────────────────────────────────────────────────

argocd-install: ## Install ArgoCD into cluster via Helm
	$(call require_bin,helm,curl https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash)
	$(call require_bin,kubectl,https://kubernetes.io/docs/tasks/tools/)
	helm repo add argo https://argoproj.github.io/argo-helm
	helm repo update
	helm upgrade --install argocd helm/argocd \
		--namespace argocd \
		--create-namespace \
		--set argo-cd.server.ingress.hostname=argocd.$(APP_DOMAIN) \
		--wait

argocd-configure: ## Set repoURL in kubernetes/apps/ from GITHUB_REPO_URL (.env) - run once before first git push
	$(call require,GITHUB_REPO_URL)
	@GITHUB_REPO_URL="$(REPO_URL)"; export GITHUB_REPO_URL; \
	for f in $$(find $(K8S_APPS_DIR) -name "*.yaml") kubernetes/root-app.yaml; do \
		envsubst '$$GITHUB_REPO_URL' < "$$f" > "$$f.tmp" && mv "$$f.tmp" "$$f"; \
	done
	@echo "✓ repoURL set in $(K8S_APPS_DIR)/ and kubernetes/root-app.yaml to: $(REPO_URL)"
	@echo "  Commit: git add kubernetes/ && git commit -m 'chore: set repo URL'"

argocd-repo-add: ## Add GitHub repo credentials to ArgoCD (required for private repos)
	$(call require,GITHUB_REPO_URL)
	$(call require,ATLANTIS_GH_TOKEN)
	$(call require,GITHUB_USERNAME)
	$(call argocd_login)
	argocd repo add $(REPO_URL) \
		--username $(GITHUB_USERNAME) \
		--password $(ATLANTIS_GH_TOKEN)
	@echo "✓ GitHub repo added to ArgoCD."

argocd-bootstrap: ## Deploy all ArgoCD Applications via App of Apps (one command)
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

# ── Velero ────────────────────────────────────────────────────────────────────

velero-secret: ## Create Velero credentials secret from .env (OCI Customer Secret Keys)
	$(call require,VELERO_ACCESS_KEY)
	$(call require,VELERO_SECRET_KEY)
	kubectl create namespace velero --dry-run=client -o yaml | kubectl apply -f -
	kubectl create secret generic velero-credentials \
		--namespace velero \
		--from-literal=cloud="[default]
aws_access_key_id=$(VELERO_ACCESS_KEY)
aws_secret_access_key=$(VELERO_SECRET_KEY)" \
		--dry-run=client -o yaml | kubectl apply -f -

velero-install: ## Configure Velero OCI region/bucket/endpoint via ArgoCD (deployed by root-app)
	$(call require,VELERO_ACCESS_KEY)
	$(call require,VELERO_SECRET_KEY)
	$(call require,TF_VAR_velero_bucket)
	$(call require,VELERO_S3_URL)
	$(call require,TF_VAR_region)
	$(MAKE) velero-secret
	$(call argocd_login)
	argocd app set velero $(ARGOCD_FLAGS) \
		-p "velero.configuration.backupStorageLocation[0].bucket=$(TF_VAR_velero_bucket)" \
		-p "velero.configuration.backupStorageLocation[0].config.region=$(TF_VAR_region)" \
		-p "velero.configuration.backupStorageLocation[0].config.s3Url=$(VELERO_S3_URL)" \
		-p "velero.configuration.volumeSnapshotLocation[0].config.region=$(TF_VAR_region)"
	@echo "✓ Velero deployed. Backups: kubectl get backup -n velero"

# ── Secrets ───────────────────────────────────────────────────────────────────

services-secrets: ## Create K8s secrets for standalone service charts + set RabbitMQ username
	$(call require,APP_DOMAIN)
	$(call require,DB_PASSWORD)
	$(call require,REDIS_PASSWORD)
	$(call require,RABBITMQ_USER)
	$(call require,RABBITMQ_PASS)
	$(call require,RABBITMQ_ERLANG_COOKIE)
	$(call require,MINIO_ROOT_USER)
	$(call require,MINIO_ROOT_PASSWORD)
	$(call require,MERCURE_JWT_SECRET)
	kubectl create namespace levelup-store --dry-run=client -o yaml | kubectl apply -f -
	kubectl create secret generic levelup-store-redis-secret \
		--namespace levelup-store \
		--from-literal=redis-password="$(REDIS_PASSWORD)" \
		--dry-run=client -o yaml | kubectl apply -f -
	kubectl create secret generic levelup-store-postgresql-secret \
		--namespace levelup-store \
		--from-literal=postgres-password="$(DB_PASSWORD)" \
		--dry-run=client -o yaml | kubectl apply -f -
	kubectl create secret generic levelup-store-rabbitmq-secret \
		--namespace levelup-store \
		--from-literal=rabbitmq-password="$(RABBITMQ_PASS)" \
		--from-literal=rabbitmq-erlang-cookie="$(RABBITMQ_ERLANG_COOKIE)" \
		--dry-run=client -o yaml | kubectl apply -f -
	kubectl create secret generic levelup-store-minio-secret \
		--namespace levelup-store \
		--from-literal=root-user="$(MINIO_ROOT_USER)" \
		--from-literal=root-password="$(MINIO_ROOT_PASSWORD)" \
		--dry-run=client -o yaml | kubectl apply -f -
	kubectl create secret generic levelup-store-mercure-secret \
		--namespace levelup-store \
		--from-literal=publisher-jwt-key="$(MERCURE_JWT_SECRET)" \
		--from-literal=subscriber-jwt-key="$(MERCURE_JWT_SECRET)" \
		--from-literal=mercure-cors-allowed-origins="https://$(APP_DOMAIN)" \
		--dry-run=client -o yaml | kubectl apply -f -
	$(call argocd_login)
	argocd app set rabbitmq $(ARGOCD_FLAGS) \
		-p rabbitmq.auth.username="$(RABBITMQ_USER)"
	@echo "✓ K8s secrets created and RabbitMQ username set."

secrets: ## Set production secrets for levelup-store app via ArgoCD
	$(call require,APP_DOMAIN)
	$(call require,GHCR_IMAGE)
	$(call require,APP_SECRET)
	$(call require,HMAC_SECRET)
	$(call require,JWT_PASSPHRASE)
	$(call require,STRIPE_SECRET)
	$(call require,DB_PASSWORD)
	$(call require,REDIS_PASSWORD)
	$(call require,RABBITMQ_PASS)
	$(call require,MINIO_ROOT_PASSWORD)
	$(call require,MERCURE_JWT_SECRET)
	$(call require,TURNSTILE_SITE_KEY)
	$(call require,TURNSTILE_SECRET_KEY)
	$(call argocd_login)
	argocd app set $(APP_NAME) $(ARGOCD_FLAGS) \
		-p app.image.repository="$(GHCR_IMAGE)" \
		-p app.secret="$(APP_SECRET)" \
		-p app.hmacSecret="$(HMAC_SECRET)" \
		-p app.jwtPassphrase="$(JWT_PASSPHRASE)" \
		-p app.stripeSecret="$(STRIPE_SECRET)" \
		-p app.url="https://$(APP_DOMAIN)" \
		-p app.corsAllowOrigin="https://$(APP_DOMAIN)" \
		-p app.wkhtmltopdfPath="$(WKHTMLTOPDF_PATH)" \
		-p app.apiCountryUrl="$(API_COUNTRY_URL)" \
		-p app.mailerDsn="$(MAILER_DSN)" \
		-p app.sentryDsn="$(SENTRY_DSN)" \
		-p postgresql.auth.username="$(DB_USERNAME)" \
		-p postgresql.auth.database="$(DB_DATABASE)" \
		-p rabbitmq.auth.username="$(RABBITMQ_USER)" \
		-p app.databaseUrl="pgsql://$(DB_USERNAME):$(DB_PASSWORD)@levelup-store-postgresql:5432/$(DB_DATABASE)?serverVersion=17&charset=utf8" \
		-p app.redisUrl="redis://:$(REDIS_PASSWORD)@levelup-store-redis-master:6379" \
		-p app.rabbitmqPass="$(RABBITMQ_PASS)" \
		-p app.messengerDsn="amqp://$(RABBITMQ_USER):$(RABBITMQ_PASS)@levelup-store-rabbitmq:5672/%2f" \
		-p app.minioRootUser="$(MINIO_ROOT_USER)" \
		-p app.minioRootPassword="$(MINIO_ROOT_PASSWORD)" \
		-p app.mercureJwtSecret="$(MERCURE_JWT_SECRET)" \
		-p ingress.host="$(APP_DOMAIN)" \
		-p minio.publicUrl="https://$(APP_DOMAIN)/storage" \
		-p mercure.publicUrl="https://$(APP_DOMAIN)/.well-known/mercure" \
		-p mercure.corsOrigins="https://$(APP_DOMAIN)" \
		-p turnstile.siteKey="$(TURNSTILE_SITE_KEY)" \
		-p turnstile.secretKey="$(TURNSTILE_SECRET_KEY)" \
		-p app.trustedProxies="$(TRUSTED_PROXIES)" \
		-p app.auditLogsEnabled="$(AUDIT_LOGS_ENABLED)" \
		-p app.wkhtmltopdfEnabled="$(WKHTMLTOPDF_ENABLED)" \
		-p app.postgresVersion="$(SERVER_VERSION)" \
		-p app.jwtTtl="$(JWT_TTL)" \
		-p app.jwtRefreshTtl="$(JWT_REFRESH_TTL)"
	@echo "✓ App secrets set."

monitoring-secrets: ## Set Grafana password, domain and Alertmanager email via ArgoCD
	$(call require,APP_DOMAIN)
	$(call require,GRAFANA_PASSWORD)
	$(call require,MAILER_USER)
	$(call require,MAILER_HOST)
	$(call require,MAILER_PORT)
	$(call require,MAILER_PASS)
	$(call argocd_login)
	argocd app set monitoring $(ARGOCD_FLAGS) \
		-p kube-prometheus-stack.grafana.adminPassword="$(GRAFANA_PASSWORD)" \
		-p "kube-prometheus-stack.grafana.ingress.hosts[0]=grafana.$(APP_DOMAIN)" \
		-p "kube-prometheus-stack.grafana.tls[0].hosts[0]=grafana.$(APP_DOMAIN)" \
		-p "kube-prometheus-stack.alertmanager.config.receivers[0].email_configs[0].to=$(MAILER_USER)" \
		-p "kube-prometheus-stack.alertmanager.config.receivers[0].email_configs[0].from=$(MAILER_USER)" \
		-p "kube-prometheus-stack.alertmanager.config.receivers[0].email_configs[0].smarthost=$(MAILER_HOST):$(MAILER_PORT)" \
		-p "kube-prometheus-stack.alertmanager.config.receivers[0].email_configs[0].auth_username=$(MAILER_USER)" \
		-p "kube-prometheus-stack.alertmanager.config.receivers[0].email_configs[0].auth_password=$(MAILER_PASS)"
	@echo "✓ Monitoring configured. Grafana: https://grafana.$(APP_DOMAIN)"

# ArgoCD Notifications
argocd-notifications: ## Configure ArgoCD email notifications
	$(call require,MAILER_PASS)
	kubectl create secret generic argocd-notifications-secret \
		--namespace argocd \
		--from-literal=email-password="$(MAILER_PASS)" \
		--dry-run=client -o yaml | kubectl apply -f -
	envsubst < kubernetes/cluster/argocd-notifications-cm.yaml | kubectl apply -f -
	@echo "✓ ArgoCD Notifications configured."

# ── Atlantis ──────────────────────────────────────────────────────────────────

atlantis-secret: ## Create Atlantis secret (GitHub token + OCI credentials + TF backend for Terraform)
	$(call require,ATLANTIS_GH_TOKEN)
	$(call require,ATLANTIS_GH_WEBHOOK_SECRET)
	$(call require,TF_VAR_tenancy_ocid)
	$(call require,TF_BACKEND_BUCKET)
	$(call require,TF_BACKEND_ENDPOINT)
	$(call require,TF_BACKEND_ACCESS_KEY)
	$(call require,TF_BACKEND_SECRET_KEY)
	@test -f $(TF_VAR_private_key_path) || (echo "ERROR: OCI private key not found at $(TF_VAR_private_key_path)"; exit 1)
	kubectl create namespace atlantis --dry-run=client -o yaml | kubectl apply -f -
	kubectl create secret generic atlantis-credentials \
		--namespace atlantis \
		--from-literal=github_token="$(ATLANTIS_GH_TOKEN)" \
		--from-literal=github_webhook_secret="$(ATLANTIS_GH_WEBHOOK_SECRET)" \
		--from-literal=TF_VAR_tenancy_ocid="$(TF_VAR_tenancy_ocid)" \
		--from-literal=TF_VAR_user_ocid="$(TF_VAR_user_ocid)" \
		--from-literal=TF_VAR_fingerprint="$(TF_VAR_fingerprint)" \
		--from-literal=TF_VAR_region="$(TF_VAR_region)" \
		--from-literal=TF_VAR_compartment_ocid="$(TF_VAR_compartment_ocid)" \
		--from-literal=TF_VAR_ssh_public_key="$(TF_VAR_ssh_public_key)" \
		--from-literal=TF_VAR_allowed_cidr="$(TF_VAR_allowed_cidr)" \
		--from-literal=APP_DOMAIN="$(APP_DOMAIN)" \
		--from-literal=TF_VAR_cloudflare_api_token="$(TF_VAR_cloudflare_api_token)" \
		--from-literal=TF_VAR_cloudflare_zone_id="$(TF_VAR_cloudflare_zone_id)" \
		--from-literal=TF_VAR_vm_shape="$(TF_VAR_vm_shape)" \
		--from-literal=TF_VAR_vm_ocpus="$(TF_VAR_vm_ocpus)" \
		--from-literal=TF_VAR_vm_memory_gb="$(TF_VAR_vm_memory_gb)" \
		--from-literal=TF_VAR_vcn_cidr="$(TF_VAR_vcn_cidr)" \
		--from-literal=TF_VAR_subnet_cidr="$(TF_VAR_subnet_cidr)" \
		--from-literal=TF_VAR_velero_bucket="$(TF_VAR_velero_bucket)" \
		--from-literal=TF_CLI_ARGS_init="-backend-config=bucket=$(TF_BACKEND_BUCKET) -backend-config=key=oracle/terraform.tfstate -backend-config=region=$(TF_VAR_region) -backend-config=endpoint=$(TF_BACKEND_ENDPOINT) -backend-config=access_key=$(TF_BACKEND_ACCESS_KEY) -backend-config=secret_key=$(TF_BACKEND_SECRET_KEY) -backend-config=skip_region_validation=true -backend-config=skip_credentials_validation=true -backend-config=skip_metadata_api_check=true -backend-config=force_path_style=true" \
		--from-file=oci_private_key=$(TF_VAR_private_key_path) \
		--dry-run=client -o yaml | kubectl apply -f -
	@echo "✓ Atlantis secret created."
	@echo "  Set up GitHub webhook: https://github.com/$(GITHUB_USERNAME)/$(APP_NAME)/settings/hooks"
	@echo "  Payload URL: https://atlantis.$(APP_DOMAIN)/events"
	@echo "  Content type: application/json"
	@echo "  Secret: value of ATLANTIS_GH_WEBHOOK_SECRET from .env"
	@echo "  Events: Pull requests, Issue comments"

atlantis-install: ## Configure Atlantis via ArgoCD (deployed by root-app)
	$(call require,APP_DOMAIN)
	$(call require,GITHUB_USERNAME)
	$(call require,ATLANTIS_REPO_WHITELIST)
	$(MAKE) atlantis-secret
	$(call argocd_login)
	argocd app set atlantis $(ARGOCD_FLAGS) \
		-p atlantis.github.user="$(GITHUB_USERNAME)" \
		-p atlantis.orgWhitelist="$(ATLANTIS_REPO_WHITELIST)" \
		-p atlantis.ingress.host="atlantis.$(APP_DOMAIN)" \
		-p "atlantis.tls[0].hosts[0]=atlantis.$(APP_DOMAIN)"
	@echo "✓ Atlantis deployed. Available at: https://atlantis.$(APP_DOMAIN)"

# ── Blackbox Exporter ─────────────────────────────────────────────────────────

blackbox-install: ## Set Blackbox Exporter target URLs via ArgoCD (deployed by root-app)
	$(call require,APP_DOMAIN)
	$(call argocd_login)
	argocd app set blackbox-exporter $(ARGOCD_FLAGS) \
		-p "prometheus-blackbox-exporter.serviceMonitor.targets[0].url=https://$(APP_DOMAIN)" \
		-p "prometheus-blackbox-exporter.serviceMonitor.targets[1].url=https://$(APP_DOMAIN)/"
	@echo "✓ Blackbox Exporter deployed. Dashboards in Grafana → Explore → probe_success"

# ── Sealed Secrets ────────────────────────────────────────────────────────────

sealed-secrets-cert: ## Download cluster public key for kubeseal (after controller install)
	@mkdir -p _examples
	kubeseal --fetch-cert \
		--controller-name=sealed-secrets \
		--controller-namespace=sealed-secrets \
		> _examples/sealed-secrets-cert.pem
	@echo "✓ Cert saved to _examples/sealed-secrets-cert.pem"
	@echo "  Next step: make sealed-secrets-generate"

sealed-secrets-generate: ## Generate SealedSecret from .env values (requires: kubeseal + cert)
	@test -f _examples/sealed-secrets-cert.pem \
		|| (echo "ERROR: Cert not found. Run first: make sealed-secrets-cert"; exit 1)
	$(call require,APP_SECRET)
	$(call require,HMAC_SECRET)
	$(call require,JWT_PASSPHRASE)
	$(call require,STRIPE_SECRET)
	$(call require,DB_PASSWORD)
	$(call require,REDIS_PASSWORD)
	$(call require,RABBITMQ_PASS)
	$(call require,MINIO_ROOT_PASSWORD)
	$(call require,MERCURE_JWT_SECRET)
	$(call require,TURNSTILE_SITE_KEY)
	$(call require,TURNSTILE_SECRET_KEY)
	kubectl create secret generic levelup-store \
		--namespace levelup-store \
		--from-literal=APP_SECRET="$(APP_SECRET)" \
		--from-literal=HMAC_SECRET="$(HMAC_SECRET)" \
		--from-literal=JWT_PASSPHRASE="$(JWT_PASSPHRASE)" \
		--from-literal=STRIPE_SECRET="$(STRIPE_SECRET)" \
		--from-literal=DATABASE_URL="pgsql://$(DB_USERNAME):$(DB_PASSWORD)@levelup-store-postgresql:5432/$(DB_DATABASE)?serverVersion=17&charset=utf8" \
		--from-literal=REDIS_URL="redis://:$(REDIS_PASSWORD)@levelup-store-redis-master:6379" \
		--from-literal=RABBITMQ_PASS="$(RABBITMQ_PASS)" \
		--from-literal=MESSENGER_TRANSPORT_DSN="amqp://$(RABBITMQ_USER):$(RABBITMQ_PASS)@levelup-store-rabbitmq:5672/%2f" \
		--from-literal=MINIO_ROOT_USER="$(MINIO_ROOT_USER)" \
		--from-literal=MINIO_ROOT_PASSWORD="$(MINIO_ROOT_PASSWORD)" \
		--from-literal=MINIO_PUBLIC_URL="https://$(APP_DOMAIN)/storage" \
		--from-literal=MERCURE_PUBLIC_URL="https://$(APP_DOMAIN)/.well-known/mercure" \
		--from-literal=MERCURE_JWT_SECRET="$(MERCURE_JWT_SECRET)" \
		--from-literal=MERCURE_CORS_ORIGINS="https://$(APP_DOMAIN)" \
		--from-literal=TURNSTILE_SITE_KEY="$(TURNSTILE_SITE_KEY)" \
		--from-literal=TURNSTILE_SECRET_KEY="$(TURNSTILE_SECRET_KEY)" \
		--dry-run=client -o yaml \
	| kubeseal \
		--cert _examples/sealed-secrets-cert.pem \
		--format yaml \
	> kubernetes/sealed-secret.yaml
	@echo "✓ Generated: kubernetes/sealed-secret.yaml"
	@echo "  Commit to git - it is safe, encrypted with the cluster key."
	@echo "  Enable: argocd app set $(APP_NAME) -p sealedSecrets.enabled=true"

# ── Helm ──────────────────────────────────────────────────────────────────────

helm-deps-update: ## Update Chart.lock for all charts (run after changing Chart.yaml versions)
	@for dir in helm/*/; do \
		if grep -q "dependencies:" "$$dir/Chart.yaml" 2>/dev/null; then \
			echo "→ $$dir"; \
			helm dependency update "$$dir"; \
		fi \
	done
	@echo "✓ Chart.lock files updated. Commit them to lock dependency versions."

# ── System Upgrade Controller ─────────────────────────────────────────────────

k3s-upgrade-plan: ## Activate automatic k3s upgrade plan (stable channel)
	kubectl apply -f kubernetes/cluster/k3s-upgrade-plan.yaml
	@echo "✓ k3s upgrade plan active. Current status: kubectl get plan -n system-upgrade"
