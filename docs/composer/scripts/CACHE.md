# ⚙️ Composer: Cache Scripts

This file documents all custom cache-related Composer scripts defined in `composer.json`.

---

### cache:clear

- **Command**: `bin/run php bin/console cache:clear`
- **Purpose**: Clears Symfony cache (dev, prod, or local) to ensure a fresh runtime environment.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### cache:warmup

- **Command**: `bin/run php bin/console cache:warmup`
- **Purpose**: Warms up Symfony cache to speed up app load and prevent first-request delays.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.
