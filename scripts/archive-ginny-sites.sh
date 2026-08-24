#!/usr/bin/env bash
# Re-archive Ginny WordPress sites + media while the old domains are still live.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ARCHIVES="${ROOT}/archives"
UA="Mozilla/5.0 (compatible; GoalGusArchive/1.0)"

mkdir -p "${ARCHIVES}"

echo "==> Mirroring ginnyrockbar.com"
mkdir -p "${ARCHIVES}/ginnyrockbar"
wget --mirror --convert-links --adjust-extension --page-requisites --no-parent \
  --no-host-directories --directory-prefix="${ARCHIVES}/ginnyrockbar" \
  --user-agent="${UA}" \
  --wait=0.3 --random-wait --timeout=30 --tries=3 \
  --reject="xmlrpc.php*,wp-login.php*,wp-admin/*,feed/*,comments/feed/*" \
  --exclude-directories=/wp-json,/wp-admin,/xmlrpc.php \
  "https://ginnyrockbar.com/" \
  "https://ginnyrockbar.com/programa/" \
  "https://ginnyrockbar.com/gallery/" \
  || true

echo "==> Mirroring ginnys-kitchen.com"
mkdir -p "${ARCHIVES}/ginnys-kitchen"
wget --mirror --convert-links --adjust-extension --page-requisites --no-parent \
  --no-host-directories --directory-prefix="${ARCHIVES}/ginnys-kitchen" \
  --user-agent="${UA}" \
  --wait=0.3 --random-wait --timeout=30 --tries=3 \
  --reject="xmlrpc.php*,wp-login.php*,wp-admin/*,feed/*,comments/feed/*" \
  --exclude-directories=/wp-json,/wp-admin,/xmlrpc.php \
  "https://ginnys-kitchen.com/" \
  "https://ginnys-kitchen.com/ginnys-menu/" \
  "https://ginnys-kitchen.com/en/home/" \
  || true

echo "==> Downloading media libraries into ginny/images and kitchen/images"
python3 << PY
import json, os, time, urllib.parse, urllib.request
from pathlib import Path

ROOT = Path(${ROOT@Q})
UA = {"User-Agent": ${UA@Q}}

def quote_url(u: str) -> str:
    parts = urllib.parse.urlsplit(u.replace("http://", "https://"))
    path = urllib.parse.quote(urllib.parse.unquote(parts.path), safe="/")
    return urllib.parse.urlunsplit((parts.scheme, parts.netloc, path, parts.query, parts.fragment))

def download_media(site: str, out_dir: Path) -> None:
    out_dir.mkdir(parents=True, exist_ok=True)
    urls = []
    page = 1
    while True:
        api = f"https://{site}/wp-json/wp/v2/media?per_page=100&page={page}"
        req = urllib.request.Request(api, headers=UA)
        with urllib.request.urlopen(req, timeout=60) as r:
            data = json.load(r)
        if not data:
            break
        for item in data:
            src = item.get("source_url")
            if src:
                urls.append(src)
            sizes = (item.get("media_details") or {}).get("sizes") or {}
            for key in ("large", "medium_large", "full"):
                if key in sizes and sizes[key].get("source_url"):
                    urls.append(sizes[key]["source_url"])
        if len(data) < 100:
            break
        page += 1

    seen = set()
    unique = []
    for u in urls:
        if u not in seen:
            seen.add(u)
            unique.append(u)

    ok = fail = 0
    for u in unique:
        path = u.split("/wp-content/uploads/", 1)
        if len(path) != 2:
            continue
        rel = path[1].split("?", 1)[0]
        dest = out_dir / rel
        try:
            dest.relative_to(out_dir).as_posix().encode("ascii")
        except UnicodeEncodeError:
            dest = dest.parent / ("cyrillic-" + str(abs(hash(rel)))[:10] + dest.suffix)
        if dest.exists() and dest.stat().st_size > 0:
            ok += 1
            continue
        dest.parent.mkdir(parents=True, exist_ok=True)
        try:
            req = urllib.request.Request(quote_url(u), headers=UA)
            with urllib.request.urlopen(req, timeout=60) as r:
                dest.write_bytes(r.read())
            ok += 1
        except Exception as e:
            fail += 1
            print(f"FAIL {u}: {e}")
        time.sleep(0.05)
    print(f"{site}: {ok} ok, {fail} fail")

download_media("ginnyrockbar.com", ROOT / "ginny" / "images")
download_media("ginnys-kitchen.com", ROOT / "kitchen" / "images")
PY

echo "Done. Archives in ${ARCHIVES}/ (gitignored)."
