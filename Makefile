COMPOSE=docker-compose

install: build up env vendor
	@printf "\nProject available at: http://localhost:4242/\n"

env:
	${COMPOSE} exec php cp docker/.env.dist .env

vendor:
	${COMPOSE} exec php composer install

build:
	${COMPOSE} build

up:
	${COMPOSE} up -d

down:
	${COMPOSE} down

cs-fixer:
	${COMPOSE} exec php vendor/bin/php-cs-fixer fix

test:
	$(COMPOSE) exec php vendor/bin/simple-phpunit
