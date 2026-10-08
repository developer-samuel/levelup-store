# ──────────────────────────────────────────────────────────────────────────────
# ⚙️ Variables & Macros
# ──────────────────────────────────────────────────────────────────────────────

# Loads root .env.production (infrastructure deploy config)
ifneq (,$(wildcard ../.env.production))
  include ../.env.production
  export
endif

# SSH public key - evaluated as shell command (not Makefile expression)
TF_VAR_ssh_public_key := $(shell cat $(ANSIBLE_SSH_KEY).pub 2>/dev/null)

# ── Variables ─────────────────────────────────────────────────────────────────

# kubeconfig
KUBECONFIG    ?= $(HOME)/.kube/config-levelup
export KUBECONFIG

# dirs
TERRAFORM_DIR := terraform/oracle
ANSIBLE_DIR   := ansible
K8S_APPS_DIR  := kubernetes/apps
# app
APP_NAME      := levelup-store
ARGOCD_SERVER ?= argocd.$(APP_DOMAIN)
ARGOCD_FLAGS  ?= --grpc-web
# Strip .git suffix - ArgoCD requires URL without it
REPO_URL      := $(shell echo "$(GITHUB_REPO_URL)" | sed 's/\.git$$//')

# ── Macros ────────────────────────────────────────────────────────────────────

# require VAR - error if not set in .env - $(call require,VAR_NAME)
define require
  @test -n "$($(1))" || (echo "ERROR: $(1) not set in .env.production"; exit 1)
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
  $(eval VM_IP := $(shell cd $(TERRAFORM_DIR) && AWS_REQUEST_CHECKSUM_CALCULATION=when_required TF_DATA_DIR=.cache terraform output -raw public_ip))
endef
