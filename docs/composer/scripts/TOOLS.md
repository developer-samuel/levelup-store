# 🧠 Composer: Tools Scripts

This file documents all custom Composer scripts related to static analysis, metrics, refactoring, code formatting, and linting defined in `composer.json`.

---

## 🛡️ Quality

### deptrac

- **Command**: `php scripts/tools/deptrac/launcher.php`
- **Purpose**: Ensures proper layer dependencies and prevents forbidden coupling.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

### php-md

- **Command**: `phpmd src kit xml phpmd.xml`
- **Purpose**: Detects potential problems, unused code, and complexity issues.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`: for long-running analysis.

### php-stan

- **Command**: `phpstan analyse --memory-limit=1G`
- **Purpose**: Static analysis tool for catching type errors, invalid code, and potential bugs (Level 10, maximum strictness).
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`: for long-running analysis.

### sonar

- **Command**: `php scripts/tools/sonar/launcher.php`
- **Purpose**: Runs SonarQube static analysis scan against the configured SonarQube server. Requires `SONAR_HOST` and `SONAR_TOKEN` in `.env`.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

## 📊 Metrics / Statistics

### php-cpd

- **Command**: `phpcpd src database kit`
- **Purpose**: Detects copy-pasted code and duplicate blocks to improve maintainability.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

### php-loc

- **Command**: `phploc src database kit`
- **Purpose**: Counts lines of PHP code, measures project size, structure, and complexity to track maintainability.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

### php-metrics

- **Command**: `php scripts/tools/php-metrics/launcher.php`
- **Purpose**: Generates maintainability and complexity metrics for the codebase.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

### pdepend

- **Command**: `php scripts/tools/pdepend/launcher.php`
- **Purpose**: Analyzes dependencies and code metrics for better architecture understanding.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.

---

## 🎨 Formatting

### php-cs-fixer:fix

- **Command**: `php scripts/tools/php-cs-fixer/launcher.php`
- **Purpose**: Automatically formats PHP code to comply with defined coding standards.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`: for long-running formatting tasks.

---

## 🛠️ Refactoring

### rector:fix

- **Command**: `rector process`
- **Purpose**: Performs automated code refactoring and modernization.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`: for long-running refactoring tasks.

---

## 🔍 Linting

### lint:twig

- **Command**: `php bin/console lint:twig templates/`
- **Purpose**: Validates all Twig templates for syntax errors, unknown filters, and unknown functions. Does **not** verify that imported file paths exist.
- **Timeout Disabled** via `Composer\\Config::disableProcessTimeout`.
