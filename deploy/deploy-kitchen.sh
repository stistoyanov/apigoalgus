#!/usr/bin/env bash
# Thin wrapper — prefer: make deploy-kitchen
set -euo pipefail
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
exec "${SCRIPT_DIR}/deploy-static-site.sh" kitchen "$@"
