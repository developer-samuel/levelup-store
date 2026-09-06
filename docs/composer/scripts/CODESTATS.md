# 📊 Composer: CodeStats Scripts

This file documents CodeStats-related Composer scripts defined in `composer.json`.

---

### count-stats

- **Command**: `bin/run php vendor/bin/count-stats`
- **Purpose**: Runs all stats at once - files, lines, and characters combined.
- **Timeout Disabled** via `Composer\Config::disableProcessTimeout`.

---

### count-files

- **Command**: `bin/run php vendor/bin/count-files`
- **Purpose**: Counts total number of files in the project.
- **Timeout Disabled** via `Composer\Config::disableProcessTimeout`.

---

### count-lines

- **Command**: `bin/run php vendor/bin/count-lines`
- **Purpose**: Counts total number of lines across project files.
- **Timeout Disabled** via `Composer\Config::disableProcessTimeout`.

---

### count-chars

- **Command**: `bin/run php vendor/bin/count-chars`
- **Purpose**: Counts total number of characters across project files.
- **Timeout Disabled** via `Composer\Config::disableProcessTimeout`.
