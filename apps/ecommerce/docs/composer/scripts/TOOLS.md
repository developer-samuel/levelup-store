# 🧠 Composer: Tools Scripts

This file documents all custom Composer scripts related to static analysis, metrics, refactoring, code formatting, and linting defined in `composer.json`.

---
```bash
cd apps/ecommerce
```


## 🛡️ Quality

### deptrac

- **Command**: `bin/run php scripts/tools/deptrac/launcher.php`
- **Purpose**: Ensures proper layer dependencies and prevents forbidden coupling.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

### php-md

- **Command**: `bin/run php vendor/bin/phpmd src packages/server xml phpmd.xml`
- **Purpose**: Detects potential problems, unused code, and complexity issues.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`: for long-running analysis.

### php-stan

- **Command**: `bin/run php vendor/bin/phpstan analyse --memory-limit=1G`
- **Purpose**: Static analysis tool for catching type errors, invalid code, and potential bugs (Level 10, maximum strictness).
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`: for long-running analysis.

### sonar

- **Command**: `bin/run php scripts/tools/sonar/launcher.php`
- **Purpose**: Runs SonarQube static analysis scan against the configured SonarQube server. Requires `SONAR_HOST` and `SONAR_TOKEN` in `.env`.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---
```bash
cd apps/ecommerce
```


## 📊 Metrics / Statistics

### php-metrics

- **Command**: `bin/run php scripts/tools/php-metrics/launcher.php`
- **Purpose**: Generates maintainability and complexity metrics for the codebase.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

### pdepend

- **Command**: `bin/run php scripts/tools/pdepend/launcher.php`
- **Purpose**: Analyzes dependencies and code metrics for better architecture understanding.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---
```bash
cd apps/ecommerce
```


## 🎨 Formatting

### php-cs-fixer:fix

- **Command**: `bin/run --root php scripts/tools/php-cs-fixer/launcher.php`
- **Purpose**: Automatically formats PHP code to comply with defined coding standards.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`: for long-running formatting tasks.

---
```bash
cd apps/ecommerce
```


## 🛠️ Refactoring

### rector:fix

- **Command**: `bin/run --root php vendor/bin/rector process`
- **Purpose**: Performs automated code refactoring and modernization.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`: for long-running refactoring tasks.

---
```bash
cd apps/ecommerce
```


## 🔍 Linting

### twig:lint

- **Command**: `bin/run php bin/console twig:lint templates/`
- **Purpose**: Validates all Twig templates for syntax errors, unknown filters, and unknown functions. Does **not** verify that imported file paths exist.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.
