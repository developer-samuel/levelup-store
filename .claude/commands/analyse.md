Run full static analysis across ecommerce and assistant.

**Ecommerce:**
- PHPStan: `cd apps/ecommerce && bin/run php vendor/bin/phpstan analyse --memory-limit=1G`
- PHPMd: `cd apps/ecommerce && bin/run php vendor/bin/phpmd src packages/server xml phpmd.xml`
- Deptrac: `cd apps/ecommerce && bin/run php scripts/tools/deptrac/launcher.php`
- TypeScript: `pnpm --filter ecommerce type-check`
- ESLint: `pnpm --filter ecommerce lint`
- Stylelint: `pnpm --filter ecommerce lint-scss`

**Assistant:**
- mypy: `cd apps/assistant && uv run mypy --explicit-package-bases --cache-dir .cache/mypy app/`
- ruff: `cd apps/assistant && uv run ruff check --cache-dir .cache/ruff app/`
- TypeScript: `pnpm --filter assistant type-check`

Run all tools, report findings grouped by tool, suggest fixes for each error.
