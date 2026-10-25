# Validation

---

## Local Validation

Run these manually to validate infrastructure changes before pushing. The pre-push hook runs them automatically when relevant files change.

---

### Helm

```bash
helm lint infrastructure/helm/apps/ecommerce
helm lint infrastructure/helm/apps/assistant
```

---

### Terraform

```bash
cd infrastructure/terraform/oracle && terraform validate
```

---

### Ansible

Requires `ansible-lint`:

```bash
sudo apt install ansible-lint
```

```bash
# Must run from infrastructure/ansible/ - ansible.cfg sets roles_path
cd infrastructure/ansible && ansible-lint
```

---

### Kubernetes manifests

Requires `kubeconform` (offline validation, no cluster needed):

```bash
curl -sSL https://github.com/yannh/kubeconform/releases/latest/download/kubeconform-linux-amd64.tar.gz | tar xz -C /tmp
sudo mv /tmp/kubeconform /usr/local/bin/
```

```bash
kubeconform -ignore-missing-schemas infrastructure/kubernetes/
```

---

## Related docs

- [Infrastructure Overview](OVERVIEW.md)
- [Setup & Prerequisites](SETUP.md)
- [Environment Variables & Secrets](SECRETS.md)
- [Port Forwarding](PORT_FORWARDING.md)
- [Makefile Command Reference](COMMANDS.md)
