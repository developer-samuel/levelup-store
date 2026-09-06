## [1.4.1] - 2026-09-06

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