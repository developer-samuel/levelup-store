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
