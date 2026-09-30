# ⚙️ Composer: Cache Scripts

This file documents all custom cache-related Composer scripts defined in `composer.json`.

---
```bash
cd apps/ecommerce
```


### cache:clear

- **Command**: `bin/run php bin/console cache:clear`
- **Purpose**: Clears Symfony cache (dev, prod, or local) to ensure a fresh runtime environment.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---
```bash
cd apps/ecommerce
```


### cache:warmup

- **Command**: `bin/run php bin/console cache:warmup`
- **Purpose**: Warms up Symfony cache to speed up app load and prevent first-request delays.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.
