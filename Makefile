include make/docker.mk
include make/install.mk
include make/ecommerce.mk
include make/assistant.mk

## Show available commands
help:
	@awk '/^## /{desc=substr($$0,4)} /^[a-zA-Z_-]+:/{if(desc!="") printf "  \033[36m%-45s\033[0m %s\n", $$1, desc; desc=""}' $(MAKEFILE_LIST) \
		| sed 's/:$$//' \
		| sort

## Set correct file permissions - fixes root-owned files (WSL2)
fix-permissions:
	@bash scripts/set-permissions/entrypoints/run.sh
	@if command -v docker > /dev/null 2>&1 && docker info > /dev/null 2>&1 && docker ps --filter "name=levelup_store_ecommerce_app" --filter "status=running" -q 2>/dev/null | grep -q .; then \
		echo "🔧 Fixing var/ permissions inside app container..."; \
		docker exec levelup_store_ecommerce_app chown -R www-data:www-data /var/www/apps/ecommerce/var/; \
	fi
	$(MAKE) cache-clear

## Generate UML diagrams from source code
generate-uml:
	@bash scripts/generate-uml/entrypoints/run.sh

## Generate project structure to .structure/tree.txt (uses git - respects .gitignore)
generate-structure:
	@mkdir -p .structure
	@git ls-files | tree --fromfile > .structure/tree.txt
	@echo "✅ Project structure saved to .structure/tree.txt"

## Generate project directory structure to .structure/dirs.txt (directories only)
generate-structure-dirs:
	@mkdir -p .structure
	@git ls-files | tree --fromfile -d > .structure/dirs.txt
	@echo "✅ Project directory structure saved to .structure/dirs.txt"
