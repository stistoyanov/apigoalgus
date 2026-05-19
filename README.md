# api.goalgus.bg (apigoalgus)

Laravel 11 API application for [api.goalgus.bg](https://api.goalgus.bg), hosted on Superhosting (cPanel).

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Mac)
- Git

## Local development (Docker)

1. Copy environment file and adjust if needed:

   ```bash
   cp .env.example .env
   ```

2. Start containers:

   ```bash
   docker compose up -d --build
   ```

3. Install dependencies and initialize the app:

   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate
   ```

4. Open the app: [http://localhost:8069](http://localhost:8069)

### Ports

| Service | URL / connection |
|---------|------------------|
| Web     | http://localhost:8069 |
| MySQL   | `localhost:33069` (user: `apigoalgus`, password: `secret`, database: `apigoalgus`) |

Inside Docker, the app uses `DB_HOST=mysql` (the Compose service name). From your Mac (TablePlus, etc.), use port **33069**.

### Useful commands

```bash
docker compose down          # stop containers
docker compose logs -f app   # follow app logs
docker compose exec app php artisan tinker
```

## Git branches

| Branch | Purpose |
|--------|---------|
| `main` | Daily development |
| `live` | Production-ready code; **only this branch may be deployed** |

Workflow: develop on `main` → merge into `live` when ready → deploy from `live`.

## Deploy to Superhosting

Deploy uses `rsync` over SSH and only runs from the `live` branch with a clean working tree.

1. Configure deploy credentials (once):

   ```bash
   cp deploy/deploy.env.example deploy/deploy.env
   ```

   Edit `deploy/deploy.env` if needed. Defaults target `goalgusb@goalgus.bg` on port `1022`, path `~/public_html/apigoalgus`.

2. Merge your changes into `live`:

   ```bash
   git checkout live
   git merge main
   ```

3. Deploy:

   ```bash
   ./deploy/deploy.sh
   ```

The script syncs code (excluding `.env`, `vendor/`, etc.) and runs on the server: `composer install --no-dev`, `migrate`, and Laravel caches. Production `.env` is never overwritten.

### SSH access

```bash
ssh -p 1022 goalgusb@goalgus.bg
```

Remote app path: `~/public_html/apigoalgus`

## Security

- Never commit `.env` — it is in `.gitignore`.
- `deploy/deploy.env` is gitignored (copy from `deploy.env.example`).
- If credentials were ever committed, rotate them on Superhosting.

## Production notes

- Server PHP handler is configured via root `.htaccess` (Superhosting PHP 8.3).
- `vendor/` is installed on the server during deploy, not synced from local.
- Document root on cPanel may differ from local Docker (`public/` locally). Verify in the hosting panel if routing issues occur.
