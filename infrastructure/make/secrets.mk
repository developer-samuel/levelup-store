# ──────────────────────────────────────────────────────────────────────────────
# 🔐 Secrets Commands
# ──────────────────────────────────────────────────────────────────────────────

.PHONY: services-secrets jwt-keys-secret ecommerce-secrets assistant-secrets monitoring-secrets \
        sealed-secrets-cert sealed-secrets-generate

## Create K8s secrets for standalone service charts + set RabbitMQ username
services-secrets:
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
		--from-literal=mercure-cors-allowed-origins="$(MERCURE_CORS_ORIGINS)" \
		--from-literal=extra-directives="$$(printf 'anonymous\ncors_origins $(CORS_ALLOW_ORIGIN)')" \
		--from-literal=caddy-extra-config="" \
		--from-literal=caddy-extra-directives="" \
		--from-literal=license="" \
		--dry-run=client -o yaml | kubectl apply -f -
	$(call argocd_login)
	argocd app set rabbitmq $(ARGOCD_FLAGS) \
		-p rabbitmq.auth.username="$(RABBITMQ_USER)"
	argocd app set minio $(ARGOCD_FLAGS) \
		-p minio.ingress.hostname="minio.$(APP_DOMAIN)"
	@echo "✓ K8s secrets created and service domains set."

## Create JWT keypair secret from local config/jwt/*.pem files (run once after keygen)
jwt-keys-secret:
	@test -f ../apps/ecommerce/config/jwt/private.pem || (echo "ERROR: apps/ecommerce/config/jwt/private.pem not found. Run: cd apps/ecommerce && composer jwt:generate"; exit 1)
	@test -f ../apps/ecommerce/config/jwt/public.pem  || (echo "ERROR: apps/ecommerce/config/jwt/public.pem not found."; exit 1)
	kubectl create secret generic levelup-store-jwt-keys \
		--namespace levelup-store \
		--from-file=private.pem=../apps/ecommerce/config/jwt/private.pem \
		--from-file=public.pem=../apps/ecommerce/config/jwt/public.pem \
		--dry-run=client -o yaml | kubectl apply -f -
	@echo "✓ JWT keys secret created/updated."

## Set production secrets for levelup-store app via ArgoCD
ecommerce-secrets:
	$(call require,APP_DOMAIN)
	$(call require,APP_URL)
	$(call require,CORS_ALLOW_ORIGIN)
	$(call require,ECOMMERCE_GHCR_IMAGE)
	$(call require,APP_SECRET)
	$(call require,HMAC_SECRET)
	$(call require,JWT_PASSPHRASE)
	$(call require,STRIPE_SECRET)
	$(call require,DATABASE_URL)
	$(call require,REDIS_URL)
	$(call require,RABBITMQ_PASS)
	$(call require,MESSENGER_TRANSPORT_DSN)
	$(call require,MINIO_ROOT_PASSWORD)
	$(call require,MINIO_PUBLIC_URL)
	$(call require,MERCURE_JWT_SECRET)
	$(call require,MERCURE_CORS_ORIGINS)
	$(call require,MERCURE_PUBLIC_URL)
	$(call require,TURNSTILE_SITE_KEY)
	$(call require,TURNSTILE_SECRET_KEY)
	$(call require,TURNSTILE_VERIFY_URL)
	$(call require,MAILER_USER)
	$(call require,MAILER_DSN)
	$(call require,API_COUNTRY_URL)
	$(call require,WKHTMLTOPDF_PATH)
	$(call require,DB_USERNAME)
	$(call require,DB_DATABASE)
	$(call require,RABBITMQ_USER)
	$(call require,MINIO_ROOT_USER)
	$(call require,TRUSTED_PROXIES)
	$(call require,SERVER_VERSION)
	$(call require,JWT_TTL)
	$(call require,JWT_REFRESH_TTL)
	$(call require,OTEL_SERVICE_NAME)
	$(call require,OTEL_EXPORTER_OTLP_ENDPOINT)
	$(call require,OTEL_EXPORTER_OTLP_PROTOCOL)
	$(call argocd_login)
	argocd app set $(APP_NAME) $(ARGOCD_FLAGS) \
		-p sealedSecrets.enabled=false \
		-p app.image.repository="$(ECOMMERCE_GHCR_IMAGE)" \
		-p app.secret="$(APP_SECRET)" \
		-p app.hmacSecret="$(HMAC_SECRET)" \
		-p app.jwtPassphrase="$(JWT_PASSPHRASE)" \
		-p app.stripeSecret="$(STRIPE_SECRET)" \
		-p app.url="$(APP_URL)" \
		-p app.corsAllowOrigin="$(CORS_ALLOW_ORIGIN)" \
		-p app.wkhtmltopdfPath="$(WKHTMLTOPDF_PATH)" \
		-p app.apiCountryUrl="$(API_COUNTRY_URL)" \
		-p app.mailerDsn="$(MAILER_DSN)" \
		-p app.mailerUser="$(MAILER_USER)" \
		-p app.sentryDsn="$(SENTRY_DSN)" \
		-p postgresql.auth.username="$(DB_USERNAME)" \
		-p postgresql.auth.database="$(DB_DATABASE)" \
		-p rabbitmq.auth.username="$(RABBITMQ_USER)" \
		-p app.databaseUrl="$(DATABASE_URL)" \
		-p app.redisUrl="$(REDIS_URL)" \
		-p app.rabbitmqPass="$(RABBITMQ_PASS)" \
		-p app.messengerDsn="$(MESSENGER_TRANSPORT_DSN)" \
		-p app.minioRootUser="$(MINIO_ROOT_USER)" \
		-p app.minioRootPassword="$(MINIO_ROOT_PASSWORD)" \
		-p app.mercureJwtSecret="$(MERCURE_JWT_SECRET)" \
		-p ingress.host="$(APP_DOMAIN)" \
		-p minio.publicUrl="$(MINIO_PUBLIC_URL)" \
		-p mercure.publicUrl="$(MERCURE_PUBLIC_URL)" \
		-p mercure.corsOrigins="$(MERCURE_CORS_ORIGINS)" \
		-p turnstile.siteKey="$(TURNSTILE_SITE_KEY)" \
		-p turnstile.secretKey="$(TURNSTILE_SECRET_KEY)" \
		-p turnstile.verifyUrl="$(TURNSTILE_VERIFY_URL)" \
		-p otel.serviceName="$(OTEL_SERVICE_NAME)" \
		-p otel.endpoint="$(OTEL_EXPORTER_OTLP_ENDPOINT)" \
		-p otel.protocol="$(OTEL_EXPORTER_OTLP_PROTOCOL)" \
		-p app.trustedProxies="$(TRUSTED_PROXIES)" \
		-p app.auditLogsEnabled="$(AUDIT_LOGS_ENABLED)" \
		-p app.wkhtmltopdfEnabled="$(WKHTMLTOPDF_ENABLED)" \
		-p app.postgresVersion="$(SERVER_VERSION)" \
		-p app.jwtTtl="$(JWT_TTL)" \
		-p app.jwtRefreshTtl="$(JWT_REFRESH_TTL)"
	@echo "✓ App secrets set."

## Set production secrets for levelup-store-assistant app via ArgoCD
assistant-secrets:
	$(call require,APP_DOMAIN)
	$(call require,AI_ASSISTANT_API_KEY)
	$(call require,REDIS_URL)
	$(call require,RABBITMQ_URL)
	$(call require,ASSISTANT_GHCR_IMAGE)
	$(call require,SUPPORT_EMAIL)
	$(call require,DATABASE_URL)
	$(call require,CORS_ALLOW_ORIGIN)
	$(call argocd_login)
	argocd app set levelup-store-assistant $(ARGOCD_FLAGS) \
		-p app.aiAssistantApiKey="$(AI_ASSISTANT_API_KEY)" \
		-p app.redisUrl="$(REDIS_URL)" \
		-p app.databaseUrl="$(DATABASE_URL)" \
		-p app.corsOrigins="$(CORS_ALLOW_ORIGIN)" \
		-p app.supportEmail="$(SUPPORT_EMAIL)" \
		-p app.ollamaHost="http://ollama.levelup-store.svc.cluster.local:11434" \
		-p broker.rabbitmqUrl="$(RABBITMQ_URL)" \
		-p app.image.repository="$(ASSISTANT_GHCR_IMAGE)" \
		-p ingress.host="assistant.$(APP_DOMAIN)"
	@echo "✓ Assistant secrets set."

## Set Grafana password, domain and Alertmanager email via ArgoCD
monitoring-secrets:
	$(call require,APP_DOMAIN)
	$(call require,GRAFANA_PASSWORD)
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

# ── Sealed Secrets ────────────────────────────────────────────────────────────

## Download cluster public key for kubeseal (after controller install)
sealed-secrets-cert:
	@mkdir -p _examples
	kubeseal --fetch-cert \
		--controller-name=sealed-secrets \
		--controller-namespace=sealed-secrets \
		> _examples/sealed-secrets-cert.pem
	@echo "✓ Cert saved to _examples/sealed-secrets-cert.pem"
	@echo "  Next step: make sealed-secrets-generate"

## Generate SealedSecret from .env values (requires: kubeseal + cert)
sealed-secrets-generate:
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
