# 🗃️ Composer: Database Scripts

This file documents all custom database-related Composer scripts defined in `composer.json`.

---

### db-setup

- **Command**: `scripts/symfony/database/launcher.php`
- **Purpose**: Runs the full database setup including creation, dropping tables, migration, and seeding.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

### elasticsearch:reindex

- **Command**: `php bin/console app:elasticsearch:reindex`
- **Purpose**: Reindexes all Elasticsearch indexes (ProductVariant, Order, Review, User). Run after `db-setup` or when ES data is out of sync.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.
- **Note**: Requires `ELASTICSEARCH_ENABLED=true` in `.env`, otherwise skipped with a warning.
