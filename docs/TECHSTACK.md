# 🛠️ Technology Stack

This document covers the **platform-level** production infrastructure shared across all apps.

---

## Production Infrastructure

The production stack runs on a single Oracle Cloud ARM VM, managed entirely via code.

- **Cloud Provider:** Oracle Cloud Infrastructure (OCI) - ARM VM, VCN, Object Storage
- **DNS:** Cloudflare (managed via Terraform)
- **IaC:** Terraform (provisions OCI VM, VCN, networking, Cloudflare DNS records)
- **Configuration Management:** Ansible (k3s install, SSH hardening, fail2ban)
- **Container Orchestration:** k3s (lightweight Kubernetes)
- **GitOps:** ArgoCD (watches `main` branch, auto-syncs Helm releases)
- **Package Management:** Helm (all services deployed as Helm charts)
- **TLS:** cert-manager + Let's Encrypt (automatic certificate provisioning)
- **Autoscaling:** KEDA (scales worker pods based on RabbitMQ queue depth)
- **Backups:** Velero (daily K8s resource + PVC backups to OCI Object Storage)
- **Observability:** OpenTelemetry + Tempo (distributed tracing), Loki (logs), Prometheus + Grafana (metrics)
- **Runtime Security:** Falco (threat detection) + Kyverno (policy enforcement)
- **HTTP Monitoring:** Blackbox Exporter (Prometheus-compatible endpoint health checks)
- **Terraform Automation:** Atlantis (runs `terraform plan` on PRs touching `infrastructure/terraform/`)
- **Image Registry:** GHCR (GitHub Container Registry - built and pushed by `deploy.yml`)
- **Alerting:** AlertManager (Prometheus alert routing and notifications)
- **Secrets at rest:** Sealed Secrets (encrypts K8s secrets in git via `kubeseal`, 30-day key rotation)
- **Config reload:** Reloader (auto-restarts pods on ConfigMap/Secret changes)
- **k3s upgrades:** System Upgrade Controller (automated k3s version upgrades via upgrade plan CR)

---

See also: [Ecommerce Tech Stack](../apps/ecommerce/docs/TECHSTACK.md) · [Assistant Tech Stack](../apps/assistant/docs/TECHSTACK.md)
