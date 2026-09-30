# ARCHITECTURE

> This document describes the **platform-level** architecture shared across all apps.

## 🏗️ Principles

- **GitOps** - All infrastructure and deployments are managed via git. ArgoCD watches `main` and auto-syncs.
- **Infrastructure as Code** - Terraform provisions OCI resources and Cloudflare DNS. Ansible configures the VM.
- **Single-Node Kubernetes** - K3s runs on a single OCI ARM VM. All services deployed as Helm charts.
- **Dual-App Platform** - Ecommerce (PHP/Symfony) and Assistant (Python/FastAPI) run in the same cluster.
- **Supply Chain Security** - All production images are signed (cosign/Sigstore) with SBOM attestation.

---

## 📊 Diagrams

### Platform
- [System Context](../diagrams/graphs/architecture/system-context.mmd)
- [Production Architecture](../diagrams/graphs/architecture/architecture.mmd)
- [Deployment Pipeline](../diagrams/graphs/architecture/deployment.mmd)
- [GitOps Flow](../diagrams/graphs/architecture/gitops.mmd)
- [Provisioning](../diagrams/graphs/architecture/provisioning.mmd)

---

See also: [Ecommerce Architecture](../../apps/ecommerce/docs/runtime/ARCHITECTURE.md) · [Assistant Architecture](../../apps/assistant/docs/runtime/ARCHITECTURE.md)
