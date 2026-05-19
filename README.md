# api.goalgus.bg (apigoalgus)

Laravel 11 API application for [api.goalgus.bg](https://api.goalgus.bg), hosted on Superhosting (cPanel).

**Repository:** [https://github.com/stistoyanov/apigoalgus](https://github.com/stistoyanov/apigoalgus)

---

## Table of contents

- [Prerequisites](#prerequisites)
- [Clone the project](#clone-the-project)
- [First-time local setup](#first-time-local-setup)
- [Local development (Docker)](#local-development-docker)
- [Git workflow](#git-workflow)
- [Deploy to Superhosting](#deploy-to-superhosting)
- [Environment files](#environment-files)
- [Security](#security)
- [Production notes](#production-notes)
- [Troubleshooting](#troubleshooting)

---

## Prerequisites

Install on your Mac before you start:

| Tool | Purpose |
|------|---------|
| [Git](https://git-scm.com/) | Version control |
| [Docker Desktop](https://www.docker.com/products/docker-desktop/) | Local PHP, nginx, MySQL |
| SSH client | Deploy to Superhosting (`ssh`, `rsync` — built into macOS) |

Optional: [TablePlus](https://tableplus.com/) or another MySQL client to inspect the local database on port `33069`.

---

## Clone the project

```bash
git clone https://github.com/stistoyanov/apigoalgus.git
cd apigoalgus
```

Check out the branch you need:

```bash
git checkout main    # daily development (default)
git checkout live    # production-ready code (deploy from this branch only)
```

---

## First-time local setup

Run these steps once after cloning:

```bash
# 1. Local environment (Docker values — see .env.example)
cp .env.example .env

# 2. Start containers
docker compose up -d --build

# 3. Install PHP dependencies and initialize Laravel
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate

# 4. Open the app
open http://localhost:8069
```

You should see the Laravel welcome page. If you get a 500 error, confirm `.env` has `DB_HOST=mysql` (not `localhost`) — that hostname is correct **inside** Docker.

---

## Local development (Docker)

### Stack

| Service | Container | Host access |
|---------|-----------|-------------|
| Web (nginx + PHP 8.3-FPM) | `nginx` + `app` | [http://localhost:8069](http://localhost:8069) |
| MySQL 8.0 | `mysql` | `127.0.0.1:33069` |

Default database credentials (local only):

| Setting | Value |
|---------|-------|
| Host (from Mac) | `127.0.0.1` |
| Port | `33069` |
| Database | `apigoalgus` |
| Username | `apigoalgus` |
| Password | `secret` |

Inside the app container, `.env` must use `DB_HOST=mysql` and `DB_PORT=3306` (see `.env.example`).

### Day-to-day commands

```bash
# Start / stop
docker compose up -d
docker compose down

# Logs
docker compose logs -f app
docker compose logs -f nginx

# Artisan
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app php artisan route:list

# Composer
docker compose exec app composer install
docker compose exec app composer update

# Rebuild after Dockerfile changes
docker compose up -d --build
```

### Frontend assets (optional)

The welcome page works without a build. To compile Vite assets on the host:

```bash
npm install
npm run dev
```

---

## Git workflow

| Branch | Purpose |
|--------|---------|
| `main` | Daily development — commit and test here |
| `live` | Production-ready — **only this branch may be deployed** |

Typical flow:

```bash
# Work on main
git checkout main
# ... edit, commit, push ...
git push origin main

# When ready for production
git checkout live
git merge main
git push origin live

# Deploy (from live only — see below)
./deploy/deploy.sh
```

Push both branches to GitHub:

```bash
git push -u origin main
git push -u origin live
```

---

## Deploy to Superhosting

Deploy syncs code with `rsync` over SSH. It **only** runs from the `live` branch with a **clean** working tree (no uncommitted changes).

**Target server**

| Setting | Value |
|---------|-------|
| SSH | `ssh -p 1022 goalgusb@goalgus.bg` |
| Remote path | `~/public_html/apigoalgus` |
| Live URL | [https://api.goalgus.bg](https://api.goalgus.bg) |

### One-time deploy setup

1. Copy deploy config (not committed to Git):

   ```bash
   cp deploy/deploy.env.example deploy/deploy.env
   ```

2. Edit `deploy/deploy.env` if needed. Defaults:

   ```bash
   DEPLOY_SSH_HOST=goalgus.bg
   DEPLOY_SSH_PORT=1022
   DEPLOY_SSH_USER=goalgusb
   DEPLOY_REMOTE_PATH=public_html/apigoalgus
   DEPLOY_PHP_BIN=php
   DEPLOY_COMPOSER=composer
   ```

3. On the server, verify PHP and Composer paths over SSH:

   ```bash
   ssh -p 1022 goalgusb@goalgus.bg
   which php
   which composer
   ```

   Put the correct values in `DEPLOY_PHP_BIN` and `DEPLOY_COMPOSER` in `deploy/deploy.env`.

4. Ensure production `.env` already exists on the server at `~/public_html/apigoalgus/.env`. The deploy script **never** uploads `.env` from your machine.

### Deploy steps

```bash
git checkout live
git merge main          # if needed
git status              # must be clean

./deploy/deploy.sh
```

### What the deploy script does

1. Verifies current branch is `live`.
2. Verifies working tree is clean.
3. `rsync` project to `goalgusb@goalgus.bg:~/public_html/apigoalgus` (port `1022`).
4. On the server:
   - `composer install --no-dev --optimize-autoloader`
   - `php artisan migrate --force`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`

**Excluded from sync** (stay on server or are rebuilt there): `.env`, `vendor/`, `node_modules/`, `storage/logs/`, compiled views/cache, `docker/`, `.git/`.

---

## Environment files

| File | Committed | Use |
|------|-----------|-----|
| `.env.example` | Yes | Template for **local Docker** — copy to `.env` |
| `.env` | No (gitignored) | Your local settings — create from `.env.example` |
| `.env.live` | No (keep local only) | Optional reference copy of **production** values — do not commit |
| `deploy/deploy.env.example` | Yes | Template for deploy SSH settings |
| `deploy/deploy.env` | No (gitignored) | Your deploy SSH/PHP paths |

**Local `.env` (Docker)** — key values:

```dotenv
APP_URL=http://localhost:8069
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=apigoalgus
DB_USERNAME=apigoalgus
DB_PASSWORD=secret
```

**Production `.env`** — lives only on Superhosting; set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://api.goalgus.bg`, and real database credentials. Manage it via SSH or cPanel File Manager.

---

## Security

- Never commit `.env`, `.env.live`, or `deploy/deploy.env`.
- Never commit `vendor/`, `node_modules/`, or the production `cache/` folder.
- If credentials were ever pushed to GitHub, rotate them on Superhosting and regenerate `APP_KEY` on production.
- Production `.env` is protected on the server by root `.htaccess` (dotfiles denied).

---

## Production notes

- PHP on Superhosting is configured via root [`.htaccess`](.htaccess) (PHP 8.3 handler).
- `vendor/` is installed on the server during deploy, not synced from your Mac.
- Local Docker uses `public/` as the web root; cPanel may point the subdomain at the project root or `public/` — check the hosting panel if URLs behave differently than locally.
- Queue worker and scheduler cron are not configured yet (out of scope for initial setup).

---

## Troubleshooting

### HTTP 500 on http://localhost:8069

- Confirm `.env` has `DB_HOST=mysql` (Docker service name).
- Run migrations: `docker compose exec app php artisan migrate`
- Check logs: `docker compose logs app` and `storage/logs/laravel.log`

### Database connection refused from Mac

Use port **33069**, not `3306`. Host: `127.0.0.1`.

### Deploy script: "only allowed from the live branch"

```bash
git checkout live
git merge main
```

### Deploy script: "working tree is not clean"

Commit or stash changes, then deploy again.

### Composer fails on server after deploy

SSH in and run manually:

```bash
ssh -p 1022 goalgusb@goalgus.bg
cd ~/public_html/apigoalgus
composer install --no-dev --optimize-autoloader
php artisan migrate --force
```

Update `DEPLOY_PHP_BIN` / `DEPLOY_COMPOSER` in `deploy/deploy.env` with the paths from `which php` and `which composer`.
