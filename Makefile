.PHONY: help init run debug up down restart clean install build composer-install composer-update \
	yarn-install yarn-build node-watch php-shell node-shell console migrate diff fixtures \
	cache-clear cache-warmup test stan ecs ecs-fix logs logs-php logs-nginx logs-db docker-compose-check

# Docker/ENV configuration
DOCKER_COMPOSE ?= docker compose
DOCKER_USER ?= "$(shell id -u):$(shell id -g)"
ENV ?= "dev"
DC = ENV=$(ENV) DOCKER_USER=$(DOCKER_USER) $(DOCKER_COMPOSE)

help: ## Show this help
	@echo "Available targets:"
	@grep -E '^[a-zA-Z0-9_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS=":.*?## "}; {printf "\033[36m%-22s\033[0m %s\n", $$1, $$2}' | sort

init: ## Initialize project: check docker, copy override, install deps, run install, up
	@make -s docker-compose-check
	@if [ ! -e compose.override.yml ]; then \
		cp compose.override.dist.yml compose.override.yml; \
	fi
	@$(DC) run --rm php composer install --no-interaction --no-scripts
	@$(DC) run --rm nodejs yarn --version >/dev/null 2>&1 || true
	@make -s install
	@$(DC) up -d

run: ## Alias to `up`
	@make -s up

debug: ## Up with debug compose overlay
	@$(DC) -f compose.yml -f compose.override.yml -f compose.debug.yml up -d

up: ## Start containers in background
	@$(DC) up -d

down: ## Stop containers
	@$(DC) down

restart: ## Restart containers (down + up)
	@$(DC) down
	@$(DC) up -d

install: ## Run Sylius installer (non-interactive)
	@$(DC) run --rm php bin/console sylius:install -s default -n

clean: ## Stop and remove containers, networks and volumes for this project
	@$(DC) down -v

build: ## Full build: composer install, yarn install, yarn build
	@$(DC) run --rm php composer install --no-interaction
	@$(DC) run --rm nodejs yarn install --frozen-lockfile || $(DC) run --rm nodejs yarn install
	@$(DC) run --rm nodejs yarn build

composer-install: ## Composer install (PHP container)
	@$(DC) run --rm php composer install --no-interaction

composer-update: ## Composer update (PHP container)
	@$(DC) run --rm php composer update --no-interaction

yarn-install: ## Yarn install (Node container)
	@$(DC) run --rm nodejs yarn install --frozen-lockfile || $(DC) run --rm nodejs yarn install

yarn-build: ## Build frontend assets via Yarn
	@$(DC) run --rm nodejs yarn build

php-shell: ## Open shell in PHP container
	@$(DC) exec php sh

node-shell: ## Open shell in Node container
	@$(DC) run --rm -i nodejs sh

node-watch: ## Run frontend watcher (npm run watch)
	@$(DC) run --rm -i nodejs "npm run watch"

console: ## Run bin/console command. Usage: make console c="cache:clear"
	@if [ -z "$(c)" ]; then echo "Usage: make console c=\"cache:clear\""; exit 2; fi
	@$(DC) run --rm php bin/console $(c)

migrate: ## Run doctrine migrations (non-interactive)
	@$(DC) run --rm php bin/console doctrine:migrations:migrate -n

diff: ## Generate a new migration from current mapping (dev only)
	@$(DC) run --rm php bin/console doctrine:migrations:diff

fixtures: ## Load Sylius fixtures (DANGEROUS: wipes data) set CONFIRM=1 to proceed
	@if [ "$(CONFIRM)" != "1" ]; then echo "Refusing to load fixtures. Set CONFIRM=1 to proceed."; exit 3; fi
	@$(DC) run --rm php bin/console sylius:fixtures:load -n

cache-clear: ## Clear Symfony cache
	@$(DC) run --rm php bin/console cache:clear

cache-warmup: ## Warm up Symfony cache
	@$(DC) run --rm php bin/console cache:warmup

test: ## Run PHPUnit test suite
	@$(DC) run --rm php ./vendor/bin/phpunit

stan: ## Run PHPStan static analysis
	@$(DC) run --rm php ./vendor/bin/phpstan analyse -c phpstan.dist.neon || true

ecs: ## Run EasyCodingStandard check
	@$(DC) run --rm php ./vendor/bin/ecs check --config ecs.php

ecs-fix: ## Run EasyCodingStandard with fixer
	@$(DC) run --rm php ./vendor/bin/ecs check --config ecs.php --fix

logs: ## Tail all main services logs
	@$(DC) logs -f --tail=200

logs-php: ## Tail PHP container logs
	@$(DC) logs -f --tail=200 php

logs-nginx: ## Tail Nginx container logs
	@$(DC) logs -f --tail=200 nginx

logs-db: ## Tail Database container logs
	@$(DC) logs -f --tail=200 database

docker-compose-check: ## Verify docker compose availability and show version
	@$(DOCKER_COMPOSE) version >/dev/null 2>&1 || (echo "Please install docker compose binary or set DOCKER_COMPOSE=\"docker-compose\" for legacy binary" && exit 1)
	@echo "You are using \"$(DOCKER_COMPOSE)\" binary"
	@echo "Current version is \"$$($(DOCKER_COMPOSE) version)\""
