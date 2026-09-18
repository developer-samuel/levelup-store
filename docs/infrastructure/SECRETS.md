# Environment Variables & Secrets

The project uses a single `.env` file (repo root) for everything - app config, infrastructure,
Terraform variables, and Ansible. Production values override via `.env.production`.

**Never commit `.env` or `.env.production` - both are gitignored.**

---

## How secrets flow to production

```
.env + .env.production
       │
       ▼
make -C infrastructure secrets
       │
       ▼
argocd app set ... -p app.secret="..." -p postgresql.auth.username="..."
       │
       ▼
ArgoCD syncs → Helm renders values → K8s Secret → pod env vars
```

Secrets are passed directly to ArgoCD as Helm values - no Sealed Secrets or Vault needed.
The `sealedSecrets.enabled=false` flag disables the alternative path.

---

## make secrets - required variables

`make -C infrastructure secrets` sets all app-level secrets via ArgoCD.
Every variable listed below must be set in `.env` (or `.env.production`) before running it.

### App

| Variable             | Description                                 | Example                                   |
|----------------------|---------------------------------------------|-------------------------------------------|
| `APP_DOMAIN`         | Public domain (no protocol)                 | `yourdomain.com`                          |
| `APP_URL`            | Full public URL                             | `https://yourdomain.com`                  |
| `APP_SECRET`         | Symfony app secret (random 32+ char string) | `openssl rand -hex 32`                    |
| `HMAC_SECRET`        | HMAC signing secret                         | `openssl rand -hex 32`                    |
| `CORS_ALLOW_ORIGIN`  | Allowed CORS origins (regex)                | `https://yourdomain.com`                  |
| `TRUSTED_PROXIES`    | Trusted proxy IPs for Symfony               | `127.0.0.1,REMOTE_ADDR`                   |
| `AUDIT_LOGS_ENABLED` | Enable audit logging                        | `true`                                    |
| `GHCR_IMAGE`         | Full GHCR image path (without tag)          | `ghcr.io/your-username/levelup-store/app` |

### Database (PostgreSQL)

| Variable         | Description                            | Example                                           |
|------------------|----------------------------------------|---------------------------------------------------|
| `DATABASE_URL`   | Full DSN - built from other vars       | `pgsql://user:pass@host:5432/db?serverVersion=17` |
| `DB_USERNAME`    | PostgreSQL username                    | `levelup`                                         |
| `DB_DATABASE`    | PostgreSQL database name               | `levelup_store`                                   |
| `SERVER_VERSION` | PostgreSQL major version (used in DSN) | `17`                                              |

> `DB_HOST` and `DB_PORT` stay as defaults in `.env`. In production `DATABASE_URL` in `.env.production`
> uses the K8s service hostname (e.g. `levelup-store-postgresql`).

### Redis

| Variable    | Description                              |
|-------------|------------------------------------------|
| `REDIS_URL` | Full DSN - `redis://:password@host:6379` |

### RabbitMQ

| Variable                  | Description                         |
|---------------------------|-------------------------------------|
| `RABBITMQ_USER`           | RabbitMQ username                   |
| `RABBITMQ_PASS`           | RabbitMQ password                   |
| `MESSENGER_TRANSPORT_DSN` | Full AMQP DSN for Symfony Messenger |

### MinIO (S3-compatible object storage)

| Variable              | Description                |
|-----------------------|----------------------------|
| `MINIO_ROOT_USER`     | MinIO admin username       |
| `MINIO_ROOT_PASSWORD` | MinIO admin password       |
| `MINIO_PUBLIC_URL`    | Public URL for file access |

### Mercure (real-time / SSE)

| Variable               | Description                      |
|------------------------|----------------------------------|
| `MERCURE_JWT_SECRET`   | JWT signing secret (min 256-bit) |
| `MERCURE_PUBLIC_URL`   | Public Mercure hub URL           |
| `MERCURE_CORS_ORIGINS` | Allowed origins for Mercure CORS |

### JWT (API authentication)

| Variable          | Description                    | Default             |
|-------------------|--------------------------------|---------------------|
| `JWT_PASSPHRASE`  | Passphrase for JWT private key | -                   |
| `JWT_TTL`         | Access token TTL in seconds    | `900` (15 min)      |
| `JWT_REFRESH_TTL` | Refresh token TTL in seconds   | `2592000` (30 days) |

### Stripe

| Variable        | Description                       |
|-----------------|-----------------------------------|
| `STRIPE_SECRET` | Stripe secret key (`sk_live_...`) |

### Mailer (SMTP)

| Variable        | Description                                    |
|-----------------|------------------------------------------------|
| `MAILER_USER`   | SMTP username / email address                  |
| `MAILER_PASS`   | SMTP password                                  |
| `MAILER_HOST`   | SMTP server hostname                           |
| `MAILER_PORT`   | SMTP port                                      |
| `MAILER_SCHEME` | `smtps` (TLS) or `smtp`                        |
| `MAILER_DSN`    | Full DSN - built automatically from above vars |

### Cloudflare Turnstile (CAPTCHA)

| Variable               | Description                     |
|------------------------|---------------------------------|
| `TURNSTILE_SITE_KEY`   | Public site key                 |
| `TURNSTILE_SECRET_KEY` | Private secret key              |
| `TURNSTILE_VERIFY_URL` | Turnstile verification endpoint |

### OpenTelemetry

| Variable                      | Description                         |
|-------------------------------|-------------------------------------|
| `OTEL_SERVICE_NAME`           | Service name shown in Tempo/Grafana |
| `OTEL_EXPORTER_OTLP_ENDPOINT` | OTel collector endpoint             |
| `OTEL_EXPORTER_OTLP_PROTOCOL` | `http/protobuf`                     |

> In production the worker pod uses `HOST_IP` (downward API) to reach the OTel DaemonSet on the node.
> The endpoint in `.env.production` should point to the collector service, not localhost.

### Other

| Variable              | Description                                   |
|-----------------------|-----------------------------------------------|
| `SENTRY_DSN`          | Sentry error tracking DSN (`null` to disable) |
| `WKHTMLTOPDF_PATH`    | Path to wkhtmltopdf binary                    |
| `WKHTMLTOPDF_ENABLED` | `true`/`false`                                |
| `API_COUNTRY_URL`     | External countries API URL                    |

---

## make services-secrets - K8s secrets for services

Run once after the cluster is up, before ArgoCD syncs the service charts.
Creates raw K8s secrets that Helm charts (PostgreSQL, Redis, RabbitMQ, MinIO, Mercure) read directly.

```bash
make -C infrastructure services-secrets
```

Required variables:

| Variable                 | Used for                                |
|--------------------------|-----------------------------------------|
| `DB_PASSWORD`            | PostgreSQL `postgres-password` secret   |
| `REDIS_PASSWORD`         | Redis `redis-password` secret           |
| `RABBITMQ_USER`          | RabbitMQ username                       |
| `RABBITMQ_PASS`          | RabbitMQ password                       |
| `RABBITMQ_ERLANG_COOKIE` | RabbitMQ cluster cookie (random string) |
| `MINIO_ROOT_USER`        | MinIO root user                         |
| `MINIO_ROOT_PASSWORD`    | MinIO root password                     |
| `MERCURE_JWT_SECRET`     | Mercure JWT secret                      |

---

## make monitoring-secrets

Sets Grafana admin password and Alertmanager email config via ArgoCD.

```bash
make -C infrastructure monitoring-secrets
```

Required: `GRAFANA_PASSWORD`, `MAILER_USER`, `MAILER_HOST`, `MAILER_PORT`, `MAILER_PASS`, `APP_DOMAIN`.

| Variable          | Description              |
|-------------------|--------------------------|
| `GRAFANA_PASSWORD` | Grafana admin password  |

---

## make jwt-keys-secret

Generates RSA key pair for JWT and stores it as a K8s secret.

```bash
make -C infrastructure jwt-keys-secret
```

Only needed once. The keys are stored in the `levelup-store` namespace and mounted into the app pod.

---

## make velero-secret / velero-install

Creates the K8s secret and configures Velero backups on OCI Object Storage.

```bash
make -C infrastructure velero-secret   # creates the K8s secret
make -C infrastructure velero-install  # sets bucket/region via ArgoCD
```

| Variable              | Description                                           |
|-----------------------|-------------------------------------------------------|
| `VELERO_ACCESS_KEY`   | OCI Customer Secret Key (access key)                  |
| `VELERO_SECRET_KEY`   | OCI Customer Secret Key (secret)                      |
| `VELERO_S3_URL`       | OCI Object Storage S3-compatible endpoint URL         |
| `TF_VAR_velero_bucket`| OCI bucket name for backups (also used by Terraform)  |
| `TF_VAR_region`       | OCI region (e.g. `eu-frankfurt-1`)                    |

---

## GitHub & Ansible

| Variable            | Description                                               |
|---------------------|-----------------------------------------------------------|
| `GITHUB_REPO_URL`   | Full GitHub repo URL (used by ArgoCD and `argocd-configure`) |
| `GITHUB_USERNAME`   | GitHub username (used by `argocd-repo-add`, Atlantis)     |
| `ANSIBLE_SSH_KEY`   | Path to SSH private key used by Ansible (`~/.ssh/id_ed25519`) |
| `ANSIBLE_USER`      | SSH user on the VM (e.g. `ubuntu`)                        |

---

## Atlantis

Required by `make atlantis-secret` and `make atlantis-install`.

| Variable                    | Description                                                    |
|-----------------------------|----------------------------------------------------------------|
| `ATLANTIS_GH_TOKEN`         | GitHub personal access token for Atlantis (repo + webhook scope) |
| `ATLANTIS_GH_WEBHOOK_SECRET`| Random secret for GitHub webhook verification                  |
| `ATLANTIS_REPO_WHITELIST`   | Allowed repos pattern (e.g. `github.com/username/levelup-store`) |

---

## Terraform remote state

Required only when using `make tf-init-remote` (OCI Object Storage backend).
Also injected into the Atlantis pod via `make atlantis-secret`.

| Variable               | Description                                      |
|------------------------|--------------------------------------------------|
| `TF_BACKEND_BUCKET`    | OCI bucket name for Terraform state              |
| `TF_BACKEND_ENDPOINT`  | OCI Object Storage S3-compatible endpoint URL    |
| `TF_BACKEND_ACCESS_KEY`| OCI Customer Secret Key (access key)             |
| `TF_BACKEND_SECRET_KEY`| OCI Customer Secret Key (secret)                 |

---

## Terraform variables

All `TF_VAR_*` variables are loaded from `.env` automatically by Makefile.

| Variable                      | Description                                            |
|-------------------------------|--------------------------------------------------------|
| `TF_VAR_tenancy_ocid`         | OCI tenancy OCID                                       |
| `TF_VAR_user_ocid`            | OCI user OCID                                          |
| `TF_VAR_fingerprint`          | OCI API key fingerprint                                |
| `TF_VAR_private_key_path`     | Path to OCI API private key (`~/.oci/oci_api_key.pem`) |
| `TF_VAR_region`               | OCI region (e.g. `eu-frankfurt-1`)                     |
| `TF_VAR_compartment_ocid`     | OCI compartment OCID                                   |
| `TF_VAR_ssh_public_key`       | Auto-read from `ANSIBLE_SSH_KEY.pub` by Makefile       |
| `TF_VAR_allowed_cidr`         | Your public IP for SSH access: `["1.2.3.4/32"]`        |
| `TF_VAR_vm_shape`             | VM shape - `VM.Standard.A1.Flex` (free tier ARM)       |
| `TF_VAR_vm_ocpus`             | vCPU count                                             |
| `TF_VAR_vm_memory_gb`         | Memory in GB                                           |
| `TF_VAR_cloudflare_api_token` | Cloudflare API token                                   |
| `TF_VAR_cloudflare_zone_id`   | Cloudflare zone ID                                     |
| `TF_VAR_velero_bucket`        | OCI bucket name for Velero backups                     |

---

## .env vs .env.production - what goes where

`.env` holds dev defaults and is the source of truth for local development.
`.env.production` only contains values that differ in production. Keep it minimal.

Typical `.env.production` content:

```bash
APP_ENV=prod
APP_DEBUG=0
APP_DOMAIN=yourdomain.com
APP_URL=https://yourdomain.com
CORS_ALLOW_ORIGIN=https://yourdomain.com

# K8s internal service hostnames (not available locally)
DB_HOST=levelup-store-postgresql
REDIS_HOST=levelup-store-redis-master
RABBITMQ_HOST=levelup-store-rabbitmq
ELASTICSEARCH_HOST=levelup-store-elasticsearch

# Rebuilt DSNs with production hostnames
DATABASE_URL=pgsql://${DB_USERNAME}:${DB_PASSWORD}@${DB_HOST}:5432/${DB_DATABASE}?serverVersion=${SERVER_VERSION}
REDIS_URL=redis://:${REDIS_PASSWORD}@${REDIS_HOST}:6379
MESSENGER_TRANSPORT_DSN=amqp://${RABBITMQ_USER}:${RABBITMQ_PASS}@${RABBITMQ_HOST}:5672//

MERCURE_PUBLIC_URL=https://yourdomain.com/.well-known/mercure
MERCURE_CORS_ORIGINS=https://yourdomain.com
MINIO_PUBLIC_URL=https://storage.yourdomain.com
```
