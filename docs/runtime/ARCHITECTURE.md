# ARCHITECTURE

> This document describes the **ecommerce** application architecture.

## 🏗️ Principles

- **Hexagonal Architecture (Ports & Adapters)** - Core never talks to infrastructure directly, only through ports/adapters.
- **DDD (Domain-Driven Design)** - Domain holds pure business rules and logic.
- **CQRS** - Queries only read, commands change state.
- **Event-Driven** - Used only for features that require asynchronous event handling, e.g. emails.

## 🧱 Ecommerce Backend Structure

```
src/
├── Adapters/           # Gateways connecting Core to the outside world
│   ├── External/       # Stripe, PDF, JWT, Turnstile, Country API, cache, RabbitMQ, Elasticsearch, Mercure, MinIO
│   └── Internal/       # Auth (token blacklist), cookie, security, internal cache, order segment
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

- [System Context](../diagrams/graphs/architecture/system-context.mmd)
- [Ecommerce Layers](../diagrams/graphs/architecture/ecommerce-layers.mmd)
- [Local Architecture](../diagrams/graphs/architecture/local-architecture.mmd)
- [Production Architecture](../diagrams/graphs/architecture/production-architecture.mmd)
- [Async Messaging](../diagrams/graphs/architecture/async-messaging.mmd)
- [Deployment Pipeline](../diagrams/graphs/architecture/deployment.mmd)
- [GitOps Flow](../diagrams/graphs/architecture/gitops.mmd)
- [Provisioning](../diagrams/graphs/architecture/provisioning.mmd)

---

See also: [Infrastructure Overview](../infrastructure/OVERVIEW.md) · [Deployment Guide](../infrastructure/DEPLOYMENT.md)
