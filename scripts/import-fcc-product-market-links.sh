#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

EXPORT_DIR="$ROOT_DIR/exports/fcc"
EXPORT_FILE="$EXPORT_DIR/product_market_links.tsv"

mkdir -p "$EXPORT_DIR"

echo "Exporting FCC product market links..."
docker exec radna-verzija-db mariadb -uroot -proot app -B -N -e "
SELECT
  COALESCE(url, ''),
  COALESCE(language, ''),
  COALESCE(title, ''),
  COALESCE(sku, ''),
  REPLACE(REPLACE(TO_BASE64(webshop_links), '\n', ''), '\r', '')
FROM blog_posts
WHERE webshop_links IS NOT NULL
  AND webshop_links != ''
ORDER BY url ASC, language ASC;
" > "$EXPORT_FILE"

echo "Importing FCC product market links into AVC platform..."
docker exec avc-platform php /var/www/html/scripts/import_fcc_product_market_links.php /var/www/html/exports/fcc/product_market_links.tsv
