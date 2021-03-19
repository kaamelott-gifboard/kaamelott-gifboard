COMPOSE=docker-compose
DOCKER_FLAGS ?= --rm
COMPOSER_HOME ?= ${HOME}/.config/composer
COMPOSER_CACHE_DIR ?= ${HOME}/.cache/composer
COMPOSE_EXEC_FLAGS =

DOCKER_RUN_COMPOSER = docker run ${DOCKER_FLAGS} \
	--env COMPOSER_HOME \
	--env COMPOSER_CACHE_DIR \
	--volume ${COMPOSER_HOME}:${COMPOSER_HOME} \
	--volume ${COMPOSER_CACHE_DIR}:${COMPOSER_CACHE_DIR} \
	--volume ${PWD}:/app \
	--user $(shell id -u):$(shell id -g) \
	-w /app \
	 composer

INTERACTIVE := $(shell [ -t 0 ] && echo 1 || echo 0)
ifeq ($(INTERACTIVE), 0)
	COMPOSE_EXEC_FLAGS += -T
endif

EXEC_APP = ${COMPOSE} exec ${COMPOSE_EXEC_FLAGS} app

##  ------
##@ Docker
##  ------

build: ## Build containers
	${COMPOSE} build
.PHONY: build

start: vendor .env ## Start containers
	${COMPOSE} up -d
.PHONY: start

stop: ## Stop containers
	${COMPOSE} stop
.PHONY: stop

down: ## Remove containers
	${COMPOSE} down
.PHONY: down

##  ------
##@ Composer
##  ------

.PHONY: composer
composer: .env ## Run composer commands. Example : `make composer c="require --dev some/package"`
	${DOCKER_RUN_COMPOSER} $c

.env: ## Copy the docker/.env file to local
	@if [ ! -f .env ]; then\
		cp .env.dist .env;\
	fi

vendor: composer.lock
	${DOCKER_RUN_COMPOSER} install && touch vendor

composer.lock: composer.json
	${DOCKER_RUN_COMPOSER} update --lock --no-scripts --no-interaction

install: vendor ## Install the project
.PHONY: install

##  --
##@ QA
##  --

cs-fixer: start ## Launch cs-fixer tool
	${EXEC_APP} vendor/bin/php-cs-fixer fix
.PHONY: cs-fixer

cs-fixer-dry: start ## Launch cs-fixer tool (dry run)
	${EXEC_APP} vendor/bin/php-cs-fixer fix --dry-run -v --ansi
.PHONY: cs-fixer-dry

test: start ## Launch the PhpUnit suite
	${EXEC_APP} bin/phpunit
.PHONY: test

phpstan: start ## Launch the Phpstan tool
	${EXEC_APP} vendor/bin/phpstan analyse -c phpstan.neon
.PHONY: phpstan

##  ----
##@ Misc
##  ----

.DEFAULT_GOAL := help
.PHONY: help
# See https://www.thapaliya.com/en/writings/well-documented-makefiles/
help: ## Display this help
	@awk 'BEGIN {FS = ":.* ##"; printf "\n\033[32;1m  Kaamelott Gifboard\n  ------------------\033[0m\n"} /^[%a-zA-Z_-]+:.* ## / { printf "  \033[33m%-25s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' ${MAKEFILE_LIST}

##@
