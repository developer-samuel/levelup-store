# 🔒 Project Requirements

**Purpose:** Minimum requirements for development, testing, and optional `Docker` services.

---

## 1. Mandatory Requirements

- **PHP** 8.2 or 8.3
- **Composer** for dependency management
- **Node.js** (LTS) + **pnpm** or **npm** for frontend assets (Vite, TS build)
- **Git** version control
- **Database** (choose at least one)
  - PostgreSQL 17+ (recommended)
  - MySQL 8.0+
- **Web server** (choose at least one)
  - Nginx (recommended)
  - Apache
- **Cron / Scheduler** required for Symfony scheduled tasks
- **wkhtmltopdf** required for PDF generation
- **SMTP account** custom or test (MailHog / Mailtrap)
- **Stripe account** test keys for payment workflows
- **External API Access** connectivity to [apicountries.com](https://www.apicountries.com/countries) for data ingestion

> ⚠️ At least one web server and one database are required for application functionality.

---

## 2. Optional Requirements (Recommended)

These improve developer experience, monitoring, or enable optional features. Not required for core application functionality:

- **Docker** for containerized environment
- **WSL 2** strongly recommended for **Windows users** to run Docker and Linux-based tools (Ubuntu) with native performance.
- **Symfony CLI** for local development commands
- **Make** (GNU Make) required to run project commands
- **PHP Coverage Driver** (choose based on need):
  - **PCOV** - recommended for coverage only (faster, always active, no debugging)
  - **Xdebug** - required for step debugging (breakpoints in IDE); can coexist with PCOV when `XDEBUG_MODE=off`
- **Elasticsearch** for full-text product search and filtering
- **Redis** for caching, sessions, rate limiting
- **RabbitMQ** for async message queue (email delivery, background tasks)
- **pgAdmin** lightweight DB management tool
- **Prometheus** metrics collection
- **Grafana** dashboards & monitoring

---

## 3. Notes

- `Docker` is optional; all services can run locally if preferred
- `wkhtmltopdf` must be installed either locally or in Docker for PDF generation
- Optional services (Elasticsearch, Redis, RabbitMQ, pgAdmin, Prometheus, Grafana) improve developer experience or monitoring but are not required to run the app - when disabled, Elasticsearch falls back to database queries and Symfony Messenger falls back to a Doctrine-based queue
- SMTP and Stripe can be sandbox/test accounts for development
- **For a comprehensive overview of the full [Tech Stack](docs/TECHSTACK.md), architecture, and all Quality Assurance tools, please refer to the documentation.**
