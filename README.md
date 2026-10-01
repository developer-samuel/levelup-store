# 🛒 LevelUp Store

A production-ready **e-commerce monorepo** with two apps:

- **[Ecommerce](apps/ecommerce/README.md)** - Symfony 7.4, Vanilla TypeScript, SCSS
- **[Assistant](apps/assistant/README.md)** - FastAPI, React, TypeScript, Tailwind, RAG (ChromaDB)

Both apps are deployed to Kubernetes (k3s on Oracle Cloud) via ArgoCD.

---

## 📊 Status

### CI/CD
[![Deploy](https://img.shields.io/github/actions/workflow/status/developer-samuel/levelup-store/deploy.yml?logo=docker&label=Deploy)](https://github.com/developer-samuel/levelup-store/actions/workflows/deploy.yml)
[![Terraform](https://img.shields.io/github/actions/workflow/status/developer-samuel/levelup-store/infrastructure-validate.yml?logo=terraform&label=Terraform)](https://github.com/developer-samuel/levelup-store/actions/workflows/infrastructure-validate.yml)

### Security
[![OpenSSF Scorecard](https://img.shields.io/ossf-scorecard/github.com/developer-samuel/levelup-store?logo=github&label=OpenSSF+Scorecard)](https://securityscorecards.dev/viewer/?uri=github.com/developer-samuel/levelup-store)
[![Supply Chain](https://img.shields.io/github/actions/workflow/status/developer-samuel/levelup-store/supply-chain.yml?logo=dependabot&label=Supply+Chain)](https://github.com/developer-samuel/levelup-store/actions/workflows/supply-chain.yml)
[![CVE Scan](https://img.shields.io/github/actions/workflow/status/developer-samuel/levelup-store/cve-scan.yml?logo=trivy&label=CVE+Scan)](https://github.com/developer-samuel/levelup-store/actions/workflows/cve-scan.yml)

### Coverage
[![codecov](https://codecov.io/gh/developer-samuel/levelup-store/branch/main/graph/badge.svg)](https://codecov.io/gh/developer-samuel/levelup-store)

---

## 📦 Repository Structure

```
levelup-store/
├── apps/
│   ├── ecommerce/          # Symfony e-commerce app
│   └── assistant/          # FastAPI AI assistant
├── infrastructure/
│   ├── helm/               # Helm charts (ArgoCD managed)
│   ├── terraform/          # Oracle Cloud provisioning
│   ├── ansible/            # k3s install + server hardening
│   └── kubernetes/         # ArgoCD App of Apps
├── scripts/                # Shared CLI scripts
└── docs/                   # Shared documentation
```

---

## 🛠️ Apps

### 🛒 [Ecommerce](apps/ecommerce/README.md)

Symfony 7.4 e-commerce platform with:
- Product catalog, cart, checkout, orders, Stripe payments
- JWT auth, admin panel, wishlist, reviews, PDF invoices
- Redis caching, RabbitMQ async queue, Mercure SSE, MinIO storage
- Hexagonal Architecture, DDD, CQRS, Event-Driven

### 🤖 [Assistant](apps/assistant/README.md)

FastAPI AI assistant with:
- RAG pipeline (ChromaDB), RabbitMQ job queue, Redis pub/sub
- React + TypeScript frontend

---

## 🚀 Quick Start

```bash
# Install dependencies (generates .env from .env.example)
composer install
pnpm install
```

→ Ecommerce setup: [apps/ecommerce/docs/INSTALL.md](apps/ecommerce/docs/INSTALL.md)  
→ Assistant setup: [apps/assistant/docs/INSTALL.md](apps/assistant/docs/INSTALL.md)

---

## ☸️ Infrastructure & Deploy

```bash
# Generate .env.production and fill in values
composer env:generate

# Deploy (from scratch)
make -C infrastructure bootstrap
```

→ Full deploy guide: [docs/infrastructure/DEPLOYMENT.md](docs/infrastructure/DEPLOYMENT.md)  
→ Infrastructure overview: [docs/infrastructure/OVERVIEW.md](docs/infrastructure/OVERVIEW.md)

---

## 📚 Documentation

### Shared
| Document                                                               | Description             |
|------------------------------------------------------------------------|-------------------------|
| [docs/INSTALL.md](docs/INSTALL.md)                                     | Installation guide      |
| [docs/SETUP.md](docs/SETUP.md)                                         | Environment setup       |
| [docs/TECHSTACK.md](docs/TECHSTACK.md)                                 | Full technology stack   |
| [docs/runtime/DEVOPS.md](docs/runtime/DEVOPS.md)                       | CI/CD pipelines         |
| [docs/runtime/ARCHITECTURE.md](docs/runtime/ARCHITECTURE.md)           | Architecture overview   |
| [docs/infrastructure/DEPLOYMENT.md](docs/infrastructure/DEPLOYMENT.md) | Kubernetes deploy guide |
| [docs/infrastructure/SECRETS.md](docs/infrastructure/SECRETS.md)       | Secrets & env variables |

### Per App
| Ecommerce                                                                     | Assistant                                                      |
|-------------------------------------------------------------------------------|----------------------------------------------------------------|
| Install | [INSTALL.md](apps/ecommerce/docs/INSTALL.md)                        | [INSTALL.md](apps/assistant/docs/INSTALL.md)                   |
| Setup | [SETUP.md](apps/ecommerce/docs/SETUP.md)                              | [SETUP.md](apps/assistant/docs/SETUP.md)                       |
| Tech Stack | [TECHSTACK.md](apps/ecommerce/docs/TECHSTACK.md)                 | [TECHSTACK.md](apps/assistant/docs/TECHSTACK.md)               |
| DevOps | [DEVOPS.md](apps/ecommerce/docs/runtime/DEVOPS.md)                   | [DEVOPS.md](apps/assistant/docs/runtime/DEVOPS.md)             |
| Architecture | [ARCHITECTURE.md](apps/ecommerce/docs/runtime/ARCHITECTURE.md) | [ARCHITECTURE.md](apps/assistant/docs/runtime/ARCHITECTURE.md) |
| Quality | [QUALITY.md](apps/ecommerce/docs/runtime/QUALITY.md)                | [QUALITY.md](apps/assistant/docs/runtime/QUALITY.md)           |

---

## 🌐 Links

- **Website:** [samuel-steiner.com](https://samuel-steiner.com)
- **Links:** [links.samuel-steiner.com](https://links.samuel-steiner.com)
- **GitHub:** [developer-samuel](https://github.com/developer-samuel)
- **LinkedIn:** [samuel-programmer](https://www.linkedin.com/in/samuel-programmer)
- **Instagram:** [samuel.programmer](https://instagram.com/samuel.programmer)

---

## 📝 License

This project is licensed under the **Samuel Šteiner License**.  
Personal and educational use is permitted. **Commercial use is strictly prohibited** without written permission.  
See [LICENSE](LICENSE) for full terms.
