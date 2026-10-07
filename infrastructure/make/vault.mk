# ──────────────────────────────────────────────────────────────────────────────
# 🔐 Vault Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: vault-init vault-unseal vault-setup vault-seal-status

## Initialize Vault (run ONCE after first deploy) - stores unseal keys in K8s secret
## After this, Vault auto-unseals on every pod restart via sidecar container
vault-init:
	@echo "Waiting for Vault pod to be running..."
	kubectl wait --for=condition=Ready=false pod -l app.kubernetes.io/name=vault -n vault --timeout=10s 2>/dev/null || true
	kubectl wait --for=jsonpath='{.status.phase}'=Running pod -l app.kubernetes.io/name=vault -n vault --timeout=120s
	@echo "Initializing Vault..."
	kubectl exec -n vault vault-0 -c vault -- vault operator init \
		-key-shares=3 \
		-key-threshold=2 \
		-format=json > /tmp/vault-init.json
	@echo "Storing unseal keys and root token in K8s secret..."
	kubectl create secret generic vault-init \
		--namespace vault \
		--from-file=init.json=/tmp/vault-init.json \
		--dry-run=client -o yaml | kubectl apply --validate=false -f -
	@rm -f /tmp/vault-init.json
	@echo "✓ Vault initialized. Run: make vault-unseal && make vault-setup"

## Unseal Vault manually (fallback - normally done automatically by sidecar)
vault-unseal:
	$(eval INIT_JSON := $(shell kubectl get secret vault-init -n vault -o jsonpath='{.data.init\.json}' | base64 -d))
	$(eval KEY1 := $(shell echo '$(INIT_JSON)' | jq -r '.unseal_keys_b64[0]'))
	$(eval KEY2 := $(shell echo '$(INIT_JSON)' | jq -r '.unseal_keys_b64[1]'))
	kubectl exec -n vault vault-0 -c vault -- vault operator unseal $(KEY1)
	kubectl exec -n vault vault-0 -c vault -- vault operator unseal $(KEY2)
	@echo "✓ Vault unsealed."

## Setup Vault after init: enable KV-v2, Kubernetes auth, ESO policy (run ONCE)
vault-setup:
	$(eval ROOT_TOKEN := $(shell kubectl get secret vault-init -n vault -o jsonpath='{.data.init\.json}' | base64 -d | jq -r '.root_token'))
	kubectl exec -n vault vault-0 -c vault -- env VAULT_TOKEN=$(ROOT_TOKEN) vault secrets enable -path=secret kv-v2 \
		|| echo "KV-v2 already enabled"
	kubectl exec -n vault vault-0 -c vault -- env VAULT_TOKEN=$(ROOT_TOKEN) vault auth enable kubernetes \
		|| echo "Kubernetes auth already enabled"
	kubectl exec -n vault vault-0 -c vault -- env VAULT_TOKEN=$(ROOT_TOKEN) vault write auth/kubernetes/config \
		kubernetes_host=https://kubernetes.default.svc:443
	kubectl exec -n vault vault-0 -c vault -- env VAULT_TOKEN=$(ROOT_TOKEN) \
		vault policy write external-secrets /dev/stdin <<< 'path "secret/data/*" { capabilities = ["read"] }'
	kubectl exec -n vault vault-0 -c vault -- env VAULT_TOKEN=$(ROOT_TOKEN) vault write auth/kubernetes/role/external-secrets \
		bound_service_account_names=external-secrets \
		bound_service_account_namespaces=external-secrets \
		policies=external-secrets \
		ttl=1h
	kubectl apply --validate=false -f infrastructure/kubernetes/vault/cluster-secret-store.yaml
	@echo "✓ Vault configured. External Secrets Operator can now read from Vault."

## Show Vault seal/init/HA status
vault-seal-status:
	kubectl exec -n vault vault-0 -c vault -- vault status
