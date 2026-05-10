#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="${ROOT_DIR}/scripts/live_ops.env"

if [[ -f "${ENV_FILE}" ]]; then
  # shellcheck disable=SC1090
  source "${ENV_FILE}"
fi

BASE_URL="${AVC_OPS_BASE_URL:-}"
KEY="${AVC_OPS_READONLY_KEY:-}"
SCOPE="${1:-health}"
shift || true

if [[ -z "${BASE_URL}" || -z "${KEY}" ]]; then
  echo "Missing AVC_OPS_BASE_URL or AVC_OPS_READONLY_KEY. Copy scripts/live_ops.env.example to scripts/live_ops.env." >&2
  exit 1
fi

ARGS=(
  --fail
  --silent
  --show-error
  --get "${BASE_URL%/}/ops-readonly"
  --data-urlencode "scope=${SCOPE}"
  --data-urlencode "key=${KEY}"
)

for pair in "$@"; do
  ARGS+=(--data-urlencode "${pair}")
done

curl "${ARGS[@]}"
