# LevelUp Store Ecommerce

Symfony 7.4 e-commerce platform with JWT auth, Stripe payments, RabbitMQ queue, Redis, Mercure SSE and MinIO storage.

## Prerequisites

- PHP 8.3
- [Composer](https://getcomposer.org)
- [Node.js LTS](https://nodejs.org) + [pnpm](https://pnpm.io)
- Docker & Docker Compose

## Quick start

```bash
# Install dependencies, setup database and start servers
make setup
```

App available at `http://localhost:8000`.  
Vite HMR at `http://localhost:5173`.

Or with Docker:

```bash
make setup-build
```

## Commands

| Command            | Description                                        |
|--------------------|----------------------------------------------------|
| `make setup`       | Install deps, setup DB, clear cache, start servers |
| `make setup-build` | First-time Docker setup (build + DB init + start)  |
| `make install`     | Install PHP + frontend dependencies                |
| `make serve`       | Start PHP dev server + Vite HMR                    |
| `make dev`         | Start Docker stack                                 |
| `make test`        | Run PHPUnit + Vitest                               |
| `make lint`        | Run PHP-CS-Fixer + ESLint + Stylelint              |
| `make cache-clear` | Clear and warmup Symfony cache                     |

## Documentation

| Document                                                     | Description           |
|--------------------------------------------------------------|-----------------------|
| [docs/INSTALL.md](docs/INSTALL.md)                           | Installation guide    |
| [docs/SETUP.md](docs/SETUP.md)                               | Environment setup     |
| [docs/TECHSTACK.md](docs/TECHSTACK.md)                       | Technology stack      |
| [docs/runtime/DEVOPS.md](docs/runtime/DEVOPS.md)             | CI/CD pipelines       |
| [docs/runtime/ARCHITECTURE.md](docs/runtime/ARCHITECTURE.md) | Architecture overview |
| [docs/runtime/QUALITY.md](docs/runtime/QUALITY.md)           | Quality assurance     |
