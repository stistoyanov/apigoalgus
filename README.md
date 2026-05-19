# api.goalgus.bg (apigoalgus)

Laravel 11 API application for [api.goalgus.bg](https://api.goalgus.bg), hosted on Superhosting (cPanel).

**Repository:** [https://github.com/stistoyanov/apigoalgus](https://github.com/stistoyanov/apigoalgus)

---

## Table of contents

- [Prerequisites](#prerequisites)
- [Makefile commands](#makefile-commands)
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

`make` is available on macOS by default (Xcode Command Line Tools).

---

## Makefile commands

Run `make` or `make help` to list all targets. Common shortcuts:

| Command | Description |
|---------|-------------|
| `make setup` | First-time local setup (`.env`, Docker, composer, key, migrate) |
| `make up` / `make down` | Start / stop containers |
| `make logs-scheduler` | Follow scheduler container logs |
| `make shell` | Bash shell inside the app container |
| `make install` | `composer install` in Docker |
| `make migrate` | `php artisan migrate` |
| `make artisan cmd="route:list"` | Any Artisan command |
| `make tinker` | Laravel Tinker |
| `make test` | Run PHPUnit |
| `make deploy-setup` | Create `deploy/deploy.env` from example |
| `make deploy-merge` | Merge `main` into `live` |
| `make deploy-dry-run` | Preview deploy (rsync dry run) |
| `make deploy` | Deploy Laravel API to `public_html/apigoalgus` |
| `make deploy-barbergarage` | Deploy static site to `public_html/barbergarage` |
| `make deploy-all` | Deploy both API and barbergarage |
| `make ssh` | SSH to production server |

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

After cloning, run:

```bash
make setup
make open
```

This copies `.env.example` → `.env` (if needed), builds and starts Docker, runs `composer install`, `key:generate`, and `migrate`.

You should see the Laravel welcome page. If you get a 500 error, confirm `.env` has `DB_HOST=mysql` (not `localhost`) — that hostname is correct **inside** Docker.

Manual equivalent:

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

---

## Local development (Docker)

### Stack

| Service | Container | Host access |
|---------|-----------|-------------|
| Web (nginx + PHP 8.3-FPM) | `nginx` + `app` | [http://localhost:8069](http://localhost:8069) |
| Scheduler | `scheduler` | Runs `php artisan schedule:work` (no host port) |
| MySQL 8.0 | `mysql` | `127.0.0.1:33069` |

**Scheduler (local vs production)**

- **Local Docker:** the `scheduler` container runs [`schedule:work`](https://laravel.com/docs/scheduling#running-the-scheduler-locally) continuously — no cron daemon required.
- **Superhosting:** keep your existing cPanel cron entry: `* * * * * php /path/to/artisan schedule:run`.

Define tasks in [`bootstrap/app.php`](bootstrap/app.php) inside `->withSchedule(...)`.

```bash
make logs-scheduler   # watch scheduler output
```

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
make up              # start containers
make down            # stop containers
make build           # rebuild and start
make logs            # app logs
make shell           # shell in app container
make migrate         # run migrations
make artisan cmd="make:model Post"
make composer args="require package/name"
make cache-clear
```

Or use `docker compose` directly if you prefer.

### Frontend

No Node.js or build step. The landing page uses plain CSS and vanilla JavaScript in `public/css/` and `public/js/`. Brand images live in `public/images/`.

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
   make deploy-setup
   ```

2. Edit `deploy/deploy.env` if needed. Defaults:

   ```bash
   DEPLOY_SSH_HOST=goalgus.bg
   DEPLOY_SSH_PORT=1022
   DEPLOY_SSH_USER=goalgusb
   DEPLOY_REMOTE_PATH=public_html/apigoalgus
   DEPLOY_BARBERGARAGE_REMOTE_PATH=public_html/barbergarage
   DEPLOY_PHP_BIN=/usr/local/bin/ea-php83
   DEPLOY_COMPOSER_BIN=/opt/cpanel/composer/bin/composer
   ```

   On Superhosting, `composer` is not on the default PATH — use cPanel’s binary with PHP 8.3 (matches the web handler).

3. Verify paths over SSH if needed:

   ```bash
   ssh -p 1022 goalgusb@goalgus.bg
   /usr/local/bin/ea-php83 -v
   /opt/cpanel/composer/bin/composer --version
   ```

4. Ensure production `.env` already exists on the server at `~/public_html/apigoalgus/.env`. The deploy script **never** uploads `.env` from your machine.

### Deploy steps

```bash
make deploy-merge       # merge main into live (optional helper)
git checkout live
git status              # must be clean

make deploy-check       # verify branch, deploy.env, clean tree
make deploy-dry-run     # optional: preview rsync
make deploy
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

**Excluded from sync** (stay on server or are rebuilt there): `.env`, `vendor/`, `node_modules/`, `storage/logs/`, compiled views/cache, `docker/`, `.git/`, `barbergarage/`.

### Deploy barbergarage (static HTML/JS)

The [barbergarage/](barbergarage/) folder is a separate static website (HTML, CSS, vanilla JS). It deploys to its own path on the same server — **not** inside the Laravel app.

| Setting | Value |
|---------|-------|
| Local source | `barbergarage/` |
| Remote path | `~/public_html/barbergarage` |
| Live URL | Your barbergarage.bg domain (document root must point at this folder in cPanel) |

```bash
git checkout live
git status              # must be clean

make deploy-barbergarage-check
make deploy-barbergarage-dry-run   # optional preview
make deploy-barbergarage
```

No Composer or Artisan steps — rsync only. To deploy **both** sites in one go:

```bash
make deploy-all
```

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
QUEUE_CONNECTION=sync
```

On production, set `QUEUE_CONNECTION=sync` in the server `.env` (deploy does not sync `.env`).

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
- All jobs run **synchronously** (`QUEUE_CONNECTION=sync`) — no queue worker or `jobs` database tables. Scheduled tasks use the `scheduler` container locally and cPanel cron (`schedule:run`) on production.

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

Update `DEPLOY_PHP_BIN` and `DEPLOY_COMPOSER_BIN` in `deploy/deploy.env` (see `deploy/deploy.env.example`).
