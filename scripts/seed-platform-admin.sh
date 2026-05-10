#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

EMAIL="${1:-admin@aloevera-centar.local}"
PASSWORD="${2:-AVC-local-test-2026!}"
FULL_NAME="${3:-AVC Local Admin}"

docker exec avc-platform php /var/www/html/scripts/seed_admin_user.php "$EMAIL" "$PASSWORD" "$FULL_NAME"
