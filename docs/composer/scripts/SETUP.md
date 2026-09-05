# ⚙️ Composer: Setup Scripts

This file documents environment and setup Composer scripts defined in `composer.json`.

---

### env:generate

- **Command**: `bin/run php scripts/symfony/env-generate/launcher.php`
- **Purpose**: Generates `.env` file from `.env.example` if it does not exist.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### env:secret

- **Command**: `bin/run php scripts/symfony/env-secret/launcher.php`
- **Purpose**: Generates `APP_SECRET`, `HMAC_SECRET` and `JWT_PASSPHRASE` into `.env` if they are empty.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### secrets:generate

- **Command**: `bin/run php bin/console secrets:generate-keys`
- **Purpose**: Generates Symfony Secrets Vault keypair into `config/secrets/dev/`.
- **Note**: Requires `.env` to exist with `APP_ENV` set. Run after `env:generate`.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### jwt:generate

- **Command**: `bin/run php scripts/symfony/jwt-generate/launcher.php`
- **Purpose**: Generates JWT RSA keypair (`private.pem`, `public.pem`) into `config/jwt/` using `JWT_PASSPHRASE` from `.env`. Always regenerates - old keys are removed first.
- **Note**: Requires `JWT_PASSPHRASE` to be set in `.env`. Run after `env:secret`.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### var:prepare

- **Command**: `bin/run php scripts/tasks/prepare-var/launcher.php`
- **Purpose**: Cleans stale `var/` directories and recreates required ones (`cache`, `log`, `sessions`, `tmp`, `tools`).
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### permissions:set

- **Command**: `bin/run php scripts/tasks/set-permissions/launcher.php`
- **Purpose**: Sets correct file and directory permissions for the project (e.g. `var/`, scripts).
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### temp:prepare

- **Command**: `bin/run php scripts/tasks/prepare-temp/launcher.php`
- **Purpose**: Creates `var/tmp` directory if it does not exist.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### assets:prepare

- **Command**: `bin/run php scripts/tasks/prepare-assets/launcher.php`
- **Purpose**: Creates `assets/controllers` directory if it does not exist.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### uml:generate

- **Command**: `bin/run php scripts/tasks/generate-uml/launcher.php`
- **Purpose**: Generates SVG diagrams from all `.mmd` source files in `docs/diagrams/` into `.uml/`. Clears existing SVGs before each run.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.
