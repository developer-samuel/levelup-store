include make/docker.mk
include make/install.mk
include make/ecommerce.mk
include make/assistant.mk

## Show available commands
help:
	@awk '/^## /{desc=substr($$0,4)} /^[a-zA-Z_-]+:/{if(desc!="") printf "  \033[36m%-45s\033[0m %s\n", $$1, desc; desc=""}' $(MAKEFILE_LIST) \
		| sed 's/:$$//' \
		| sort
