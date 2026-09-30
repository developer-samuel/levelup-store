# Setup & Prerequisites

---

## Required tools

Install all tools before running any `make` command:

```bash
make -C infrastructure check-deps    # verifies all required tools are present
make -C infrastructure install-deps  # installs missing tools (Homebrew/apt)
```

| Tool         | Minimum version | Purpose                         |
|--------------|-----------------|---------------------------------|
| `terraform`  | >= 1.5.0        | Provision OCI VM                |
| `ansible`    | >= 2.15         | Configure VM (k3s, hardening)   |
| `kubectl`    | >= 1.28         | Manage K8s resources            |
| `helm`       | >= 3.12         | Deploy Helm charts              |
| `argocd` CLI | >= 2.9          | Set app secrets, manage apps    |
| `velero` CLI | >= 1.13         | Trigger/inspect backup restores |
| `make`       | any             | Run infrastructure commands     |

---

## SSH key

All Ansible playbooks and Terraform use the same SSH key.
Generate one if you don't have it:

```bash
ssh-keygen -t ed25519 -f ~/.ssh/id_ed25519
```

The path is configured via `ANSIBLE_SSH_KEY` in `.env`.

---

## OCI API key

Required for Terraform to provision resources on Oracle Cloud.

1. Log into OCI Console → Profile → API Keys → Add API Key
2. Download the private key to `~/.oci/oci_api_key.pem`
3. Copy the fingerprint shown after adding the key

These values go into `.env` as `TF_VAR_fingerprint` and `TF_VAR_private_key_path`.

---

## Environment file

The Makefile loads `.env.production` from the repo root.

Generate it with:

```bash
composer env:generate
```

This copies `.env.production.example` → `.env.production` and auto-generates empty secrets.
Fill in the remaining values before running any `make` command.

See [SECRETS.md](SECRETS.md) for a full breakdown of every variable.

---

## Cloudflare

Terraform manages DNS records via the Cloudflare provider.
You need:
- `TF_VAR_cloudflare_api_token` - API token with Zone:Edit permissions
- `TF_VAR_cloudflare_zone_id` - Zone ID from Cloudflare dashboard (Overview page, right sidebar)

---

## Terraform remote state

By default Terraform uses local state. To enable remote state on OCI Object Storage:

1. Create an OCI bucket (e.g. `levelup-store-tfstate`, Private) in OCI Console → Object Storage
2. Copy `secrets/backend.config.hcl.example` → `secrets/backend.config.hcl` and fill in bucket, namespace, region
3. Copy `secrets/backend.credentials.hcl.example` → `secrets/backend.credentials.hcl` and fill in access/secret key
4. Run `make -C infrastructure tf-init-remote`

Without remote state, `terraform.tfstate` stays local - do not commit it.
