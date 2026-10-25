# Secret Scanning

---

## Gitleaks

Scan the repo for accidentally committed secrets before pushing.

Install:

```bash
curl -sSL https://github.com/gitleaks/gitleaks/releases/latest/download/gitleaks_$(uname -s)_$(uname -m).tar.gz | tar xz -C /tmp
sudo mv /tmp/gitleaks /usr/local/bin/
```

Run:

```bash
gitleaks detect --no-banner
```

> Also runs automatically on every pre-push via `.githooks/pre-push` and on every PR in CI.

---

## Related docs

- [Security](SECURITY_ASSURANCE.md)
- [Environment Variables & Secrets](infrastructure/SECRETS.md)
