# 🛠️ Technology Stack

This document covers the **platform-level** production infrastructure shared across all apps.

For the full infrastructure stack with details see [Infrastructure Overview](infrastructure/OVERVIEW.md).

---

## Production Infrastructure

The production stack runs on a single Oracle Cloud ARM VM, managed entirely via code.

- **Cloud Provider:** Oracle Cloud Infrastructure (OCI) - ARM VM, VCN, Object Storage
- **DNS + WAF + DDoS:** Cloudflare (`proxied = true`, managed via Terraform)
- **IaC:** Terraform
- **Configuration Management:** Ansible
- **Container Orchestration:** k3s (lightweight Kubernetes)
- **GitOps:** ArgoCD
- **CI/CD:** GitHub Actions (automated) + Jenkins (manual deployment pipeline)
- **Package Management:** Helm
- **Image Registry:** GHCR
- **TLS:** cert-manager + Let's Encrypt
- **Autoscaling:** HPA (CPU), KEDA (RabbitMQ queue depth), VPA (resource recommendations)
- **Backups:** Velero (daily K8s + PVC backups to OCI Object Storage)
- **Observability:** OpenTelemetry + Tempo + Loki + Prometheus + Grafana + AlertManager
- **Runtime Security:** Falco + Kyverno
- **Secrets:** Sealed Secrets (at rest) + HashiCorp Vault + External Secrets Operator
- **WAF (ingress):** Coraza (Traefik plugin) + OWASP Core Rule Set
- **Terraform Automation:** Atlantis
- **Load Testing:** k6

---

See also: [Ecommerce Tech Stack](../apps/ecommerce/docs/TECHSTACK.md) · [Assistant Tech Stack](../apps/assistant/docs/TECHSTACK.md)
