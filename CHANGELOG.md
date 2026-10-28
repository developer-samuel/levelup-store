## [2.0.0] - 2026-10-28

### 🏗️ Architecture

- **Reorganized the repository as a monorepo** containing two independently deployable applications: `apps/ecommerce` (Symfony) and `apps/assistant` (FastAPI).

### 🤖 AI Assistant

- **Added a FastAPI AI assistant with a React, TypeScript and Tailwind CSS chat interface integrated into the ecommerce experience.**
- Implemented product-knowledge retrieval-augmented generation (RAG) with ChromaDB and scheduled incremental ingestion every 15 minutes.
- Added configurable model and embedding integrations for local Ollama and external API providers, including Gemini.
- Added RabbitMQ-backed request queuing for Ollama mode; API-provider requests stream directly, with Redis pub/sub used for live SSE delivery and stream recovery.
- Added Redis-backed server-side conversation history with separate one-day guest and 30-day user-associated TTLs, plus ecommerce context from account orders, cart, reviews and wishlist.
- Added browser microphone transcription into the chat input, browser text-to-speech playback for assistant replies, an emoji picker and a new-chat/reset action.
- Applied a shared per-IP chat limit across Ollama and API-provider modes, with Sentry error monitoring and OpenTelemetry tracing.
- Added shared API-key validation and dedicated CORS configuration for assistant endpoints.

### 🛍️ Ecommerce

- Added reusable presets for shared ecommerce code and configuration: PHP assertions and utilities in `presets/server/kit`, test helpers in `presets/server/test`, and Vite/Vitest configuration in `presets/vite`.
- Improved product discovery with configurable search synonyms, asynchronous paginated search and lazy loading.
- Reworked the cart sidebar, zoom modal and overlay styles; sticky header now hides the user dropdown on scroll and handles overflow correctly across all breakpoints.
- Added consistent custom scrollbar styles shared between the ecommerce storefront and the assistant chat interface.
- Aligned the assistant chat UI visually with ecommerce modal and overlay conventions so both applications share a cohesive look and feel.
- Scoped maximum-price filtering to the currently selected category.
- Fixed product block layout to display ratings and reviews counts as separate values.
- Added interactive OpenAPI documentation with Nelmio Swagger UI at `/api/dev/docs` in local, development and test environments.

### ☁️ Infrastructure & DevOps

- **Added an infrastructure-as-code deployment stack for Oracle Cloud Infrastructure (OCI)** using Terraform, Ansible and k3s, with Helm releases managed through Argo CD.
- Added Helm-managed application and platform services for PostgreSQL, Redis, RabbitMQ, Elasticsearch, MinIO, Mercure and Ollama, with Argo CD App-of-Apps synchronization.
- Added a shared production Dockerfile at `infrastructure/docker/Dockerfile.prod` for building the ecommerce and assistant images.
- Added production and staging application manifests. Staging is disabled by default; ecommerce staging is configured to reuse services in the production namespace.
- Automated deployments from pushes to `main` and `staging`: GitHub Actions detects changed apps, builds multi-platform images, publishes them to GHCR and updates the matching Helm values for Argo CD to sync.
- Added a manual Jenkins pipeline to build and deploy selected apps or roll back to a chosen image tag in staging or production, with an approval gate for production.
- Added Atlantis Terraform plans for infrastructure pull requests and automated validation for Terraform, Helm, Kubernetes, Ansible and container configuration.
- Added autoscaling configuration with HPA and RabbitMQ-driven KEDA, and VPA resource recommendations for both applications.
- Added runtime detection and policy enforcement with Falco and Kyverno, plus Traefik rate limiting, assistant circuit breaking, security headers and canonical-domain redirects.
- Restricted Jenkins and Argo CD access to the Tailscale network and automated TLS provisioning with cert-manager and Let's Encrypt.
- Expanded observability with Prometheus, Grafana, Loki, Tempo, OpenTelemetry, AlertManager and Blackbox Exporter; added Reloader for configuration-triggered restarts.
- Added Velero Kubernetes resource and volume backups to OCI Object Storage, PostgreSQL backup uploads through `rclone`, and automated k3s upgrades through the System Upgrade Controller.
- Added optional Vault and External Secrets Operator support; current application and service secrets can also be managed through Argo CD parameters and Kubernetes secrets.
- Added Coraza WAF as a Traefik middleware plugin with the OWASP Core Rule Set; applied to ecommerce and assistant ingress chains to block SQLi, XSS, path traversal and malicious bots at the ingress level before requests reach the application.
- Documented and diagrammed the single-node ARM deployment and its infrastructure workflows.

### 🔐 Security & Supply Chain

- Strengthened ecommerce authentication with token blacklisting on logout and refresh-token rotation.
- Added release image security and provenance steps: generate and attest SPDX SBOMs, scan images with Trivy, and sign images with Cosign.
- Expanded automated checks with CodeQL, Gitleaks, OSSF Scorecard, Trivy and Grype vulnerability scans, dependency review, OWASP ZAP, Checkov infrastructure scanning, and a weekly testssl.sh TLS audit against the staging environment.
- Added comprehensive HTTP security response headers across ecommerce and assistant; ZAP and Checkov findings block CI (with per-project ignore rules for known false positives); Trivy and Grype run in report-only mode with findings uploaded to GitHub Security.
- Restricted workflow token permissions and added timeouts to security-sensitive and long-running CI jobs.
- Added a security assurance case, `GOVERNANCE.md` and `.github/CODE_OF_CONDUCT.md`, and clarified contribution and testing expectations.
- Extended supply chain checks (Gitleaks secrets detection and OSSF Scorecard) to the `staging` branch in addition to `main`.
- Expanded Kubernetes runtime monitoring, admission policy enforcement and infrastructure access restrictions.

### 🧪 Testing, Quality & CI/CD

- Expanded GitHub Actions into dedicated ecommerce, assistant, infrastructure, security, quality and deployment workflows with app-specific path filters and branch triggers.
- Added automated smoke tests, OpenAPI linting with Redocly, Helm diff checks, infrastructure linting and Terraform/Helm/Kubernetes/Ansible validation.
- Added assistant pytest coverage reporting, ecommerce PHP and frontend coverage, and SonarCloud analysis.
- Added k6 load tests with p99 latency thresholds, Lighthouse performance budgets, bundle-size reports and accessibility audits.
- Added scheduled Playwright end-to-end testing and recurring frontend, SAST and DAST audits.
- Renamed GitHub Actions composite actions to verb-noun kebab-case convention for consistency.

### 🛠️ Developer Experience & Automation

- Split the root Makefile into four focused Docker, install, ecommerce and assistant modules, with shared commands for setup, permissions, structure generation and development.
- Reorganized infrastructure commands into 38 domain-specific Make modules, including generated Argo CD lifecycle, status and debugging commands for platform services and workloads.
- Added infrastructure bootstrap and dependency-management commands, cluster diagnostics, secret operations, port forwarding and k6 load-test targets.
- Added reusable GitHub Actions workflows and composite actions to share build, environment setup and service bootstrapping across application pipelines.
- Added Renovate configuration for automated dependency update pull requests across supported ecosystems.

### 📚 Documentation

- Reorganized and expanded shared and app-specific documentation with separate setup, development, architecture, quality and operations guides for the platform, ecommerce and assistant.
- Added infrastructure guides for prerequisites, server setup, secrets, full bootstrap and deployment, validation, troubleshooting, port forwarding and command reference.
- Added service-specific operational guides for Argo CD-managed workloads, databases, cache, messaging, storage, realtime, observability, scaling, backups and platform services.
- Added and updated architecture, provisioning, deployment, GitOps, application integration and CI/CD diagrams.


## [1.4.1] - 2026-09-06

### 🐛 Fixes
- Removed node_modules chown from Docker permissions script to prevent pnpm install failures on host
- Added pnpm via corepack to PHP container
- Added frontend build step to Docker setup flow
- Added chmod 644 for pnpm package files in permissions scripts

## [1.4.0] - 2026-09-06

### 🚀 Features
- Mercure real-time integration with live order status, product stock and review rating updates
- Pluggable object storage via MinIO S3
- Cloudflare Turnstile CAPTCHA integration
- Audit logging moved into event listeners for cleaner separation of concerns
- SonarQube static analysis pipeline
 
### 🏗️ Infrastructure
- Reorganized Docker Compose into base/dev stacks with per-service includes
- Added RabbitMQ, Elasticsearch, MinIO S3, Mercure, Mailpit, Dozzle services
- Replaced Adminer with pgAdmin with auto-provisioned PostgreSQL connection
- Added AlertManager + Prometheus alerting and Loki + Alloy observability stack
- Switched Vite container from npm to pnpm
- Health checks hardened across all major services
- MinIO and Mercure services added to PHPUnit and E2E CI pipelines - Elasticsearch index seeded before PHPUnit runs to prevent 500 errors on fresh environments

### ♻️ Architecture
- Reorganized `src/` into clean hexagonal layers (Adapters, Core, Infrastructure, Presentation, Shared)
- Removed redundant handler contracts and flattened directory structure 
- Use 422 instead of 400 for validation errors

### 🛠️ Tooling
- Added custom Rector rule `FinalizeNonAbstractClassRector` for automatic class finalization
- Fixed PHP CS Fixer CRLF line endings in shell scripts
- Added `symplify/rule-doc-generator-contracts` dev dependency
- Flattened ESLint config into `tools/eslint/`
- Synchronized composer and frontend script documentation
 
### 🐛 Fixes
- Fixed product filter and sort bugs
- Fixed ProfileRequest address field mapping (billing/shipping prefix)
- Implemented `ProductRecordContract` on all product record classes
- Stabilised Playwright E2E suite

## [1.3.0] - 2026-08-17

### 🚀 Features                                                                                                                                                                  
- Abandoned cart reminder email via scheduler
- Auto-login user after email verification
- RabbitMQ messaging infrastructure with AMQP transport

### 🏗️  Infrastructure
- RabbitMQ service added to CI test pipeline (PHPUnit + E2E)
- Composite action for RabbitMQ health-check setup

### 🐛 Fixes
- Prevent order creation when notification fails
- Resolve Deptrac architecture violation in CartReminderTask
- Comment out transport-specific messenger options to support Doctrine fallback

## [1.2.0] - 2026-08-16

### 🚀 Features
- Zoom image modal on product detail gallery with full-screen image preview

### 🏗️ Architecture
- Shared base modal layer (`shared/elements/modal`) with generic show/hide, Escape key and backdrop click handling
- Reviews modal and zoom modal unified under common base
- Zoom modal template moved to `components/modals/zoom` and rendered at body level via `app/init`

## [1.1.1] - 2026-08-16

### 🐛 Fixes 
- Product detail gallery height chain on mobile and tablet (responsive breakpoints)
- Product detail swipe now correctly ignores vertical scroll via gesture direction detection
- Search input no longer strips spaces while typing

## [1.1.0] - 2026-08-14

### 🚀 Features
- Live cart updates on stock conflicts with automatic cleanup scheduler
- Cart stock validation before payment and order creation
- Recommended products sync scheduler for out-of-stock variants
- Expired token and country sync scheduled tasks
- Soft deletes for users with full profile destroy flow
- Audit logging with request metadata extraction
- Smart header hide/show on mobile scroll
- Health-check endpoint
- Privacy policy, updated Terms & Conditions
- Demo disclaimer in footer
- Cart cleanup scheduler with updated_at tracking on mutations

### 🏗️ Architecture
- Domain exceptions unified under `Shared\Exception` namespace
- `DomainException` enforced for business validations
- Rate limiter rewritten to IP-based cache strategy with configurable TTL per endpoint and Retry-After header
- Scheduler messages and tasks renamed for clarity

### ♻️ Refactoring
- Form handlers simplified, per-type notyf duration, removed scroll on success
- SEO metadata, title block and accessibility improvements
- ORM mapping moved to traits with enforced non-nullable expires

### 📦 Infrastructure
- GitHub Actions CI/CD: `main`, `pull-request`, `release` pipelines
- Composite actions for PHP, Node, Postgres, Redis, env
- PHPUnit matrix across PHP 8.2 and 8.3

### 🧪 Tests
- PHPUnit, Vitest extended
- Playwright E2E

### 🐛 Fixes
- Product filter slug matching for hyphenated names
- Product detail responsive layout and anchor links on mobile
- Sticky elements, filter, and navigation on mobile
- Form error scroll for custom scroll containers
- Footer offset, background, and copyright year
- Out-of-stock removal from cart on order create
- Empty navigation links remove

## [1.0.0] - 2026-07-12

### 🚀 Features

#### E-Commerce Core
- Product catalog with taxonomy (Categories → Types → Subtypes), filters, sorting, pagination
- Product detail with variants, stock, images, descriptions, discounts
- Cart management (add, update, remove) with real-time stock validation
- Checkout flow - personal, billing, shipping details capture
- Payments via Stripe (card) and cash-on-delivery
- Downloadable PDF invoices with unique codes (wkhtmltopdf)
- Order history and status tracking
- Wishlist (add/remove products)
- Reviews with like/dislike reactions
- Full-text product search

#### Admin Panel
- Dashboard analytics - orders per month, paid/unpaid ratio, user growth (ApexCharts)
- CRUD for brands, banners, products, variants, users, orders
- Order status management

#### Authentication & Security
- JWT access + refresh token pattern (HTTP-only cookies)
- Email verification and password reset flows
- Role-based access control (guest, user, admin)
- Rate limiting on auth endpoints

### 🏗️  Architecture
- Hexagonal Architecture (Ports & Adapters)
- Domain-Driven Design (DDD) with rich domain model
- CQRS - explicit command/query separation
- Event-Driven - domain events for email notifications
- Specification Pattern for product variant availability

### 📦 Infrastructure
- Docker Compose with PostgreSQL 17, Redis 7, Nginx, Adminer
- Symfony Scheduler - stock and EAN sync every 15 minutes
- Redis for caching and session storage with TTL-based invalidation
- Prometheus + Grafana for metrics, Sentry for error tracking

### 🔌 Integrations
- Stripe API - card payments and refunds
- Symfony Mailer - emails (verification, reset, order confirmation...)
- apicountries.com - country data with Redis cache
- wkhtmltopdf - PDF invoice generation

### 🧪 Tests      
- Backend: PHPUnit (Unit / Integration / Feature), PHPStan (lvl. 10), Deptrac, PHPMD
- Frontend: Vitest (Unit / Integration / Functional), Playwright E2E (auth flows)
- All implemented tests pass at 100%