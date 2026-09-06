# ARCHITECTURE

## 🏗️ Principles

- **Hexagonal Architecture (Ports & Adapters)** - Core never talks to infrastructure directly, only through ports/adapters.
- **DDD (Domain-Driven Design)** - Domain holds pure business rules and logic.
- **CQRS** - Queries only read, commands change state.
- **Event-Driven** - Used only for features that require asynchronous event handling, e.g. emails.

## 🧱 Project Structure

```
src/
├── Adapters/           # Gateways connecting Core to the outside world
│   ├── External/       # Stripe, PDF, JWT, Country API, cache, RabbitMQ, Elasticsearch, Mercure, MinIO
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

---

## 📊 Diagrams

- [Architecture Layers](../diagrams/graphs/architecture/layers.mmd)
- [Deployment](../diagrams/graphs/architecture/deployment.mmd)
