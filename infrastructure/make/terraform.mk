# ──────────────────────────────────────────────────────────────────────────────
# 🏗️ Terraform Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: tf-lock tf-init tf-init-remote tf-plan tf-apply tf-import-velero tf-destroy tf-output

## Generate .terraform.lock.hcl - run once and commit to git
tf-lock:
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform providers lock \
		-platform=linux_arm64 \
		-platform=linux_amd64 \
		-platform=darwin_arm64
	@echo "✓ .terraform.lock.hcl updated. Commit: git add $(TERRAFORM_DIR)/.terraform.lock.hcl"

## Initialize Terraform - local state (default)
tf-init:
	$(call require_bin,terraform,https://developer.hashicorp.com/terraform/install)
	cd $(TERRAFORM_DIR) && TF_DATA_DIR=.cache terraform init

## Initialize Terraform - OCI Object Storage remote state
tf-init-remote:
	cd $(TERRAFORM_DIR) && \
		AWS_REQUEST_CHECKSUM_CALCULATION=when_required \
		TF_DATA_DIR=.cache terraform init \
		-reconfigure \
		-backend-config=secrets/backend.config.hcl \
		-backend-config=secrets/backend.credentials.hcl

## Terraform plan - shows what will change on OCI
tf-plan:
	cd $(TERRAFORM_DIR) && \
		AWS_REQUEST_CHECKSUM_CALCULATION=when_required \
		TF_DATA_DIR=.cache terraform plan

## Terraform apply - creates VM on OCI
tf-apply:
	$(call require_bin,terraform,https://developer.hashicorp.com/terraform/install)
	cd $(TERRAFORM_DIR) && \
		AWS_REQUEST_CHECKSUM_CALCULATION=when_required \
		TF_DATA_DIR=.cache terraform apply

## Import existing Velero OCI bucket into Terraform state
tf-import-velero:
	cd $(TERRAFORM_DIR) && \
		AWS_REQUEST_CHECKSUM_CALCULATION=when_required \
		TF_DATA_DIR=.cache terraform import oci_objectstorage_bucket.velero "n/$(OCI_NAMESPACE)/b/$(TF_VAR_velero_bucket)"

## Terraform destroy - deletes VM on OCI
tf-destroy:
	cd $(TERRAFORM_DIR) && \
		AWS_REQUEST_CHECKSUM_CALCULATION=when_required \
		TF_DATA_DIR=.cache terraform destroy

## Show terraform outputs (e.g. public IP)
tf-output:
	cd $(TERRAFORM_DIR) && \
		AWS_REQUEST_CHECKSUM_CALCULATION=when_required \
		TF_DATA_DIR=.cache terraform output
