include make/docker.mk
include make/ecommerce.mk
include make/assistant.mk

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sed 's/^.*\.mk://' \
		| sort \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-45s\033[0m %s\n", $$1, $$2}'
