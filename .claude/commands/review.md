Review the architecture and layer boundaries of a given class or file.

The user will provide a file path or class name. Perform a full architectural review.

## Steps

1. Read the file fully
2. Identify the layer (Domain, Application, Infrastructure, Presentation, Adapters, Shared, Ports, Scheduler)
3. Check all imports - flag any cross-layer violations
4. Check segment placement matches the domain entity
5. Run Deptrac to confirm:
   ```bash
   cd apps/ecommerce && bin/run php scripts/tools/deptrac/launcher.php
   ```
6. Check PHPStan for type errors:
   ```bash
   cd apps/ecommerce && bin/run php vendor/bin/phpstan analyse {file} --memory-limit=1G
   ```

## Report format

```
File: {path}
Layer: {layer}
Segment: {segment}

✅ Layer boundary: correct / ❌ Violation: {description}
✅ Segment: correct / ❌ Wrong segment: should be in {correct segment}
✅ PHPStan: clean / ❌ Errors: {list}
✅ Deptrac: clean / ❌ Violations: {list}

Suggestions:
- {actionable improvement}
```

## Architecture reference

```
src/
├── Adapters/
│   ├── External/       # Third-party integrations: Api, Cache, Jwt, MessageBroker, Payment/Stripe, Pdf, Realtime, Search, Storage, Turnstile
│   └── Internal/       # Internal cross-cutting: Auth, Cache, Cookie, Order, Security
├── Core/
│   ├── Application/    # Orchestration: Abstract, Admin, Auth, Cache, Cookie, Home, Search, Security, Segment, Shared
│   ├── Domain/         # Business rules: Admin, Auth, Cache, Cookie, Home, Search, Segment, Shared
│   └── Ports/          # Contracts only: Admin, Auth, Cache, Gateways, Home, Payment, Search, Security, Segment, Shared, Web
├── Infrastructure/     # Technical implementations: Abstract, Auth, Console, Payment, Security, Segment, Shared
├── Presentation/
│   ├── Abstract/       # Base controllers, requests
│   ├── Api/            # JSON endpoints: Admin, Assistant, Auth, Cookie, Dev, Search
│   ├── Web/            # Twig pages: Abstract, Admin, Auth, Home, Search, Segment
│   └── Shared/         # Shared presentation: Manager, Processor, Responder, Traits, Twig, Utils, Validation
├── Scheduler/          # Background tasks: Message (Cart, Country, Product, Token), Task (Cart, Country, Product, Token)
└── Shared/             # App-wide: Constants, Enum, Renderer, Responder, Traits, Utils
```

## Layer rules

- `Core/Domain` - entities, value objects, events, specs. No framework dependencies.
- `Core/Application` - services, handlers, inputs, resources. Orchestrates domain. No infrastructure.
- `Core/Ports` - interfaces/contracts only. No implementations.
- `Infrastructure` - implements Ports. Repositories, listeners, mailers, event subscribers.
- `Presentation/Api` - JSON controllers. No business logic.
- `Presentation/Web` - Twig controllers. No business logic.
- `Adapters/External` - third-party integrations implementing Port contracts.
- `Adapters/Internal` - internal cross-cutting concerns implementing Port contracts.
- `Scheduler` - async messages and scheduled tasks. No business logic.
- `Shared` - utilities, traits, enums, constants. No layer-specific dependencies.
