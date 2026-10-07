# Ecommerce (Symfony/PHP)

## Dev

```bash
# Start local dev servers (PHP + frontend)
make serve
# or
cd apps/ecommerce && composer serve &
pnpm dev

# Full local setup: install + permissions + database + serve
make setup

# List all registered routes
make routes
# or
cd apps/ecommerce && php bin/console debug:router
```

## Maintenance

```bash
# Clear and warmup Symfony cache (flushes Redis if available)
make cache-clear
# or
cd apps/ecommerce && composer cache:clear && composer cache:warmup

```

## Production image

```bash
# Build ecommerce production image locally (smoke test before push to main)
make build-prod
# or
docker build -f docker/Dockerfile.prod --target ecommerce -t levelup-store-ecommerce:prod-test .

# Verify production image has bin/console (run after build-prod)
make test-prod
# or
docker run --rm --entrypoint php levelup-store-ecommerce:prod-test -l /var/www/bin/console
```
