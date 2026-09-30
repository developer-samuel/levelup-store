# ⚡ Composer Scripts Overview

Root-level `composer.json` scripts. These run across the entire monorepo.

---

### 📊 CodeStats Scripts

Count source code statistics across the entire monorepo.

| Script        | Command                      | Description                        |
|---------------|------------------------------|------------------------------------|
| `count-stats` | `php vendor/bin/count-stats` | Run all count scripts at once      |
| `count-files` | `php vendor/bin/count-files` | Count total number of source files |
| `count-lines` | `php vendor/bin/count-lines` | Count total lines of code          |
| `count-chars` | `php vendor/bin/count-chars` | Count total characters             |
