# ──────────────────────────────────────────────────────────────────────────────
# 🧩 Addons (Velero, Atlantis, Blackbox)
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: velero-secret velero-install atlantis-secret atlantis-install blackbox-install jenkins-secret jenkins-install

## Create Velero credentials secret from .env (OCI Customer Secret Keys)
velero-secret:
	$(call require,VELERO_ACCESS_KEY)
	$(call require,VELERO_SECRET_KEY)
	kubectl create namespace velero --dry-run=client -o yaml | kubectl apply --validate=false -f -
	@printf '[default]\naws_access_key_id=%s\naws_secret_access_key=%s\n' \
		'$(VELERO_ACCESS_KEY)' '$(VELERO_SECRET_KEY)' > /tmp/velero-creds
	kubectl create secret generic velero-credentials \
		--namespace velero \
		--from-file=cloud=/tmp/velero-creds \
		--dry-run=client -o yaml | kubectl apply --validate=false -f -
	@rm -f /tmp/velero-creds

## Configure Velero OCI region/bucket/endpoint via ArgoCD (deployed by root-app)
velero-install:
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

## Create Atlantis secret (GitHub token + OCI credentials + TF backend for Terraform)
atlantis-secret:
	$(call require,ATLANTIS_GH_TOKEN)
	$(call require,ATLANTIS_GH_WEBHOOK_SECRET)
	$(call require,TF_VAR_tenancy_ocid)
	$(call require,TF_BACKEND_BUCKET)
	$(call require,TF_BACKEND_ENDPOINT)
	$(call require,TF_BACKEND_ACCESS_KEY)
	$(call require,TF_BACKEND_SECRET_KEY)
	@test -f $(TF_VAR_private_key_path) || (echo "ERROR: OCI private key not found at $(TF_VAR_private_key_path)"; exit 1)
	kubectl create namespace atlantis --dry-run=client -o yaml | kubectl apply --validate=false -f -
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
		--dry-run=client -o yaml | kubectl apply --validate=false -f -
	@echo "✓ Atlantis secret created."
	@echo "  Set up GitHub webhook: https://github.com/$(GITHUB_USERNAME)/$(APP_NAME)/settings/hooks"
	@echo "  Payload URL: https://atlantis.$(APP_DOMAIN)/events"
	@echo "  Content type: application/json"
	@echo "  Secret: value of ATLANTIS_GH_WEBHOOK_SECRET from .env"
	@echo "  Events: Pull requests, Issue comments"

## Configure Atlantis via ArgoCD (deployed by root-app)
atlantis-install:
	$(call require,APP_DOMAIN)
	$(call require,GITHUB_USERNAME)
	$(call require,ATLANTIS_REPO_WHITELIST)
	$(MAKE) atlantis-secret
	$(call argocd_login)
	argocd app set atlantis $(ARGOCD_FLAGS) \
		-p atlantis.github.user="$(GITHUB_USERNAME)" \
		-p atlantis.github.token="$(ATLANTIS_GH_TOKEN)" \
		-p atlantis.github.secret="$(ATLANTIS_GH_WEBHOOK_SECRET)" \
		-p atlantis.orgWhitelist="$(ATLANTIS_REPO_WHITELIST)" \
		-p atlantis.ingress.host="atlantis.$(APP_DOMAIN)" \
		-p "atlantis.ingress.tls[0].hosts[0]=atlantis.$(APP_DOMAIN)"
	@echo "✓ Atlantis deployed. Available at: https://atlantis.$(APP_DOMAIN)"

## Create Jenkins credentials secret from .env (GitHub PAT)
jenkins-secret:
	$(call require,GITHUB_PAT)
	kubectl create namespace jenkins --dry-run=client -o yaml | kubectl apply --validate=false -f -
	kubectl create secret generic jenkins-credentials \
		--namespace jenkins \
		--from-literal=GITHUB_PAT="$(GITHUB_PAT)" \
		--dry-run=client -o yaml | kubectl apply --validate=false -f -
	@echo "✓ Jenkins secret created."

## Configure Jenkins ingress via ArgoCD (deployed by root-app)
jenkins-install:
	$(call require,APP_DOMAIN)
	$(MAKE) jenkins-secret
	$(call argocd_login)
	argocd app set jenkins $(ARGOCD_FLAGS) \
		-p jenkins.controller.ingress.hostName="jenkins.$(APP_DOMAIN)" \
		-p "jenkins.controller.ingress.tls[0].hosts[0]=jenkins.$(APP_DOMAIN)"
	@echo "✓ Jenkins deployed. Available at: https://jenkins.$(APP_DOMAIN)"

## Set Blackbox Exporter target URLs via ArgoCD (deployed by root-app)
blackbox-install:
	$(call require,APP_DOMAIN)
	$(call argocd_login)
	argocd app set blackbox-exporter $(ARGOCD_FLAGS) \
		-p "prometheus-blackbox-exporter.serviceMonitor.targets[0].url=https://$(APP_DOMAIN)" \
		-p "prometheus-blackbox-exporter.serviceMonitor.targets[1].url=https://$(APP_DOMAIN)/"
	@echo "✓ Blackbox Exporter deployed. Dashboards in Grafana → Explore → probe_success"
