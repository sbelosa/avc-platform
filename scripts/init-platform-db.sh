#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

if [[ -f .env ]]; then
  set -a
  # shellcheck disable=SC1091
  source ./.env
  set +a
fi

echo "Creating platform database and user..."
docker compose exec -T db_avc mariadb -uroot "-p${DB_ROOT_PASSWORD}" -e "
CREATE DATABASE IF NOT EXISTS \`${PLATFORM_DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${PLATFORM_DB_USER}'@'%' IDENTIFIED BY '${PLATFORM_DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${PLATFORM_DB_NAME}\`.* TO '${PLATFORM_DB_USER}'@'%';
FLUSH PRIVILEGES;
"

echo "Applying AVC platform schema files..."
while IFS= read -r schema_file; do
  echo "  -> $(basename "$schema_file")"
  docker compose exec -T db_avc mariadb -uroot "-p${DB_ROOT_PASSWORD}" "${PLATFORM_DB_NAME}" < "$schema_file"
done < <(find "$ROOT_DIR/platform/database" -maxdepth 1 -name '*.sql' | sort)

echo "Applying platform schema normalization..."
docker compose exec -T db_avc mariadb -uroot "-p${DB_ROOT_PASSWORD}" "${PLATFORM_DB_NAME}" -e "
ALTER TABLE content_translations
  ADD COLUMN IF NOT EXISTS source_wp_post_id BIGINT UNSIGNED NULL AFTER content_item_id,
  ADD INDEX IF NOT EXISTS idx_content_translations_source_wp_post (source_wp_post_id),
  ADD INDEX IF NOT EXISTS idx_content_translations_language_code (language_code),
  DROP INDEX IF EXISTS uq_content_translation_slug;
"

echo "Platform database is ready."
