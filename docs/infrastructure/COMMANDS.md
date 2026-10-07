# Makefile Command Reference

All commands run from the repo root with `make -C infrastructure <command>`.
The Makefile auto-loads `.env` and `.env.production` - no manual exports needed.

```bash
make -C infrastructure <command>
# or from inside the infrastructure/ folder:
make <command>
```

---

## General

| Command        | Description                                        |
|----------------|----------------------------------------------------|
| `help`         | List all available commands with descriptions      |
| `check-deps`   | Verify all required tools are installed            |
| `install-deps` | Install missing tools                              |
| `bootstrap`    | Full setup from scratch - runs all stages in order |

---

## Terraform

| Command            | Description                                                  |
|--------------------|--------------------------------------------------------------|
| `tf-init`          | Initialize Terraform with local state                        |
| `tf-init-remote`   | Initialize Terraform with remote state on OCI Object Storage |
| `tf-plan`          | Preview infrastructure changes                               |
| `tf-apply`         | Apply infrastructure changes (provision/update VM, DNS)      |
| `tf-destroy`       | **Destructive** - destroy all Terraform-managed resources    |
| `tf-import-velero` | Import existing Velero OCI bucket into Terraform state       |
| `tf-output`        | Show Terraform outputs (VM IP, etc.)                         |
| `tf-lock`          | Update Terraform provider lock file                          |

---

## Ansible

| Command           | Description                                                |
|-------------------|------------------------------------------------------------|
| `ansible-install` | Install Ansible Galaxy collections from `requirements.yml` |
| `k3s-install`     | Install k3s on the VM and copy kubeconfig locally          |
| `server-harden`   | Apply SSH hardening and fail2ban via Ansible               |

---

## cert-manager & TLS

| Command                | Description                                                   |
|------------------------|---------------------------------------------------------------|
| `cert-manager-install` | Deploy cert-manager and configure Let's Encrypt ClusterIssuer |

---

## ArgoCD

| Command                | Description                                                        |
|------------------------|--------------------------------------------------------------------|
| `argocd-install`       | Deploy ArgoCD to the cluster                                       |
| `argocd-configure`     | Apply ArgoCD configuration (RBAC, settings)                        |
| `argocd-repo-add`      | Add GitHub repo credentials to ArgoCD (required for private repos) |
| `argocd-bootstrap`     | Deploy root app - triggers ArgoCD to sync all charts               |
| `argocd-notifications` | Configure ArgoCD notification templates                            |

---

## Secrets

| Command              | Description                                                        |
|----------------------|--------------------------------------------------------------------|
| `secrets`            | Set all app production secrets via ArgoCD app params               |
| `services-secrets`   | Create K8s secrets for PostgreSQL, Redis, RabbitMQ, MinIO, Mercure |
| `jwt-keys-secret`    | Generate RSA key pair and store as K8s secret for JWT              |
| `monitoring-secrets` | Set Grafana admin password and Alertmanager email config           |
| `velero-secret`      | Create K8s secret with OCI Object Storage credentials for Velero   |
| `atlantis-secret`    | Create K8s secret with GitHub token + webhook secret for Atlantis  |
| `jenkins-secret`     | Create K8s secret with GitHub PAT for Jenkins                      |

> Run `secrets` and `services-secrets` whenever credentials change.
> Always ensure `.env.production` is loaded (values override `.env`) before running these.

---

## Velero (backups)

| Command          | Description                                            |
|------------------|--------------------------------------------------------|
| `velero-install` | Deploy Velero and configure OCI Object Storage backend |

---

## Atlantis

| Command            | Description              |
|--------------------|--------------------------|
| `atlantis-install` | Deploy Atlantis via Helm |

---

## Jenkins

| Command           | Description                                            |
|-------------------|--------------------------------------------------------|
| `jenkins-install` | Create Jenkins secret and configure ingress via ArgoCD |

---

## Blackbox Exporter

| Command            | Description                                           |
|--------------------|-------------------------------------------------------|
| `blackbox-install` | Deploy Blackbox Exporter for HTTP endpoint monitoring |

---

## Sealed Secrets

| Command                   | Description                                            |
|---------------------------|--------------------------------------------------------|
| `sealed-secrets-cert`     | Fetch the Sealed Secrets controller public certificate |
| `sealed-secrets-generate` | Generate SealedSecret manifests from `.env` values     |

> Sealed Secrets are currently disabled (`sealedSecrets.enabled=false`).
> Secrets are passed directly via ArgoCD app params.

---

## Helm

| Command            | Description                                          |
|--------------------|------------------------------------------------------|
| `helm-deps-update` | Update Helm chart dependencies (downloads subcharts) |

---

## System Upgrade Controller

| Command            | Description                                          |
|--------------------|------------------------------------------------------|
| `k3s-upgrade-plan` | Apply k3s upgrade plan via System Upgrade Controller |

---

## Order for full bootstrap

```bash
make -C infrastructure bootstrap
```

Internally runs in this order:

1. `tf-apply`
2. `ansible-install`
3. `k3s-install`
4. `server-harden`
5. `helm-deps-update`
6. `cert-manager-install`
7. `argocd-install`
8. `argocd-notifications`
9. `argocd-repo-add`
10. `argocd-bootstrap`
11. `services-secrets`
12. `secrets`
13. `monitoring-secrets`
14. `blackbox-install`
15. `velero-install`

---

## Application Management (ArgoCD)

Each application has up to 9 commands. Scripts live in `infrastructure/make/argocd-apps/` (organized by category matching `kubernetes/apps/`). All standard lifecycle and status targets delegate to the generic `argocd-app-*` targets in `make/argocd.mk` - change behaviour there once, all apps inherit it.

> Detailed docs per category:
> [Workloads](argocd-apps/workloads/WORKLOADS.md) · [Database](argocd-apps/database/DATABASE.md) · [Cache](argocd-apps/cache/CACHE.md) · [Messaging](argocd-apps/messaging/MESSAGING.md) · [Storage](argocd-apps/storage/STORAGE.md) · [Realtime](argocd-apps/realtime/REALTIME.md) · [Observability](argocd-apps/observability/OBSERVABILITY.md) · [Platform](argocd-apps/platform/PLATFORM-CORE.md) · [DevOps](argocd-apps/platform/DEVOPS.md) · [Scaling](argocd-apps/scaling/SCALING.md) · [Backup](argocd-apps/backup/BACKUP.md)

**Lifecycle:**

| Pattern        | Description                                                   |
|----------------|---------------------------------------------------------------|
| `<app>-start`  | Re-enable ArgoCD sync + trigger sync (fully on)               |
| `<app>-down`   | Disable ArgoCD sync + scale pods to 0 (fully off)             |
| `<app>-stop`   | Scale pods to 0 only (ArgoCD sync stays active - may restart) |
| `<app>-unsync` | Disable ArgoCD sync only (pods keep running)                  |

**Status:**

| Pattern        | Description                                                   |
|----------------|---------------------------------------------------------------|
| `<app>-status` | Show ArgoCD sync/health status and conditions                 |
| `<app>-sync`   | Trigger ArgoCD sync (without changing sync policy)            |
| `<app>-pods`   | Show pods for this application                                |
| `<app>-logs`   | Tail logs (last 100 lines, follow)                            |
| `<app>-vpa`    | Show VPA resource recommendations (ecommerce, assistant only) |

> **Note:** `vault-status` = ArgoCD sync/health (consistent with all apps). `vault-seal-status` = Vault internal seal/init/HA status (in `make/vault.mk`).

**Available applications:**

| Application                                     | Namespace                 | Description                                        |
|-------------------------------------------------|---------------------------|----------------------------------------------------|
| `ecommerce` (ArgoCD: `levelup-store`)           | `levelup-store`           | Ecommerce app (Symfony PHP)                        |
| `assistant` (ArgoCD: `levelup-store-assistant`) | `levelup-store-assistant` | AI assistant app (FastAPI + React)                 |
| `postgresql`                                    | `levelup-store`           | Primary database                                   |
| `redis`                                         | `levelup-store`           | Cache + session store                              |
| `rabbitmq`                                      | `levelup-store`           | Message broker (Symfony Messenger)                 |
| `elasticsearch`                                 | `levelup-store`           | Full-text search engine                            |
| `minio`                                         | `levelup-store`           | S3-compatible object storage (files, images)       |
| `mercure`                                       | `levelup-store`           | SSE/WebSocket hub (real-time updates)              |
| `ollama`                                        | `levelup-store`           | Local LLM inference (used by assistant)            |
| `monitoring`                                    | `monitoring`              | Prometheus + Grafana stack                         |
| `loki`                                          | `monitoring`              | Log aggregation                                    |
| `otel-collector`                                | `monitoring`              | OpenTelemetry collector (traces, metrics)          |
| `blackbox-exporter`                             | `monitoring`              | HTTP endpoint uptime monitoring                    |
| `tempo`                                         | `tempo`                   | Distributed tracing backend                        |
| `jenkins`                                       | `jenkins`                 | CI/CD server                                       |
| `atlantis`                                      | `atlantis`                | Terraform PR automation                            |
| `velero`                                        | `velero`                  | Kubernetes backup (OCI Object Storage)             |
| `falco`                                         | `falco`                   | Runtime security monitoring                        |
| `kyverno`                                       | `kyverno`                 | Kubernetes policy engine                           |
| `kyverno-policies`                              | `kyverno`                 | Kyverno policy definitions                         |
| `keda`                                          | `keda`                    | Event-driven autoscaling (workers, schedulers)     |
| `vpa`                                           | `vpa`                     | Vertical Pod Autoscaler (resource recommendations) |
| `reloader`                                      | `reloader`                | Auto-restart pods on ConfigMap/Secret changes      |
| `external-secrets`                              | `external-secrets`        | Sync secrets from Vault to Kubernetes              |
| `sealed-secrets`                                | `sealed-secrets`          | Encrypted secrets stored in Git                    |
| `vault`                                         | `vault`                   | HashiCorp Vault - secrets management               |
| `cert-manager`                                  | `cert-manager`            | TLS certificate management (Let's Encrypt)         |

**Generic targets** (direct use, bypassing per-app shortcuts):

| Command                             | Description                         |
|-------------------------------------|-------------------------------------|
| `argocd-app-start APP=<app>`        | Start any app by ArgoCD name        |
| `argocd-app-down APP=<app> NS=<ns>` | Stop any app (sync off + pods to 0) |
| `argocd-app-stop APP=<app> NS=<ns>` | Scale pods to 0 only                |
| `argocd-app-unsync APP=<app>`       | Disable ArgoCD sync only            |
| `argocd-app-status APP=<app>`       | ArgoCD sync/health status           |
| `argocd-app-sync APP=<app>`         | Trigger ArgoCD sync                 |
| `argocd-app-pods APP=<app> NS=<ns>` | Show pods                           |
| `argocd-app-logs APP=<app> NS=<ns>` | Tail logs                           |
| `argocd-app-vpa APP=<app> NS=<ns>`  | Show VPA recommendations            |

**Example:**

```bash
make jenkins-down    # stop Jenkins completely
make jenkins-start   # start Jenkins back up
make falco-down
make velero-down
```

---

## Cluster Monitoring & Debugging

Commands in `infrastructure/make/cluster.mk`.

### Status

| Command                 | Description                                    |
|-------------------------|------------------------------------------------|
| `cluster-pods`          | All pods across all namespaces                 |
| `cluster-apps`          | ArgoCD applications with sync/health status    |
| `cluster-nodes`         | Nodes with status and roles                    |
| `cluster-events`        | Recent events sorted by time (last 40)         |
| `cluster-errors`        | Only pods that are not Running or Completed    |
| `cluster-hpa`           | All HorizontalPodAutoscalers                   |
| `cluster-vpa`           | All VerticalPodAutoscalers                     |
| `cluster-pvc`           | All PersistentVolumeClaims                     |
| `cluster-pv`            | All PersistentVolumes with capacity            |
| `cluster-certs`         | cert-manager Certificate objects with expiry   |
| `cluster-ingress`       | All Ingress rules and hostnames                |
| `cluster-netpol`        | All NetworkPolicies                            |
| `cluster-namespaces`    | List all namespaces                            |
| `cluster-crds`          | List all CustomResourceDefinitions             |
| `cluster-images NS=...` | List all container images running in namespace |

### Resource Usage

| Command                    | Description                                         |
|----------------------------|-----------------------------------------------------|
| `cluster-top-pods`         | CPU/RAM per pod - sorted by memory (highest first)  |
| `cluster-top-pods-cpu`     | CPU/RAM per pod - sorted by CPU (highest first)     |
| `cluster-top-nodes`        | CPU/RAM per node                                    |
| `cluster-top-ns`           | CPU/RAM summed per namespace (sorted by RAM)        |
| `cluster-resources NS=...` | Resource requests/limits per pod in namespace       |
| `cluster-pressure`         | Node pressure conditions (Memory, Disk, PID, Ready) |

### Rollout

| Command                                  | Description                  |
|------------------------------------------|------------------------------|
| `cluster-rollout-restart APP=... NS=...` | Restart a deployment         |
| `cluster-rollout-status APP=... NS=...`  | Watch rollout progress       |
| `cluster-rollout-undo APP=... NS=...`    | Rollback to previous version |

### Logs

| Command                            | Description                            |
|------------------------------------|----------------------------------------|
| `cluster-logs APP=... NS=...`      | Tail logs (last 100 lines, follow)     |
| `cluster-logs-prev APP=... NS=...` | Logs of previously crashed pod         |
| `cluster-cp-logs APP=... NS=...`   | Download logs to `/tmp/<app>-<ns>.log` |

### Debug

| Command                               | Description             |
|---------------------------------------|-------------------------|
| `cluster-describe-pod POD=... NS=...` | Describe a specific pod |
| `cluster-describe-app APP=... NS=...` | Describe a deployment   |
| `cluster-exec APP=... NS=...`         | Open shell in a pod     |

### Config & Networking

| Command                     | Description                                 |
|-----------------------------|---------------------------------------------|
| `cluster-secrets NS=...`    | List secrets in namespace                   |
| `cluster-configmaps NS=...` | List configmaps in namespace                |
| `cluster-services NS=...`   | Services with ports in namespace            |
| `cluster-endpoints NS=...`  | Endpoints in namespace (debug connectivity) |
| `cluster-sa NS=...`         | ServiceAccounts in namespace                |

---

## Load Testing

k6 scripts live in `infrastructure/k6/`. `APP_URL` is auto-loaded from `.env.production`.

| Command             | Description                                                                  |
|---------------------|------------------------------------------------------------------------------|
| `make k6-ecommerce` | Load test all ecommerce endpoints (auth, search, assistant, health, cookies) |

---

## Related docs

- [Infrastructure Overview](OVERVIEW.md)
- [Setup & Prerequisites](SETUP.md)
- [Environment Variables & Secrets](SECRETS.md)
- [Deployment Guide](DEPLOYMENT.md)
- [Port Forwarding](PORT_FORWARDING.md)
