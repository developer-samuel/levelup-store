# 🛒 LevelUp Store

A production-ready **e-commerce platform** built with **Symfony 7.4**, **Vanilla TypeScript**, and **SCSS**.  
Designed for security, scalability, robustness, observability, and maintainability.

- Product catalog with categories, types, subtypes, filters, and sorting
- Cart, checkout, and order management with Stripe and cash-on-delivery
- JWT authentication - login, signup, password reset, email verification
- Admin panel with dashboard analytics, CRUD for products, orders, and users
- Wishlist, reviews with reactions, PDF invoices, Redis caching, and async email queue via RabbitMQ
- Real-time product stock and review rating updates via Mercure (SSE)

---

## 🧱 Architecture

- Hexagonal Architecture (Ports & Adapters)
- Domain-Driven Design (DDD)
- CQRS for read/write separation
- Event-Driven Design

```
src/
├── Adapters/           # Gateways connecting Core to the outside world
│   ├── External/       # Stripe, PDF, JWT, Country API, Redis, RabbitMQ, Elasticsearch, Mercure, MinIO
│   └── Internal/       # Cookie, security, internal cache, order segment
├── Core/               # Heart of the application - pure business logic
│   ├── Application/    # Orchestration: services, handlers, inputs, policies
│   ├── Domain/         # Business rules: entities, value objects, events, specs
│   └── Ports/          # Contracts: gateways, repositories, renderers, notifiers
├── Infrastructure/     # Technical implementations: repositories, listeners, mailers
├── Presentation/       # User-facing layer: controllers, requests, renderers, twig
├── Scheduler/          # Background tasks and async messages
└── Shared/             # Cross-cutting: utils, traits, enums, constants
```

→ Full architecture overview: [docs/runtime/ARCHITECTURE.md](docs/runtime/ARCHITECTURE.md)

---

## 🛠️ Tech Stack

| Layer          | Stack                                                            |
|----------------|------------------------------------------------------------------|
| Backend        | PHP 8.2 / 8.3, Symfony 7.4                                       |
| Auth           | JWT (LexikJWTAuthenticationBundle)                               |
| Frontend       | Vanilla TypeScript, SCSS, Vite                                   |
| Database       | PostgreSQL / MySQL                                               |
| Cache          | Redis (cache, sessions, rate limiting)                           |
| Queue          | RabbitMQ / Doctrine (Symfony Messenger)                          |
| Payments       | Stripe API (card), cash-on-delivery                              |
| Infrastructure | Docker, Nginx, Prometheus, Grafana, Loki, Mercure, MinIO, Sentry |
| Testing        | PHPUnit, Vitest, Playwright                                      |

→ Full tech stack: [docs/TECHSTACK.md](docs/TECHSTACK.md)

---

## 🚀 Quick Start

> 📁 **Sample uploads recommended** - download banners and images from [developer-samuel/levelup-store-uploads](https://github.com/developer-samuel/levelup-store-uploads) and place the `uploads/` folder into `public/`.

```bash
# Quick start
make setup

# or step by step:

# 1. Install dependencies
make install
# or
composer install

pnpm install
# or
npm install

# 2. Setup database
composer db-setup

# 3. Run application
make serve
# or
composer serve

pnpm dev
# or
npm run dev
```

→ Full installation guide: [docs/INSTALL.md](docs/INSTALL.md)  
→ Environment & configuration: [docs/SETUP.md](docs/SETUP.md)  
→ Docker & Makefile commands: [docs/DEVELOPMENT.md](docs/DEVELOPMENT.md)

---

## 📚 Documentation

| Document                                        | Description                 |
|-------------------------------------------------|-----------------------------|
| [INSTALL.md](docs/INSTALL.md)                   | Installation steps          |
| [SETUP.md](docs/SETUP.md)                       | Environment & configuration |
| [REQUIREMENTS.md](docs/REQUIREMENTS.md)         | System requirements         |
| [DEVELOPMENT.md](docs/DEVELOPMENT.md)           | Docker & Makefile reference |
| [MAINTENANCE.md](docs/MAINTENANCE.md)           | Dependency maintenance      |
| [TECHSTACK.md](docs/TECHSTACK.md)               | Full technology stack       |
| [ARCHITECTURE.md](docs/runtime/ARCHITECTURE.md) | Architecture overview       |
| [TESTS.md](docs/runtime/TESTS.md)               | Testing guide               |
| [QUALITY.md](docs/runtime/QUALITY.md)           | Quality tools               |
| [DEVOPS.md](docs/runtime/DEVOPS.md)             | DevOps & deployment         |
| [MODEL.md](docs/runtime/MODEL.md)               | Project modeling & diagrams |

---

## 🌐 Links

- **Website:** [samuel-steiner.com](https://samuel-steiner.com)
- **Links:** [links.samuel-steiner.com](https://links.samuel-steiner.com)
- **GitHub:** [developer-samuel](https://github.com/developer-samuel)
- **LinkedIn:** [samuel.programmer](https://www.linkedin.com/in/samuel-programmer)
- **Instagram:** [samuel.programmer](https://instagram.com/samuel.programmer)

---

## 📝 License

This project is licensed under the **Samuel Šteiner License**.  
Personal and educational use is permitted. **Commercial use is strictly prohibited** without written permission.  
See [LICENSE](LICENSE) for full terms.
