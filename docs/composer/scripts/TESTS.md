# 🧪 Composer: Test Scripts

This file documents all custom test-related Composer scripts defined in `composer.json`.

---

### php-unit

- **Command**: `bin/run php scripts/tools/php-unit/launcher.php`
- **Purpose**: Runs all PHPUnit tests.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.
