COMPOSE=docker-compose

##
## Kaamelott Gifboard Makefile
##

install: build up env vendor ## Install the project
	@printf "\nProject available at: http://localhost:4242/\n"

env: ## Copy the docker/.env file to local
	${COMPOSE} exec -T php cp docker/.env.dist .env

vendor: ## Install vendors
	${COMPOSE} exec -T php composer install

build: ## Build containers
	${COMPOSE} build

up: ## Start containers
	${COMPOSE} up -d

down: ## Stop containers
	${COMPOSE} down

cs-fixer: ## Launch cs-fixer tool
	${COMPOSE} exec -T php vendor/bin/php-cs-fixer fix

cs-fixer-dry: ## Launch cs-fixer tool (dry run)
	${COMPOSE} exec -T php vendor/bin/php-cs-fixer fix --dry-run -v --ansi

test: ## Launch the PhpUnit suite
	$(COMPOSE) exec -T php vendor/bin/simple-phpunit

##

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/'

.DEFAULT_GOAL := help
