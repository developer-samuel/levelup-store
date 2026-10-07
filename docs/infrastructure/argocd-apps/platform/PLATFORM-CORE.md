# Platform

> See also: [Makefile Command Reference](../../COMMANDS.md)

Core platform components - secrets, autoscaling, certificates, policies.

---

## vault (HashiCorp Vault)

Secrets management.

| Command                        | Description                                      |
|--------------------------------|--------------------------------------------------|
| `vault-start/down/stop/unsync` | Lifecycle                                        |
| `vault-status/sync/pods/logs`  | ArgoCD status                                    |
| `vault-seal-status`            | Vault seal/init/HA status - from `make/vault.mk` |

---

## cert-manager

TLS certificate management (Let's Encrypt).

| Command                               | Description                       |
|---------------------------------------|-----------------------------------|
| `cert-manager-start/down/stop/unsync` | Lifecycle                         |
| `cert-manager-status/sync/pods/logs`  | Status                            |
| `cert-manager-certs`                  | List all certificates with expiry |
| `cert-manager-issuers`                | List ClusterIssuers and Issuers   |

---

## external-secrets

Syncs secrets from Vault to Kubernetes Secrets.

| Command                                   | Description                               |
|-------------------------------------------|-------------------------------------------|
| `external-secrets-start/down/stop/unsync` | Lifecycle                                 |
| `external-secrets-status/sync/pods/logs`  | Status                                    |
| `external-secrets-stores`                 | List SecretStores and ClusterSecretStores |
| `external-secrets-sync-status`            | Show sync status of all ExternalSecrets   |

---

## sealed-secrets

Encrypted secrets stored in Git.

| Command                                 | Description |
|-----------------------------------------|-------------|
| `sealed-secrets-start/down/stop/unsync` | Lifecycle   |
| `sealed-secrets-status/sync/pods/logs`  | Status      |

---

## keda

Event-driven autoscaling (workers, schedulers).

| Command                       | Description                                          |
|-------------------------------|------------------------------------------------------|
| `keda-start/down/stop/unsync` | Lifecycle                                            |
| `keda-status/sync/pods/logs`  | Status                                               |
| `keda-scaledobjects`          | List all ScaledObjects with current/desired replicas |
| `keda-triggers`               | List all TriggerAuthentications                      |

---

## vpa

Vertical Pod Autoscaler - resource recommendations.

| Command                      | Description |
 ------------------------------|-------------|
| `vpa-start/down/stop/unsync` | Lifecycle   |
| `vpa-status/sync/pods/logs`  | Status      |

> VPA recommendations for apps: `make levelup-store-vpa` / `make levelup-store-assistant-vpa`

---

## reloader

Auto-restarts pods when ConfigMap or Secret changes.

| Command                           | Description |
|-----------------------------------|-------------|
| `reloader-start/down/stop/unsync` | Lifecycle   |
| `reloader-status/sync/pods/logs`  | Status      |

---

## kyverno + kyverno-policies

Kubernetes policy engine.

| Command                                   | Description                      |
|-------------------------------------------|----------------------------------|
| `kyverno-start/down/stop/unsync`          | Lifecycle                        |
| `kyverno-status/sync/pods/logs`           | Status                           |
| `kyverno-policies-start/down/stop/unsync` | Lifecycle for policy definitions |
| `kyverno-policies-status/sync`            | Status of policy definitions     |
