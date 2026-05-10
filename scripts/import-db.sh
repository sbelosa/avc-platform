#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

if [[ -f .env ]]; then
  set -a
  # shellcheck disable=SC1091
  source ./.env
  set +a
else
  set -a
  # shellcheck disable=SC1091
  source ./.env.example
  set +a
fi

DB_FILE="${1:-}"

if [[ -z "$DB_FILE" ]]; then
  DB_FILE="$(find "$ROOT_DIR/backups/db" -maxdepth 1 -type f \( -name "*.sql" -o -name "*.sql.gz" \) | sort | head -n 1 || true)"
fi

if [[ -z "$DB_FILE" ]]; then
  echo "Nisam pronasao SQL dump u backups/db."
  echo "Stavi .sql ili .sql.gz datoteku i pokreni skriptu ponovno."
  exit 1
fi

echo "Pokrecem bazu ako vec nije pokrenuta..."
docker compose up -d db_avc >/dev/null

echo "Cekam da MariaDB bude spremna..."
until docker compose exec -T db_avc mariadb-admin ping -h localhost -uroot "-p${DB_ROOT_PASSWORD}" --silent >/dev/null 2>&1; do
  sleep 2
done

echo "Uvozim bazu iz: $DB_FILE"
if [[ "$DB_FILE" == *.sql.gz ]]; then
  gzip -dc "$DB_FILE" | docker compose exec -T db_avc mariadb -uroot "-p${DB_ROOT_PASSWORD}" "$DB_NAME"
else
  cat "$DB_FILE" | docker compose exec -T db_avc mariadb -uroot "-p${DB_ROOT_PASSWORD}" "$DB_NAME"
fi

LOCAL_SITE_URL="${LOCAL_SITE_URL:-http://localhost:${WP_PORT:-8080}}"
WP_TABLE_PREFIX="${WP_TABLE_PREFIX:-wp_}"

echo "Postavljam lokalni site URL na: $LOCAL_SITE_URL"
docker compose exec -T db_avc mariadb -uroot "-p${DB_ROOT_PASSWORD}" "$DB_NAME" -e "
UPDATE \`${WP_TABLE_PREFIX}options\`
SET option_value='${LOCAL_SITE_URL}'
WHERE option_name IN ('siteurl', 'home');
"

echo "Import zavrsen."
echo "Ako lokalni URL ne radi odmah, sljedeci korak je search/replace produkcijskog domena."
