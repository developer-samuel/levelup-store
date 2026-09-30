# ⚙️ Setup

## 1. Generate environment file

```bash
composer env:generate
```

Creates `.env.production` from `.env.production.example` and auto-generates secrets (`APP_SECRET`, `HMAC_SECRET`, `JWT_PASSPHRASE`, `MERCURE_JWT_SECRET`, `RABBITMQ_ERLANG_COOKIE`).

Fill in the remaining values in `.env.production` before running any `make` commands.

---

## 2. Infrastructure (Kubernetes deploy)

See [docs/infrastructure/SETUP.md](infrastructure/SETUP.md) for prerequisites, then [DEPLOYMENT.md](infrastructure/DEPLOYMENT.md) for the full deploy sequence.

---

## 3. Local development

See [Ecommerce Setup](../apps/ecommerce/docs/SETUP.md) · [Assistant Setup](../apps/assistant/docs/SETUP.md)
