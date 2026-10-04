# 🛠️ Ecommerce Technology Stack

This document provides a comprehensive overview of the technologies, frameworks, and libraries used in the ecommerce app.

---

## 1. Backend

- **Language:** PHP 8.3
- **Framework:** Symfony
- **Dependency Manager:** Composer
- **Template Engine:** Twig
- **Authentication:** JWT (LexikJWTAuthenticationBundle)
- **Database Abstraction:** Doctrine ORM
- **Migration Tool:** Doctrine Migrations
- **Search:** Elasticsearch (product search and filtering)
- **Async Messaging:** Symfony Messenger (AMQP via RabbitMQ / Doctrine fallback)
- **Task Scheduling:** Symfony Scheduler & Cron
- **Emailing:** Symfony Mailer (SMTP)
- **Real-time:** Mercure (SSE hub for server-sent events)
- **API Documentation:** nelmio/api-doc-bundle (Swagger UI at `/api/dev/docs`, local/dev only, PHP 8 attributes)

---

## 2. Frontend & Design

- **Runtime:** Node.js (LTS)
- **Package Manager:** pnpm
- **Build Tool:** Vite
- **Asset Management:** Symfony AssetMapper
- **TypeScript**: Vanilla TypeScript
- **Styling:** SCSS

---

## 3. Infrastructure & Services

- **Containerization:** Docker & Docker Compose
- **Web Server:** Nginx (Primary) / Apache (Support)
- **Version Control:** Git
- **Database:** PostgreSQL (Primary) / MySQL (Secondary)
- **Search Engine:** Elasticsearch (full-text product search and filtering)
- **Caching & Storage:** Redis (cache, sessions, rate limiting)
- **Message Broker:** RabbitMQ (async email queue via Symfony Messenger)
- **External API**: REST API [apicountries.com](https://www.apicountries.com/countries) for country data ingestion
- **Bot Protection:** Cloudflare Turnstile (CAPTCHA for login, signup, and password reset)
- **Payment Gateway:** Stripe API
- **PDF Generation:** wkhtmltopdf
- **Error Monitoring:** Sentry
- **Object Storage:** MinIO (S3-compatible, pluggable via Flysystem)
- **Real-time Hub:** Mercure (server-sent events for stock and review updates)
- **Monitoring:** Prometheus, Grafana, Loki, AlertManager
- **Dev Tools:** Mailpit (local email), Dozzle (container logs), SonarQube (static analysis), Elasticvue (Elasticsearch UI), pgAdmin (PostgreSQL UI), Alloy (local log/metric collection for Loki + Prometheus)
- **CI/CD:** GitHub Actions (see [DEVOPS.md](runtime/DEVOPS.md) for full pipeline details)

---

## 4. Documentation & Modeling

- The project includes comprehensive **documentation**
- **UML diagrams** are used throughout the project via:
  - **Class Diagrams** - for entities, database tables, attributes, and relationships.
  - **Flowcharts** - for processes and logic.
  - **Graphs** - for architecture and system visualization.
- Diagrams are created in **Markdown / Mermaid**, making them easy to maintain and update.

---

## 5. Data & Configuration

- **Configuration:** YAML (Symfony services, routing, deptrac.yaml, etc.)
- **Data Exchange:** JSON
- **QA Configuration:** XML (phpmd.xml, phpunit.xml.dist)
- **Environment:** Dotenv (.env files for secrets)

---

## 6. Automation & Tooling

- **Command Runner:** Makefile
- **Dependency Automation:** Renovate (automated dependency update PRs for Composer, pnpm, Docker, Helm)
- **Scripts:** Bash, PHP, Tools
- **Code Statistics:** Custom PHP tools for files, rows, and characters counting

---

## 7. Quality Assurance

We maintain 100% focus on code quality using these tools:

### Backend QA

| Tool                  | Purpose                                        | Execution (via Composer)  |
|-----------------------|------------------------------------------------|---------------------------|
| Deptrac               | Architectural dependency enforcement           | composer deptrac          |
| PHPMD                 | PHP Mess Detector (using `phpmd.xml`)          | composer php-md           |
| PHPStan               | Static analysis (Level 10+)                    | composer php-stan         |
| PHPMetrics            | Visual quality metrics and complexity analysis | composer php-metrics      |
| PDepend               | Design metrics and software artifacts          | composer pdepend          |
| PHP CS Fixer          | Coding standards enforcement                   | composer php-cs-fixer:fix |
| Rector                | Automated refactoring and upgrades             | composer rector:fix       |
| PHPUnit               | Unit, Integration and Feature testing          | composer php-unit         |
| SonarQube             | Local static analysis dashboard                | composer sonar            |

### Frontend QA

| Tool              | Purpose                                   | Execution                      |
|-------------------|-------------------------------------------|--------------------------------|
| Vitest            | Unit, Integration and Functional testing  | pnpm vitest                    |
| Playwright        | End-to-end testing                        | pnpm e2e                       |
| TypeScript        | Static type checking                      | pnpm type-check                |
| ESLint + Prettier | TS linting and automated code formatting  | pnpm lint                      |
| Stylelint SCSS    | Stylesheet quality control                | pnpm lint-scss                 |
| SLOC              | Source Lines of Code analysis (TS & SCSS) | npx sloc assets/ts assets/scss |

### CI/CD Tooling

Tools that run exclusively in GitHub Actions pipelines - not available as local CLI commands.

| Tool                     | Purpose                                               | Workflow                                                      |
|--------------------------|-------------------------------------------------------|---------------------------------------------------------------|
| OWASP ZAP                | DAST - baseline scan against live app                 | `ecommerce-zap.yml`                                           |
| CodeQL                   | SAST - static security analysis                       | `ecommerce-sast.yml`                                          |
| Trivy                    | CVE scan on Docker images and dependencies            | `cve-scan.yml`, `deploy.yml`, `ecommerce-docker-validate.yml` |
| Grype (Anchore)          | Vulnerability scan on built image                     | `cve-scan.yml`                                                |
| GitHub Dependency Review | Dependency vulnerability review on PRs                | `cve-scan.yml`                                                |
| Gitleaks                 | Secrets detection in commits                          | `supply-chain.yml`                                            |
| OSSF Scorecard           | Supply chain security scoring                         | `supply-chain.yml`                                            |
| cosign / Sigstore        | Production image signing                              | `deploy.yml`                                                  |
| SBOM (Syft / Anchore)    | Software Bill of Materials generation and attestation | `deploy.yml`                                                  |
| Hadolint                 | Dockerfile linting                                    | `ecommerce-docker-validate.yml`                               |
| Checkov                  | IaC security scanning (Terraform, Helm, K8s)          | `infrastructure-validate.yml`                                 |
| ShellCheck               | Shell script static analysis                          | `infrastructure-lint.yml`                                     |
| ansible-lint             | Ansible playbook linting                              | `infrastructure-lint.yml`                                     |
| Lighthouse CI            | Frontend performance auditing + budget enforcement    | `ecommerce-lighthouse.yml`, `ecommerce-frontend-audit.yml`    |
| axe-core + pa11y         | Accessibility testing on PRs                          | `ecommerce-frontend-audit.yml`                                |
| SonarCloud               | Cloud static analysis + ESLint + coverage             | `ecommerce-coverage.yml`                                      |
| Codecov                  | PHP + JS coverage tracking and reporting              | `ecommerce-coverage.yml`                                      |
| commitlint               | Conventional commit message format enforcement        | `validate-commits.yml`                                        |

---

See also: [Production Infrastructure](../../../../docs/TECHSTACK.md)
