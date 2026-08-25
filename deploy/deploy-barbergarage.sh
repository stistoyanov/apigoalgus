#!/usr/bin/env bash
# Thin wrapper — prefer: make deploy-barbergarage
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
exec "${SCRIPT_DIR}/deploy-static-site.sh" barbergarage "$@"
