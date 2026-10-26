# Server

## Current Setup

Single Oracle Cloud Free Tier ARM VM - **2 OCPU, 12 GB RAM**.

RAM is the primary constraint - Ollama (local LLM inference) alone requires several GB. The full stack is tuned to fit within 12 GB; all user-facing services run at all times, while some infrastructure tooling (e.g. Jenkins) is scaled to zero to conserve memory.

> **Note:** The current hardware is not suitable for high traffic. It is intended as a demo/portfolio environment. For production use with real load, see the recommended configurations below.

Resource configuration is in `infrastructure/terraform/oracle/variables.tf`.

---

## Recommended Configurations

### Without Ollama (no local LLM inference)

|           | Value |
|-----------|-------|
| **Nodes** | 1     |
| **OCPU**  | 4     |
| **RAM**   | 24 GB |

All services run comfortably without memory pressure. Jenkins and other tooling can be enabled without scaling to zero.

---

### With Ollama (local LLM inference)

Ollama running Mistral 7B requires ~8-10 GB RAM alone, making a single node insufficient.

|          | Node 1 (Ecommerce) | Node 2 (AI + Ollama) |
|----------|--------------------|----------------------|
| **OCPU** | 4                  | 4                    |
| **RAM**  | 24 GB              | 24 GB                |

Node 1 runs the ecommerce stack (PHP, PostgreSQL, Elasticsearch, Redis, RabbitMQ, monitoring).  
Node 2 runs the AI assistant, ChromaDB, and Ollama for local inference.

---

## Multi-node Setup (Future)

To scale to two nodes, the following changes are needed:

**Terraform** - split `infrastructure/terraform/oracle/` into two subdirectories (`ecommerce/` and `assistant/`), each with its own VM, networking and security list configuration. Add security list rules to allow k3s traffic between the two VMs (port 6443, 10250, 8472/UDP).

**Ansible** - provision node2 as a k3s agent that joins the node1 server. Add a second host to the inventory and a new playbook role for k3s agent setup.

**Kubernetes** - label each node and add `nodeSelector` to Helm charts:

```bash
kubectl label node <node1> role=ecommerce
kubectl label node <node2> role=assistant
```

Each Helm chart specifies which node it runs on via `nodeSelector` in its `values.yaml`:
```yaml
nodeSelector:
  role: ecommerce  # or: role: assistant
```

Node 1 (`role=ecommerce`): ecommerce app, PostgreSQL, Elasticsearch, Redis, RabbitMQ, monitoring.  
Node 2 (`role=assistant`): AI assistant, ChromaDB, Ollama.

**ArgoCD** - no changes needed, it syncs to the whole cluster automatically.

---

## Disabled services

The following services are disabled due to high resource requirements on the Free Tier node: Ollama, Jenkins, Falco, Coraza WAF.

---

## Also see

- [OVERVIEW.md](OVERVIEW.md) - full infrastructure overview
- [infrastructure/terraform/oracle/variables.tf](../../infrastructure/terraform/oracle/variables.tf) - resource configuration
