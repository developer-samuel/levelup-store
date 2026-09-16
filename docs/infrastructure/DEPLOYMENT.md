# Deployment Guide

This guide covers the full deployment from scratch (new VM) and day-to-day operations.

---

## Prerequisites

- All tools installed (`make -C infrastructure check-deps`)
- `.env` and `.env.production` filled in (see [SECRETS.md](SECRETS.md))
- OCI API key at `~/.oci/oci_api_key.pem`
- SSH key at `~/.ssh/id_ed25519`

---

## DNS setup (manual, one-time)

These steps must be done before running Terraform. Terraform manages DNS records inside Cloudflare,
but it cannot configure the registrar or create the Cloudflare zone itself.

1. **Register the domain** at your registrar (e.g. Hostinger).

2. **Create a Cloudflare zone** for the domain - add the site in the Cloudflare dashboard and note the two assigned nameservers.

3. **Point nameservers to Cloudflare** - in the registrar control panel, replace the default nameservers with the two Cloudflare ones. Propagation takes up to 24 h.

4. **Set Cloudflare API credentials** - `terraform/oracle/variables.tf` expects `cloudflare_api_token` and `cloudflare_zone_id`. After the zone is active, copy the Zone ID from the Cloudflare dashboard (Overview → Zone ID).

After this, `tf-apply` creates two Cloudflare DNS records via `dns.tf`:
- `domain.com` → OCI VM public IP (proxied)
- `*.domain.com` → OCI VM public IP (proxied, wildcard)

The wildcard covers most subdomains (ArgoCD, Grafana, Mercure, etc.) - no per-service DNS entries needed.

**Exception - MinIO:** The Cloudflare wildcard has `proxied = true`, which breaks S3 binary content (images won't load). MinIO needs a direct A record at the registrar (e.g. Hostinger) pointing to the Oracle Cloud VM IP, bypassing Cloudflare entirely:

| Type | Name              | Value                                                    | TTL |
|------|-------------------|----------------------------------------------------------|-----|
| A    | minio.your-domain | OCI VM public IP (`terraform output instance_public_ip`) | 300 |

Add this at the registrar DNS panel, not in Cloudflare.

---

## Full bootstrap (from scratch)

Run everything in one command:

```bash
make -C infrastructure bootstrap
```

Or step by step if you need to debug individual stages:

```bash
make -C infrastructure tf-apply              # 1. provision OCI VM + DNS
make -C infrastructure ansible-install       # 2. install Ansible collections
make -C infrastructure k3s-install           # 3. install k3s on the VM
make -C infrastructure server-harden         # 4. SSH hardening + fail2ban
make -C infrastructure helm-deps-update      # 5. download Helm chart dependencies
make -C infrastructure cert-manager-install  # 6. cert-manager + Let's Encrypt
make -C infrastructure argocd-install        # 7. install ArgoCD
make -C infrastructure argocd-notifications  # 8. configure ArgoCD email notifications
make -C infrastructure argocd-repo-add       # 9. add GitHub repo to ArgoCD (private repo access)
make -C infrastructure argocd-bootstrap      # 10. root-app → ArgoCD deploys everything
make -C infrastructure services-secrets      # 11. K8s secrets for PostgreSQL, Redis, RabbitMQ...
make -C infrastructure secrets               # 12. app production secrets
make -C infrastructure monitoring-secrets    # 13. Grafana + Alertmanager config
make -C infrastructure blackbox-install      # 14. configure Blackbox Exporter targets
make -C infrastructure velero-install        # 15. configure Velero backups
```

After step 9, ArgoCD takes over and deploys all Helm charts automatically.
Steps 10-13 configure secrets that the running pods need.

---

## Seed MinIO uploads (one-time)

After ArgoCD syncs MinIO for the first time, seed the bucket with initial upload assets from the
[levelup-store-uploads](https://github.com/Developer-Samuel/levelup-store-uploads) repository.

Exec into the running app pod:

```bash
kubectl exec -it deployment/levelup-store-app -n levelup-store -- bash docker/scripts/bootstrap/uploads-setup.sh
```

The script reads `MINIO_ENABLED`, `MINIO_ENDPOINT`, `MINIO_ROOT_USER`, `MINIO_ROOT_PASSWORD`, and `MINIO_BUCKET`
from the pod's environment - no extra config needed.

This only needs to be done once. MinIO persists data on a PVC - the bucket survives pod restarts and redeployments.

> Re-run only if you want to reset uploads to the repository state (the script wipes and re-uploads `uploads/`).

---

## Day-to-day deployments

Deployments are fully automatic after a push to `main`:

```
push to main → CI passes → deploy.yml builds image → pushes to GHCR
             → updates values.prod.yaml with new image tag
             → ArgoCD detects change → rolling update
```

No manual intervention needed. Monitor in ArgoCD UI or:

```bash
kubectl rollout status deployment/levelup-store-app -n levelup-store
kubectl get pods -n levelup-store
```

---

## Updating app secrets

When a secret changes (new Stripe key, rotated JWT passphrase, etc.):

1. Update value in `.env.production`
2. Run `make -C infrastructure secrets`
3. ArgoCD will trigger a rolling restart automatically

```bash
make -C infrastructure secrets
```

---

## Updating service secrets (PostgreSQL, Redis, RabbitMQ passwords)

```bash
make -C infrastructure services-secrets
```

Then restart the affected pods:

```bash
kubectl rollout restart deployment -n levelup-store
```

---

## Terraform - reprovisioning the VM

If you need to recreate the VM (disaster recovery, region change):

```bash
make -C infrastructure tf-plan    # preview changes
make -C infrastructure tf-apply   # apply
```

After reprovisioning, re-run from `k3s-install` onwards.

> **Warning:** `tf-destroy` deletes the VM and all data on it. Always ensure Velero backups
> are current before destroying.

---

## Velero - backups and restore

Velero runs daily backups of all K8s resources and PVCs to OCI Object Storage.

Check backup status:

```bash
kubectl get backups -n velero
```

Restore from backup:

```bash
velero restore create --from-backup <backup-name>
```

---

## Sealed Secrets

Sealed Secrets are disabled in favor of direct ArgoCD app params (`sealedSecrets.enabled=false`).
If you want to enable them:

```bash
make -C infrastructure sealed-secrets-cert      # get the public cert
make -C infrastructure sealed-secrets-generate  # generate sealed secrets from .env
```

Then set `sealedSecrets.enabled=true` in `values.prod.yaml`.

---

## Atlantis (Terraform automation via PRs)

Atlantis runs `terraform plan` on PRs that change `infrastructure/terraform/`.

Setup:

```bash
make -C infrastructure atlantis-secret   # create K8s secret with GitHub token + webhook secret
make -C infrastructure atlantis-install  # deploy Atlantis via Helm
```

Required variables: `ATLANTIS_GH_TOKEN`, `ATLANTIS_GH_WEBHOOK_SECRET`, `ATLANTIS_REPO_WHITELIST`.

---

## Common issues

### ArgoCD login fails

```bash
argocd login <APP_DOMAIN> --username admin --password <ARGOCD_PASSWORD> --grpc-web
```

If the password is wrong, reset it:

```bash
kubectl -n argocd get secret argocd-initial-admin-secret -o jsonpath="{.data.password}" | base64 -d
```

### KEDA ScaledObject not ready

KEDA resolves RabbitMQ by hostname. If the ScaledObject shows `Ready: False`, check that
`KEDA_RABBITMQ_HOST` in the secret uses the full FQDN:

```
levelup-store-rabbitmq.levelup-store.svc.cluster.local
```

Short hostnames are not resolvable from the `keda` namespace.

### pg_dump fails in cronjob

The `DATABASE_URL` uses `pgsql://` scheme (Symfony format). pg_dump requires `postgresql://`.
The cronjob converts it automatically with sed. If you see connection errors, check that
`DATABASE_URL` in the secret does not contain `?serverVersion=...` query params - those are
stripped by the same sed command.

### Worker not sending OTel traces

The worker pod needs `OTEL_EXPORTER_OTLP_ENDPOINT` pointing to the OTel collector on the node.
It uses the `HOST_IP` downward API to get the node IP at runtime. If traces are missing,
verify the DaemonSet hostPort is open and the endpoint in the secret is correct.

### Image pull fails (Docker Hub rate limit)

Use `quay.io` mirrors for Docker Hub images where available. The pg-backup cronjob uses
`quay.io/minio/mc` instead of `docker.io/minio/mc` for this reason.

### composer install fails locally with ext-opentelemetry missing

Add the ignore flag:

```bash
composer install --ignore-platform-req=ext-opentelemetry --ignore-platform-req=ext-otel_instrumentation
```

This is a local-only workaround - CI has the extension installed via the php composite action.
