# Infrastructure Overview

This document describes the production infrastructure stack for LevelUp Store.
The full stack runs on a single Oracle Cloud ARM VM managed by Terraform, configured by Ansible,
and orchestrated by k3s (lightweight Kubernetes).

---

## Stack

| Layer                   | Tool                                       | Purpose                                                         |
|-------------------------|--------------------------------------------|-----------------------------------------------------------------|
| Cloud VM                | Terraform + OCI                            | Provisions ARM VM, VCN, networking                              |
| DNS                     | Cloudflare (managed via Terraform)         | DNS records for all domains                                     |
| OS configuration        | Ansible                                    | k3s install, SSH hardening, fail2ban                            |
| Container orchestration | k3s                                        | Lightweight Kubernetes on the VM                                |
| GitOps                  | ArgoCD                                     | Watches GitHub repo, auto-syncs Helm releases                   |
| Package management      | Helm                                       | All services deployed as Helm charts                            |
| Image registry          | GHCR                                       | Docker images built and pushed by `deploy.yml`                  |
| Secrets (app)           | ArgoCD app params                          | Passed directly to pods via `make secrets`                      |
| Secrets (services)      | kubectl                                    | Raw K8s secrets for PostgreSQL, Redis, RabbitMQ, MinIO          |
| TLS                     | cert-manager + Let's Encrypt               | Automatic certificate provisioning                              |
| Backups                 | Velero                                     | Daily K8s resource + PVC backups to OCI Object Storage          |
| Autoscaling             | KEDA                                       | Scales worker pods based on RabbitMQ queue depth                |
| Observability           | OTel + Tempo + Loki + Prometheus + Grafana | Traces, logs, metrics + alerting                                |
| Alerting                | AlertManager                               | Prometheus alert routing and notifications                      |
| HTTP monitoring         | Blackbox Exporter                          | Prometheus-compatible endpoint health checks                    |
| Security scanning       | Falco + Kyverno                            | Runtime threat detection + policy enforcement                   |
| Vulnerability scanning  | Trivy (in deploy.yml)                      | Docker image scanning on every deploy                           |
| Terraform automation    | Atlantis                                   | Runs `terraform plan` on PRs touching `terraform/`              |
| Secrets at rest         | Sealed Secrets (Bitnami)                   | Encrypts K8s secrets in git via `kubeseal`, 30-day key rotation |
| Config reload           | Reloader (Stakater)                        | Auto-restarts pods when ConfigMap or Secret changes             |
| k3s upgrades            | System Upgrade Controller                  | Automated k3s version upgrades via upgrade plan CR              |

---

## Repository layout

| Directory     | Contains                                                          |
|---------------|-------------------------------------------------------------------|
| `terraform/`  | OCI VM, VCN, networking, Cloudflare DNS records                   |
| `ansible/`    | Playbooks and roles: k3s install, OS hardening, storage           |
| `helm/`       | One subfolder per Helm release - values only, charts vendored     |
| `kubernetes/` | ArgoCD Application manifests, cluster resources, Kyverno policies |

> Run `tree infrastructure/` for the full current layout.

---

## GitOps flow

```
git push → GitHub Actions CI → build Docker image → push to GHCR
       → update values.prod.yaml (image tag) → ArgoCD detects change
       → ArgoCD syncs Helm release → K8s rolling update
```

ArgoCD watches the `main` branch. Any change to `infrastructure/helm/` triggers an automatic sync.
No manual `helm upgrade` is ever needed after initial setup.

---

## Related docs

- [Setup & Prerequisites](SETUP.md)
- [Environment Variables & Secrets](SECRETS.md)
- [Deployment Guide](DEPLOYMENT.md)
- [Makefile Command Reference](COMMANDS.md)
