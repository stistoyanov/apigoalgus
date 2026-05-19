#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
ENV_FILE="${SCRIPT_DIR}/deploy.env"

DRY_RUN=false
CHECK_ONLY=false

for arg in "$@"; do
    case "${arg}" in
        --dry-run) DRY_RUN=true ;;
        --check) CHECK_ONLY=true ;;
    esac
done

cd "${PROJECT_ROOT}"

# --- Guards ---
CURRENT_BRANCH="$(git branch --show-current)"

if [[ ! -f "${ENV_FILE}" ]]; then
    echo "Error: ${ENV_FILE} not found. Run: make deploy-setup"
    exit 1
fi

# shellcheck source=/dev/null
source "${ENV_FILE}"

: "${DEPLOY_SSH_HOST:?DEPLOY_SSH_HOST is required}"
: "${DEPLOY_SSH_PORT:?DEPLOY_SSH_PORT is required}"
: "${DEPLOY_SSH_USER:?DEPLOY_SSH_USER is required}"
: "${DEPLOY_REMOTE_PATH:?DEPLOY_REMOTE_PATH is required}"

DEPLOY_PHP_BIN="${DEPLOY_PHP_BIN:-php}"
DEPLOY_COMPOSER_BIN="${DEPLOY_COMPOSER_BIN:-/opt/cpanel/composer/bin/composer}"

SSH_TARGET="${DEPLOY_SSH_USER}@${DEPLOY_SSH_HOST}"
RSYNC_TARGET="${SSH_TARGET}:${DEPLOY_REMOTE_PATH}/"
SSH_OPTS=(-p "${DEPLOY_SSH_PORT}")

if [[ "${CURRENT_BRANCH}" != "live" ]]; then
    echo "Error: deploy only allowed from the 'live' branch (current: ${CURRENT_BRANCH})."
    exit 1
fi

if [[ -n "$(git status --porcelain)" ]]; then
    echo "Error: working tree is not clean. Commit or stash changes before deploying."
    exit 1
fi

if [[ "${CHECK_ONLY}" == true ]]; then
    echo "Deploy check passed:"
    echo "  Branch:   ${CURRENT_BRANCH}"
    echo "  Target:   ${RSYNC_TARGET}"
    echo "  SSH:      ssh -p ${DEPLOY_SSH_PORT} ${SSH_TARGET}"
    echo "  PHP:      ${DEPLOY_PHP_BIN}"
    echo "  Composer: ${DEPLOY_PHP_BIN} ${DEPLOY_COMPOSER_BIN}"
    exit 0
fi

RSYNC_FLAGS=(-avz)
if [[ "${DRY_RUN}" == true ]]; then
    RSYNC_FLAGS+=(-n)
    echo "==> DRY RUN — no changes will be made"
fi

echo "==> Deploying from branch '${CURRENT_BRANCH}' to ${RSYNC_TARGET}"

rsync "${RSYNC_FLAGS[@]}" --delete \
    -e "ssh ${SSH_OPTS[*]}" \
    --exclude='.git/' \
    --exclude='.env' \
    --exclude='.env.live' \
    --exclude='.env.backup' \
    --exclude='.env.production' \
    --exclude='vendor/' \
    --exclude='node_modules/' \
    --exclude='storage/logs/' \
    --exclude='storage/framework/cache/data/' \
    --exclude='storage/framework/sessions/' \
    --exclude='storage/framework/views/' \
    --exclude='bootstrap/cache/*.php' \
    --exclude='docker/' \
    --exclude='deploy/deploy.env' \
    --exclude='.DS_Store' \
    --exclude='cache/' \
    --exclude='cgi-bin/' \
    --exclude='tests/' \
    --exclude='barbergarage/' \
    "${PROJECT_ROOT}/" \
    "${RSYNC_TARGET}"

if [[ "${DRY_RUN}" == true ]]; then
    echo "==> Dry run complete. Remote commands skipped."
    exit 0
fi

echo "==> Running post-deploy commands on server..."

ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" bash -s <<REMOTE
set -euo pipefail
cd ~/${DEPLOY_REMOTE_PATH}

${DEPLOY_PHP_BIN} ${DEPLOY_COMPOSER_BIN} install --no-dev --optimize-autoloader --no-interaction
${DEPLOY_PHP_BIN} artisan migrate --force
${DEPLOY_PHP_BIN} artisan config:cache
${DEPLOY_PHP_BIN} artisan route:cache
${DEPLOY_PHP_BIN} artisan view:cache

echo "Deploy finished successfully."
REMOTE

echo "==> Done."
