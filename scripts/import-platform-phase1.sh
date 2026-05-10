#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "Importing WordPress Phase 1 data into AVC platform..."
docker compose exec -T platform php /dev/stdin < "$ROOT_DIR/scripts/import-platform-phase1.php"

echo "AVC platform import completed."
