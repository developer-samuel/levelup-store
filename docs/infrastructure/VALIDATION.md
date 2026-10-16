# Validation

---

## Local Validation

Run these manually to validate infrastructure changes before pushing. The pre-push hook runs them automatically when relevant files change.

### Install validation tools

```bash
# ansible-lint
sudo apt install ansible-lint

# kubeconform (offline Kubernetes manifest validation)
curl -sSL https://github.com/yannh/kubeconform/releases/latest/download/kubeconform-linux-amd64.tar.gz | tar xz -C /tmp
sudo mv /tmp/kubeconform /usr/local/bin/
```

### Run validation

```bash
# Helm
helm lint infrastructure/helm/apps/ecommerce
helm lint infrastructure/helm/apps/assistant

# Terraform
cd infrastructure/terraform/oracle && terraform validate

# Ansible (must run from infrastructure/ansible/ - ansible.cfg sets roles_path)
cd infrastructure/ansible && ansible-lint

# Kubernetes manifests (offline, no cluster needed)
kubeconform -ignore-missing-schemas infrastructure/kubernetes/
```

---

## Related docs

- [Infrastructure Overview](OVERVIEW.md)
- [Setup & Prerequisites](SETUP.md)
- [Environment Variables & Secrets](SECRETS.md)
- [Port Forwarding](PORT_FORWARDING.md)
- [Makefile Command Reference](COMMANDS.md)
