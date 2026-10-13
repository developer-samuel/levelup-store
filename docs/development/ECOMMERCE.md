# Ecommerce (Symfony/PHP)

## Dev

```bash
# Start local dev servers (PHP + frontend)
make serve

# Full local setup: install + permissions + database + serve
make setup

# List all registered routes
make routes
```

## Maintenance

```bash
# Clear and warmup Symfony cache (flushes Redis if available)
make cache-clear
```

## Production image

```bash
# Build ecommerce production image locally (smoke test before push to main)
make build-prod

# Verify production image has bin/console (run after build-prod)
make test-prod
```
