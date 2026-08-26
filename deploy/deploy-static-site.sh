#!/usr/bin/env bash
# Deploy a static/PHP document-root site from this repo to Superhosting.
#
# Usage:
#   ./deploy/deploy-static-site.sh <site> [--check|--dry-run]
#
# Sites: barbergarage | ginny | kitchen
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
ENV_FILE="${SCRIPT_DIR}/deploy.env"

SITE="${1:-}"
if [[ -z "${SITE}" || "${SITE}" == --* ]]; then
    echo "Usage: $0 <barbergarage|ginny|kitchen> [--check|--dry-run]"
    exit 1
fi
shift || true

DRY_RUN=false
CHECK_ONLY=false

for arg in "$@"; do
    case "${arg}" in
        --dry-run) DRY_RUN=true ;;
        --check) CHECK_ONLY=true ;;
        *)
            echo "Error: unknown option '${arg}'"
            exit 1
            ;;
    esac
done

cd "${PROJECT_ROOT}"

if [[ ! -f "${ENV_FILE}" ]]; then
    echo "Error: ${ENV_FILE} not found. Run: make deploy-setup"
    exit 1
fi

# shellcheck source=/dev/null
source "${ENV_FILE}"

: "${DEPLOY_SSH_HOST:?DEPLOY_SSH_HOST is required}"
: "${DEPLOY_SSH_PORT:?DEPLOY_SSH_PORT is required}"
: "${DEPLOY_SSH_USER:?DEPLOY_SSH_USER is required}"

case "${SITE}" in
    barbergarage)
        SOURCE_DIR="${PROJECT_ROOT}/barbergarage"
        REMOTE_PATH="${DEPLOY_BARBERGARAGE_REMOTE_PATH:-public_html/barbergarage}"
        EXTRA_EXCLUDES=(
            --exclude='config.php'
            --exclude='cache/*'
            --exclude='!cache/.gitignore'
            --exclude='!cache/.htaccess'
        )
        ;;
    ginny)
        SOURCE_DIR="${PROJECT_ROOT}/ginny"
        REMOTE_PATH="${DEPLOY_GINNY_REMOTE_PATH:-public_html/ginny}"
        EXTRA_EXCLUDES=(
            --exclude='config.php'
            --exclude='cache/*'
            --exclude='!cache/.gitignore'
            --exclude='!cache/.htaccess'
        )
        ;;
    kitchen)
        SOURCE_DIR="${PROJECT_ROOT}/kitchen"
        REMOTE_PATH="${DEPLOY_KITCHEN_REMOTE_PATH:-public_html/kitchen}"
        EXTRA_EXCLUDES=(
            --exclude='config.php'
            --exclude='cache/*'
            --exclude='!cache/.gitignore'
            --exclude='!cache/.htaccess'
        )
        ;;
    *)
        echo "Error: unknown site '${SITE}'. Use: barbergarage | ginny | kitchen"
        exit 1
        ;;
esac

if [[ ! -d "${SOURCE_DIR}" ]]; then
    echo "Error: ${SOURCE_DIR} not found."
    exit 1
fi

SSH_TARGET="${DEPLOY_SSH_USER}@${DEPLOY_SSH_HOST}"
RSYNC_TARGET="${SSH_TARGET}:${REMOTE_PATH}/"
SSH_OPTS=(-p "${DEPLOY_SSH_PORT}")

CURRENT_BRANCH="$(git branch --show-current)"

if [[ "${CURRENT_BRANCH}" != "live" ]]; then
    echo "Error: deploy only allowed from the 'live' branch (current: ${CURRENT_BRANCH})."
    exit 1
fi

if [[ -n "$(git status --porcelain)" ]]; then
    echo "Error: working tree is not clean. Commit or stash changes before deploying."
    exit 1
fi

if [[ "${CHECK_ONLY}" == true ]]; then
    echo "${SITE} deploy check passed:"
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

echo "==> Deploying ${SITE} from branch '${CURRENT_BRANCH}' to ${RSYNC_TARGET}"

rsync "${RSYNC_FLAGS[@]}" --delete \
    -e "ssh ${SSH_OPTS[*]}" \
    --exclude='.DS_Store' \
    --exclude='._*' \
    --exclude='.well-known' \
    "${EXTRA_EXCLUDES[@]}" \
    "${SOURCE_DIR}/" \
    "${RSYNC_TARGET}"

# Ensure document-root is web-readable (cPanel empty folders are often 0750).
if [[ "${DRY_RUN}" == false ]]; then
    echo "==> Fixing remote directory permissions on ${REMOTE_PATH}"
    ssh "${SSH_OPTS[@]}" "${SSH_TARGET}" \
        "find ~/${REMOTE_PATH} -type d -exec chmod 755 {} +; find ~/${REMOTE_PATH} -type f -exec chmod 644 {} +"
fi

echo "==> ${SITE} deploy finished."
