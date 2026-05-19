.DEFAULT_GOAL := help

COMPOSE  := docker compose
EXEC     := $(COMPOSE) exec app
EXEC_T   := $(COMPOSE) exec -T app
APP_URL  := http://localhost:8069

# ---------------------------------------------------------------------------
# Help
# ---------------------------------------------------------------------------

.PHONY: help
help: ## Show available commands
	@echo "apigoalgus — local Docker & deploy helpers"
	@echo ""
	@echo "Usage: make <target>"
	@echo ""
	@grep -E '^[a-zA-Z0-9_.-]+:.*?## ' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-22s\033[0m %s\n", $$1, $$2}'

# ---------------------------------------------------------------------------
# First-time setup
# ---------------------------------------------------------------------------

.PHONY: setup
setup: env up install key migrate ## First-time setup: .env, Docker, composer, key, migrate
	@echo ""
	@echo "Setup complete. Open $(APP_URL)"

.PHONY: env
env: ## Copy .env.example to .env if missing
	@if [ ! -f .env ]; then cp .env.example .env && echo "Created .env from .env.example"; else echo ".env already exists"; fi

# ---------------------------------------------------------------------------
# Docker
# ---------------------------------------------------------------------------

.PHONY: up
up: ## Start containers (detached)
	$(COMPOSE) up -d

.PHONY: down
down: ## Stop containers
	$(COMPOSE) down

.PHONY: build
build: ## Build and start containers
	$(COMPOSE) up -d --build

.PHONY: restart
restart: down up ## Restart containers

.PHONY: logs
logs: ## Follow app container logs
	$(COMPOSE) logs -f app

.PHONY: logs-nginx
logs-nginx: ## Follow nginx logs
	$(COMPOSE) logs -f nginx

.PHONY: logs-scheduler
logs-scheduler: ## Follow scheduler container logs
	$(COMPOSE) logs -f scheduler

.PHONY: ps
ps: ## Show container status
	$(COMPOSE) ps

.PHONY: shell
shell: ## Open bash shell in app container
	$(COMPOSE) exec app bash

# ---------------------------------------------------------------------------
# Composer
# ---------------------------------------------------------------------------

.PHONY: install
install: ## composer install
	$(EXEC_T) composer install

.PHONY: composer-install
composer-install: install ## Alias for install

.PHONY: composer-update
composer-update: ## composer update
	$(EXEC_T) composer update

.PHONY: composer-require
composer-require: ## Add package: make composer-require pkg=vendor/package
	@test -n "$(pkg)" || (echo "Usage: make composer-require pkg=vendor/package" && exit 1)
	$(EXEC_T) composer require $(pkg)

.PHONY: composer
composer: ## Run composer: make composer args="dump-autoload"
	@test -n "$(args)" || (echo "Usage: make composer args=\"install\"" && exit 1)
	$(EXEC_T) composer $(args)

# ---------------------------------------------------------------------------
# Artisan (named shortcuts)
# ---------------------------------------------------------------------------

.PHONY: key
key: ## php artisan key:generate
	$(EXEC_T) php artisan key:generate

.PHONY: migrate
migrate: ## php artisan migrate
	$(EXEC_T) php artisan migrate

.PHONY: migrate-fresh
migrate-fresh: ## php artisan migrate:fresh
	$(EXEC_T) php artisan migrate:fresh

.PHONY: migrate-rollback
migrate-rollback: ## php artisan migrate:rollback
	$(EXEC_T) php artisan migrate:rollback

.PHONY: seed
seed: ## php artisan db:seed
	$(EXEC_T) php artisan db:seed

.PHONY: fresh
fresh: ## migrate:fresh --seed
	$(EXEC_T) php artisan migrate:fresh --seed

.PHONY: tinker
tinker: ## php artisan tinker
	$(EXEC) php artisan tinker

.PHONY: routes
routes: ## php artisan route:list
	$(EXEC_T) php artisan route:list

.PHONY: cache-clear
cache-clear: ## Clear config, route, view, and app cache
	$(EXEC_T) php artisan config:clear
	$(EXEC_T) php artisan route:clear
	$(EXEC_T) php artisan view:clear
	$(EXEC_T) php artisan cache:clear

.PHONY: optimize
optimize: ## Cache config, routes, and views (local)
	$(EXEC_T) php artisan config:cache
	$(EXEC_T) php artisan route:cache
	$(EXEC_T) php artisan view:cache

.PHONY: test
test: ## Run PHPUnit tests
	$(EXEC_T) php artisan test

.PHONY: pint
pint: ## Run Laravel Pint
	$(EXEC_T) ./vendor/bin/pint

.PHONY: artisan
artisan: ## Run any artisan command: make artisan cmd="migrate:status"
	@test -n "$(cmd)" || (echo "Usage: make artisan cmd=\"migrate:status\"" && exit 1)
	$(EXEC_T) php artisan $(cmd)

# ---------------------------------------------------------------------------
# Deploy (Superhosting)
# ---------------------------------------------------------------------------

.PHONY: deploy-setup
deploy-setup: ## Copy deploy/deploy.env.example to deploy/deploy.env
	@if [ ! -f deploy/deploy.env ]; then cp deploy/deploy.env.example deploy/deploy.env && echo "Created deploy/deploy.env"; else echo "deploy/deploy.env already exists"; fi

.PHONY: deploy-check
deploy-check: ## Verify deploy prerequisites (branch, clean tree, deploy.env)
	@./deploy/deploy.sh --check

.PHONY: deploy
deploy: ## Deploy to production (live branch, clean tree)
	@./deploy/deploy.sh

.PHONY: deploy-dry-run
deploy-dry-run: ## Preview rsync changes without deploying
	@./deploy/deploy.sh --dry-run

.PHONY: deploy-merge
deploy-merge: ## Merge main into live (does not deploy)
	@git checkout live
	@git merge main
	@echo "Merged main into live. Run 'make deploy' from the live branch when ready."

.PHONY: deploy-barbergarage-check
deploy-barbergarage-check: ## Verify barbergarage deploy prerequisites
	@./deploy/deploy-barbergarage.sh --check

.PHONY: deploy-barbergarage
deploy-barbergarage: ## Deploy barbergarage static site (live branch, clean tree)
	@./deploy/deploy-barbergarage.sh

.PHONY: deploy-barbergarage-dry-run
deploy-barbergarage-dry-run: ## Preview barbergarage rsync without deploying
	@./deploy/deploy-barbergarage.sh --dry-run

.PHONY: deploy-all
deploy-all: deploy deploy-barbergarage ## Deploy apigoalgus API and barbergarage site

.PHONY: ssh
ssh: ## SSH to Superhosting (uses deploy/deploy.env)
	@set -a && . deploy/deploy.env && set +a && \
	ssh -p "$${DEPLOY_SSH_PORT}" "$${DEPLOY_SSH_USER}@$${DEPLOY_SSH_HOST}"

# ---------------------------------------------------------------------------
# Misc
# ---------------------------------------------------------------------------

.PHONY: open
open: ## Open local app in browser (macOS)
	@open $(APP_URL)

.PHONY: urls
urls: ## Print local URLs and DB connection info
	@echo "App:      $(APP_URL)"
	@echo "MySQL:    127.0.0.1:33069 (user: apigoalgus, db: apigoalgus)"
