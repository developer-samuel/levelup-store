# ──────────────────────────────────────────────────────────────────────────────
# ⛵ Helm / Cluster Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: helm-deps-update cert-manager-install k3s-upgrade-plan

## Update Chart.lock for all charts (run after changing Chart.yaml versions)
helm-deps-update:
	@find helm -name "Chart.yaml" | while read f; do \
		dir=$$(dirname "$$f"); \
		if grep -q "dependencies:" "$$f" 2>/dev/null; then \
			echo "→ $$dir"; \
			helm dependency update "$$dir"; \
		fi \
	done
	@echo "✓ Chart.lock files updated. Commit them to lock dependency versions."

## Install cert-manager + ClusterIssuer (Let's Encrypt)
cert-manager-install:
	$(call require_bin,helm,curl https://raw.githubusercontent.com/helm/helm/main/scripts/get-helm-3 | bash)
	$(call require_bin,kubectl,https://kubernetes.io/docs/tasks/tools/)
	$(call require,MAILER_USER)
	helm repo add jetstack https://charts.jetstack.io
	helm repo update
	helm upgrade --install cert-manager helm/platform/cert-manager \
		--namespace cert-manager \
		--create-namespace \
		--wait
	envsubst < kubernetes/cluster/cluster-issuer.yaml | kubectl apply --validate=false -f -

## Activate automatic k3s upgrade plan (stable channel)
k3s-upgrade-plan:
	kubectl apply --validate=false -f kubernetes/cluster/k3s-upgrade-plan.yaml
	@echo "✓ k3s upgrade plan active. Current status: kubectl get plan -n system-upgrade"
