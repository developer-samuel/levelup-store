# Infrastructure Overview

This document describes the production infrastructure stack for LevelUp Store.
The full stack runs on a single Oracle Cloud ARM VM managed by Terraform, configured by Ansible,
and orchestrated by k3s (lightweight Kubernetes).

---

## Stack

| Layer                    | Tool                                        | Purpose                                                                                    |
|--------------------------|---------------------------------------------|--------------------------------------------------------------------------------------------|
| Cloud VM                 | Terraform + OCI                             | Provisions ARM VM, VCN, networking                                                         |
| DNS                      | Cloudflare (managed via Terraform)          | DNS records for all domains                                                                |
| OS configuration         | Ansible                                     | k3s install, SSH hardening, fail2ban                                                       |
| Container orchestration  | k3s                                         | Lightweight Kubernetes on the VM                                                           |
| GitOps                   | ArgoCD                                      | Watches GitHub repo, auto-syncs Helm releases                                              |
| Package management       | Helm                                        | All services deployed as Helm charts                                                       |
| Image registry           | GHCR                                        | Docker images built and pushed by `deploy.yml`                                             |
| Secrets (app)            | ArgoCD app params                           | Passed directly to pods via `make ecommerce-secrets` / `make assistant-secrets`            |
| Secrets (services)       | kubectl                                     | Raw K8s secrets for PostgreSQL, Redis, RabbitMQ, MinIO                                     |
| TLS                      | cert-manager + Let's Encrypt                | Automatic certificate provisioning                                                         |
| Backups                  | Velero                                      | Daily K8s resource + PVC backups to OCI Object Storage                                     |
| Autoscaling (horizontal) | HPA (Horizontal Pod Autoscaler)             | Scales ecommerce and assistant pods based on CPU utilization                               |
| Autoscaling (event)      | KEDA                                        | Scales worker pods based on RabbitMQ queue depth                                           |
| Resource recommendations | VPA (Vertical Pod Autoscaler)               | Recommends CPU/memory requests for ecommerce and assistant pods                            |
| CI/CD (manual)           | Jenkins                                     | Manual deployment pipeline (`levelup-store-deploy`) - build, staging, approval, production |
| Observability            | OTel + Tempo + Loki + Prometheus + Grafana  | Traces, logs, metrics + alerting                                                           |
| Alerting                 | AlertManager                                | Prometheus alert routing and notifications                                                 |
| HTTP monitoring          | Blackbox Exporter                           | Prometheus-compatible endpoint health checks                                               |
| WAF (edge)               | Cloudflare WAF + DDoS protection            | Filters malicious traffic at the Cloudflare edge before it reaches the server              |
| WAF (ingress)            | Coraza (Traefik plugin) + OWASP CRS         | Second WAF layer inside the cluster; blocks SQLi, XSS, path traversal and malicious bots  |
| Rate limiting            | Traefik RateLimit middleware                | 300 req/min (burst 100) per IP - protects against DoS, brute-force and scraping            |
| Circuit breaker          | Traefik CircuitBreaker middleware           | Cuts off assistant traffic on high error rate or latency, keeps ecommerce unaffected       |
| Access control           | Traefik IPAllowList + Tailscale             | Jenkins and ArgoCD restricted to Tailscale network only                                    |
| Security headers         | Traefik headers middleware                  | CSP, HSTS, X-Frame-Options, CORS, Permissions-Policy on all ingress routes                 |
| WWW redirect             | Traefik redirectRegex middleware            | Redirects www.domain → domain (ecommerce only)                                             |
| Security scanning        | Falco + Kyverno                             | Runtime threat detection + policy enforcement                                              |
| Vulnerability scanning   | Trivy (in deploy.yml)                       | Docker image scanning on every deploy                                                      |
| Terraform automation     | Atlantis                                    | Runs `terraform plan` on PRs touching `terraform/`                                         |
| Secrets at rest          | Sealed Secrets (Bitnami)                    | Encrypts K8s secrets in git via `kubeseal`, 30-day key rotation                            |
| Secrets management       | HashiCorp Vault + External Secrets Operator | Centralized secret store with KV-v2, Kubernetes auth, auto-unseal                          |
| Config reload            | Reloader (Stakater)                         | Auto-restarts pods when ConfigMap or Secret changes                                        |
| k3s upgrades             | System Upgrade Controller                   | Automated k3s version upgrades via upgrade plan CR                                         |
| Load testing             | k6                                          | Load test scripts for all ecommerce endpoints - run via `make k6-ecommerce`                |

---

## Repository layout

| Directory     | Contains                                                          |
|---------------|-------------------------------------------------------------------|
| `terraform/`  | OCI VM, VCN, networking, Cloudflare DNS records                   |
| `ansible/`    | Playbooks and roles: k3s install, OS hardening, storage           |
| `helm/`       | One subfolder per Helm release - values only, charts vendored     |
| `kubernetes/` | ArgoCD Application manifests, cluster resources, Kyverno policies |
| `k6/`         | Load test scripts for ecommerce endpoints - run via `make k6-ecommerce` |
| `make/`       | Makefile modules split by domain (deploy, secrets, cluster, etc.) |

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

## 📊 Diagrams

- [Production Architecture](../diagrams/graphs/architecture/architecture.mmd)
- [System Context](../diagrams/graphs/architecture/system-context.mmd)
- [Provisioning](../diagrams/graphs/architecture/provisioning.mmd)
- [GitOps Flow](../diagrams/graphs/architecture/gitops.mmd)
- [CI/CD Deployment](../diagrams/graphs/architecture/deployment.mmd)

---

## Related docs

- [Setup & Prerequisites](SETUP.md)
- [Environment Variables & Secrets](SECRETS.md)
- [Deployment Guide](DEPLOYMENT.md)
- [Makefile Command Reference](COMMANDS.md)
- [Server Setup & Recommendations](SERVER.md)
