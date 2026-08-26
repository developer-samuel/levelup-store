# 🕒 Composer: Misc Scripts

This file documents general-purpose utility Composer scripts defined in `composer.json`.

---

### serve

- **Command**: `php -S 127.0.0.1:8000 -t public`
- **Purpose**: Starts local PHP development server.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### scheduler:run

- **Command**: `php bin/console messenger:consume scheduler_default`
- **Purpose**: Runs all scheduled tasks via the Symfony Messenger scheduler transport.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

