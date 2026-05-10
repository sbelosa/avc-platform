#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

BACKUP_FILE="${1:-}"

if [[ -z "$BACKUP_FILE" ]]; then
  BACKUP_FILE="$(find "$ROOT_DIR/backups/site" -maxdepth 1 -type f -name "*.tar" | sort | head -n 1 || true)"
fi

if [[ -z "$BACKUP_FILE" ]]; then
  echo "Nisam pronasao Backuply .tar u backups/site."
  exit 1
fi

mkdir -p "$ROOT_DIR/local/wordpress" "$ROOT_DIR/backups/db"

echo "Izvlacim WordPress datoteke iz:"
echo "  $BACKUP_FILE"

tar \
  --exclude='./public_html/wp-config.php' \
  --strip-components=2 \
  -xf "$BACKUP_FILE" \
  -C "$ROOT_DIR/local/wordpress" \
  ./public_html

DB_ENTRY="$(tar -tf "$BACKUP_FILE" | rg '^\./backuply-data/mysql/.+\.sql(\.gz)?$' | head -n 1 || true)"

if [[ -z "$DB_ENTRY" ]]; then
  echo "Nisam pronasao SQL dump unutar Backuply backupa."
  exit 1
fi

DB_BASENAME="$(basename "$DB_ENTRY")"

echo "Izvlacim bazu:"
echo "  $DB_ENTRY -> backups/db/$DB_BASENAME"

tar -xOf "$BACKUP_FILE" "$DB_ENTRY" > "$ROOT_DIR/backups/db/$DB_BASENAME"

echo
echo "Priprema zavrsena."
echo "Sljedeci koraci:"
echo "  1. docker compose up -d"
echo "  2. ./scripts/import-db.sh"
