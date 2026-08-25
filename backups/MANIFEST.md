# Static site backups (pre-redesign)

Dated snapshots of the live PHP shells **before** the 2026 redesign.
Kept in Git so we can restore or compare while redesigning locally.

**Created:** 2026-08-25 (after first production deploy to ginny.bg / kitchen.ginny.bg)

| Snapshot | Source | Live domain | Restore |
|----------|--------|-------------|---------|
| `backups/ginny-2026-08-25/` | `ginny/` | https://ginny.bg | `rsync -a --delete backups/ginny-2026-08-25/ ginny/` |
| `backups/kitchen-2026-08-25/` | `kitchen/` | https://kitchen.ginny.bg | `rsync -a --delete backups/kitchen-2026-08-25/ kitchen/` |

## What these are

- PHP shells (`index.php`, `lib/`, `css/`, `js/`, pages) + curated `images/`
- Same tree that `make deploy-ginny` / `make deploy-kitchen` ship

## What these are not

- WordPress HTML mirrors live in gitignored `archives/` (see `archives/MANIFEST.md` locally)
- Do **not** deploy `backups/` — `deploy/deploy.sh` and static deploy scripts ignore this folder

## Create a new snapshot

```bash
make backup-ginny-sites
```
