#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

EXPORT_DIR="$ROOT_DIR/exports/wordpress"
mkdir -p "$EXPORT_DIR"

run_export() {
  local mode="$1"
  local target="$2"

  echo "Exporting ${mode} -> ${target}"
  docker compose exec -T wordpress php /dev/stdin "$mode" < "$ROOT_DIR/scripts/wordpress_phase1_export.php" > "$target"
}

run_export "settings" "$EXPORT_DIR/settings.json"
run_export "inventory" "$EXPORT_DIR/inventory.json"
run_export "permalink-map" "$EXPORT_DIR/permalink-manager-uris.json"
run_export "content" "$EXPORT_DIR/content.json"
run_export "products" "$EXPORT_DIR/products.json"
run_export "routes" "$EXPORT_DIR/routes.json"
run_export "translations" "$EXPORT_DIR/translations.json"
run_export "seo" "$EXPORT_DIR/seo.json"

echo
echo "Phase 1 WordPress export completed."
echo "Files written to: $EXPORT_DIR"
