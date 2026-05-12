#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLATFORM_DIR="${ROOT_DIR}/platform"
RELEASE_DIR="${ROOT_DIR}/releases"
TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
STAGING_DIR="${RELEASE_DIR}/staging-${TIMESTAMP}"
PACKAGE_PATH="${RELEASE_DIR}/avc-platform-production-${TIMESTAMP}.tar.gz"

mkdir -p "${RELEASE_DIR}"
rm -rf "${STAGING_DIR}"
mkdir -p "${STAGING_DIR}/avc-platform" "${STAGING_DIR}/database" "${STAGING_DIR}/docs"

SQL_BACKUP="${1:-}"
if [[ -z "${SQL_BACKUP}" ]]; then
  SQL_BACKUP="$(find "${PLATFORM_DIR}/storage/backups" -maxdepth 1 -type f -name 'avc_platform_ready_for_live_*.sql' | sort | tail -n 1)"
fi

if [[ -z "${SQL_BACKUP}" || ! -f "${SQL_BACKUP}" ]]; then
  echo "Nedostaje SQL backup. Pokreni prvo production readiness backup ili proslijedi putanju do .sql datoteke." >&2
  exit 1
fi

rsync -a \
  --exclude '.env' \
  --exclude '.env.local' \
  --exclude '.env.production' \
  --exclude '.DS_Store' \
  --exclude 'storage/backups/*.sql' \
  --exclude 'storage/cache/*' \
  --exclude 'storage/imports/*' \
  --exclude 'storage/logs/*' \
  --exclude 'storage/reports/*' \
  "${PLATFORM_DIR}/" "${STAGING_DIR}/avc-platform/"

cp "${SQL_BACKUP}" "${STAGING_DIR}/database/avc_platform_ready_for_live.sql"

cat > "${STAGING_DIR}/docs/PRODUCTION_DEPLOYMENT_CHECKLIST.txt" <<'CHECKLIST'
AVC production deployment checklist

1. Backup current production before any switch:
   - archive current public_html
   - export current WordPress database
   - keep public_html/wp-content/uploads because the new platform still serves legacy media paths

2. Create/import the new database:
   - import database/avc_platform_ready_for_live.sql into the production database
   - create a dedicated DB user with least required privileges

3. Configure avc-platform/.env.production:
   - copy avc-platform/.env.production.example to avc-platform/.env.production
   - fill DB credentials
   - fill mail settings
   - keep AVC_BASE_URL=https://aloevera-centar.com
   - keep AVC_ADMIN_NOTIFICATION_EMAIL=belosa.flp@gmail.com
   - keep AVC_ACTIVE_FOREVER_ID=360000760944

4. Web root:
   - preferred: point the domain document root to avc-platform/public
   - if hosting cannot change document root, create a safe front-controller layout manually and keep app files outside the public web root

5. Media:
   - move or rsync old public_html/wp-content/uploads to avc-platform/public/wp-content/uploads
   - verify at least one existing image URL under /wp-content/uploads opens after the switch

6. Activate:
   - switch document root or replace public_html only after backup is complete
   - clear LiteSpeed/cache
   - verify homepage, sitemap.xml, robots.txt, one article, one product, discount lead email, and admin login

Rollback:
   - restore old public_html archive
   - restore old WordPress database if needed
   - clear cache
CHECKLIST

(
  cd "${STAGING_DIR}"
  tar -czf "${PACKAGE_PATH}" .
)

rm -rf "${STAGING_DIR}"

echo "${PACKAGE_PATH}"
