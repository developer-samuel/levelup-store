# ──────────────────────────────────────────────────────────────────────────────
# 📈 Load Testing (k6)
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: k6-ecommerce

## k6-ecommerce: Load test all ecommerce endpoints (auth, search, assistant, health, cookies)
k6-ecommerce:
	$(call require_bin,k6,https://k6.io/docs/get-started/installation)
	$(call require,APP_URL)
	k6 run k6/ecommerce/index.js --env BASE_URL=$(APP_URL)
