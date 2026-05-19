#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
SOURCE_DIR="${PROJECT_ROOT}/barbergarage"
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

CURRENT_BRANCH="$(git branch --show-current)"

if [[ ! -f "${ENV_FILE}" ]]; then
    echo "Error: ${ENV_FILE} not found. Run: make deploy-setup"
    exit 1
fi

if [[ ! -d "${SOURCE_DIR}" ]]; then
    echo "Error: ${SOURCE_DIR} not found."
    exit 1
fi

# shellcheck source=/dev/null
source "${ENV_FILE}"

: "${DEPLOY_SSH_HOST:?DEPLOY_SSH_HOST is required}"
: "${DEPLOY_SSH_PORT:?DEPLOY_SSH_PORT is required}"
: "${DEPLOY_SSH_USER:?DEPLOY_SSH_USER is required}"

DEPLOY_BARBERGARAGE_REMOTE_PATH="${DEPLOY_BARBERGARAGE_REMOTE_PATH:-public_html/barbergarage}"

SSH_TARGET="${DEPLOY_SSH_USER}@${DEPLOY_SSH_HOST}"
RSYNC_TARGET="${SSH_TARGET}:${DEPLOY_BARBERGARAGE_REMOTE_PATH}/"
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
    echo "Barbergarage deploy check passed:"
    echo "  Branch:  ${CURRENT_BRANCH}"
    echo "  Source:  ${SOURCE_DIR}/"
    echo "  Target:  ${RSYNC_TARGET}"
    echo "  SSH:     ssh -p ${DEPLOY_SSH_PORT} ${SSH_TARGET}"
    exit 0
fi

RSYNC_FLAGS=(-avz)
if [[ "${DRY_RUN}" == true ]]; then
    RSYNC_FLAGS+=(-n)
    echo "==> DRY RUN — no changes will be made"
fi

echo "==> Deploying barbergarage (static) from branch '${CURRENT_BRANCH}' to ${RSYNC_TARGET}"

rsync "${RSYNC_FLAGS[@]}" --delete \
    -e "ssh ${SSH_OPTS[*]}" \
    --exclude='.DS_Store' \
    --exclude='._*' \
    "${SOURCE_DIR}/" \
    "${RSYNC_TARGET}"

echo "==> Barbergarage deploy finished."
