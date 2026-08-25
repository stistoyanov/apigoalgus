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
deploy-check: ## Verify API deploy prerequisites (branch, clean tree, deploy.env)
	@./deploy/deploy.sh --check

.PHONY: deploy
deploy: ## Deploy Laravel API only (live branch, clean tree)
	@./deploy/deploy.sh

.PHONY: deploy-dry-run
deploy-dry-run: ## Preview API rsync without deploying
	@./deploy/deploy.sh --dry-run

# Explicit aliases — same as deploy / deploy-check / deploy-dry-run
.PHONY: deploy-api
deploy-api: deploy ## Deploy Laravel API only (alias of make deploy)

.PHONY: deploy-api-check
deploy-api-check: deploy-check ## Verify API deploy prerequisites (alias of make deploy-check)

.PHONY: deploy-api-dry-run
deploy-api-dry-run: deploy-dry-run ## Preview API rsync (alias of make deploy-dry-run)

.PHONY: deploy-merge
deploy-merge: ## Merge main into live (does not deploy)
	@git checkout live
	@git merge main
	@echo "Merged main into live. Run 'make deploy-api' from the live branch when ready."

.PHONY: deploy-barbergarage-check
deploy-barbergarage-check: ## Verify barbergarage deploy prerequisites
	@./deploy/deploy-barbergarage.sh --check

.PHONY: deploy-barbergarage
deploy-barbergarage: ## Deploy barbergarage.bg (live branch, clean tree)
	@./deploy/deploy-barbergarage.sh

.PHONY: deploy-barbergarage-dry-run
deploy-barbergarage-dry-run: ## Preview barbergarage rsync without deploying
	@./deploy/deploy-barbergarage.sh --dry-run

.PHONY: deploy-ginny-check
deploy-ginny-check: ## Verify ginny.bg deploy prerequisites
	@./deploy/deploy-ginny.sh --check

.PHONY: deploy-ginny
deploy-ginny: ## Deploy ginny.bg (live branch, clean tree)
	@./deploy/deploy-ginny.sh

.PHONY: deploy-ginny-dry-run
deploy-ginny-dry-run: ## Preview ginny rsync without deploying
	@./deploy/deploy-ginny.sh --dry-run

.PHONY: deploy-kitchen-check
deploy-kitchen-check: ## Verify kitchen.ginny.bg deploy prerequisites
	@./deploy/deploy-kitchen.sh --check

.PHONY: deploy-kitchen
deploy-kitchen: ## Deploy kitchen.ginny.bg (live branch, clean tree)
	@./deploy/deploy-kitchen.sh

.PHONY: deploy-kitchen-dry-run
deploy-kitchen-dry-run: ## Preview kitchen rsync without deploying
	@./deploy/deploy-kitchen.sh --dry-run

.PHONY: deploy-sites
deploy-sites: deploy-barbergarage deploy-ginny deploy-kitchen ## Deploy all static sites (not the API)

.PHONY: deploy-all
deploy-all: deploy deploy-sites ## Deploy API + all static sites

.PHONY: ssh
ssh: ## SSH to Superhosting (uses deploy/deploy.env)
	@set -a && . deploy/deploy.env && set +a && \
	ssh -p "$${DEPLOY_SSH_PORT}" "$${DEPLOY_SSH_USER}@$${DEPLOY_SSH_HOST}"

# ---------------------------------------------------------------------------
# Ginny static sites (local preview)
# ---------------------------------------------------------------------------

.PHONY: serve-ginny
serve-ginny: ## Serve Ginny Rock Bar at http://localhost:8070
	@echo "Ginny Rock Bar → http://localhost:8070"
	@php -S localhost:8070 -t ginny

.PHONY: serve-kitchen
serve-kitchen: ## Serve Ginny's Kitchen at http://localhost:8071
	@echo "Ginny's Kitchen → http://localhost:8071"
	@php -S localhost:8071 -t kitchen

.PHONY: open-ginny
open-ginny: ## Open local Ginny Rock Bar in browser
	@open http://localhost:8070

.PHONY: open-kitchen
open-kitchen: ## Open local Ginny's Kitchen in browser
	@open http://localhost:8071

.PHONY: archive-ginny
archive-ginny: ## Re-download WP mirrors + media (while old domains are live)
	@./scripts/archive-ginny-sites.sh

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
	@echo "Ginny:    http://localhost:8070  (make serve-ginny)"
	@echo "Kitchen:  http://localhost:8071  (make serve-kitchen)"
