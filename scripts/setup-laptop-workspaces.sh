#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PRODUCT_ROOT="${HOME}/Documents/product"
SKIP_AVC=0
SKIP_PRODUCT=0

usage() {
  cat <<EOF
Usage: $(basename "$0") [--product-root PATH] [--skip-avc] [--skip-product]

Starts Docker for the copied workspaces on the laptop and restores the latest
export-laptop-state SQL dump for each non-system database.

Options:
  --product-root PATH   Override the default product path (${HOME}/Documents/product)
  --skip-avc            Restore only product
  --skip-product        Restore only AVC
  -h, --help            Show this help
EOF
}

require_command() {
  local command_name="$1"
  if ! command -v "$command_name" >/dev/null 2>&1; then
    echo "Nedostaje naredba: $command_name"
    exit 1
  fi
}

validate_db_name() {
  local db_name="$1"
  if [[ ! "$db_name" =~ ^[A-Za-z0-9_]+$ ]]; then
    echo "Nepodrzano ime baze: $db_name"
    exit 1
  fi
}

latest_dump_dbs() {
  local dump_dir="$1"
  find "$dump_dir" -maxdepth 1 -type f -name '*-laptop-*.sql.gz' 2>/dev/null \
    | sed -E 's#^.*/([^/]+)-laptop-[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}-[0-9]{2}\.sql\.gz$#\1#' \
    | sort -u
}

latest_dump_for_db() {
  local dump_dir="$1"
  local db_name="$2"
  find "$dump_dir" -maxdepth 1 -type f -name "${db_name}-laptop-*.sql.gz" 2>/dev/null | sort | tail -n 1
}

load_avc_env() {
  if [[ -f "$ROOT_DIR/.env" ]]; then
    set -a
    # shellcheck disable=SC1091
    source "$ROOT_DIR/.env"
    set +a
  elif [[ -f "$ROOT_DIR/.env.example" ]]; then
    set -a
    # shellcheck disable=SC1091
    source "$ROOT_DIR/.env.example"
    set +a
  else
    echo "Nisam pronasao ni .env ni .env.example u AVC workspaceu."
    exit 1
  fi

  : "${DB_ROOT_PASSWORD:?DB_ROOT_PASSWORD nije postavljen}"
  : "${DB_NAME:?DB_NAME nije postavljen}"
  : "${PLATFORM_DB_NAME:?PLATFORM_DB_NAME nije postavljen}"
  : "${PLATFORM_DB_USER:?PLATFORM_DB_USER nije postavljen}"
  : "${PLATFORM_DB_PASSWORD:?PLATFORM_DB_PASSWORD nije postavljen}"
}

run_avc_compose() {
  (
    cd "$ROOT_DIR"
    docker compose "$@"
  )
}

docker_container_exists() {
  local container_name="$1"
  docker container inspect "$container_name" >/dev/null 2>&1
}

docker_container_running() {
  local container_name="$1"
  [[ "$(docker container inspect -f '{{.State.Running}}' "$container_name" 2>/dev/null || true)" == "true" ]]
}

wait_for_avc_db() {
  echo "Cekam AVC bazu..."
  until run_avc_compose exec -T db mariadb-admin ping -h 127.0.0.1 -uroot "-p${DB_ROOT_PASSWORD}" --silent >/dev/null 2>&1; do
    sleep 2
  done
}

recreate_avc_db() {
  local db_name="$1"
  validate_db_name "$db_name"
  run_avc_compose exec -T db mariadb -uroot "-p${DB_ROOT_PASSWORD}" -e "
    DROP DATABASE IF EXISTS \`${db_name}\`;
    CREATE DATABASE \`${db_name}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  "
}

import_avc_db() {
  local db_name="$1"
  local dump_file="$2"
  echo "Restore AVC baze: $db_name <- $dump_file"
  gzip -dc "$dump_file" | run_avc_compose exec -T db mariadb -uroot "-p${DB_ROOT_PASSWORD}" "$db_name"
}

ensure_avc_platform_user() {
  run_avc_compose exec -T db mariadb -uroot "-p${DB_ROOT_PASSWORD}" -e "
    CREATE USER IF NOT EXISTS '${PLATFORM_DB_USER}'@'%' IDENTIFIED BY '${PLATFORM_DB_PASSWORD}';
    GRANT ALL PRIVILEGES ON \`${PLATFORM_DB_NAME}\`.* TO '${PLATFORM_DB_USER}'@'%';
    FLUSH PRIVILEGES;
  "
}

update_avc_site_url() {
  local local_site_url="${LOCAL_SITE_URL:-http://localhost:${WP_PORT:-8080}}"
  local wp_table_prefix="${WP_TABLE_PREFIX:-wp_}"

  run_avc_compose exec -T db mariadb -uroot "-p${DB_ROOT_PASSWORD}" "$DB_NAME" -e "
    UPDATE \`${wp_table_prefix}options\`
    SET option_value='${local_site_url}'
    WHERE option_name IN ('siteurl', 'home');
  " >/dev/null

  echo "Postavljen AVC WordPress URL na: $local_site_url"
}

restore_avc() {
  local dump_dir="$ROOT_DIR/backups/db"
  local db_name
  local dump_file
  local restored_any=0

  if [[ ! -d "$dump_dir" ]]; then
    echo "AVC dump folder ne postoji: $dump_dir"
    exit 1
  fi

  echo "Pokrecem AVC stack..."
  run_avc_compose up -d --build
  wait_for_avc_db

  for db_name in $(latest_dump_dbs "$dump_dir"); do
    dump_file="$(latest_dump_for_db "$dump_dir" "$db_name")"
    [[ -z "$dump_file" ]] && continue
    recreate_avc_db "$db_name"
    import_avc_db "$db_name" "$dump_file"
    restored_any=1
  done

  if [[ "$restored_any" -eq 0 ]]; then
    echo "Nisam pronasao AVC dumpove u $dump_dir."
    exit 1
  fi

  ensure_avc_platform_user
  update_avc_site_url
}

run_product_compose() {
  (
    cd "$PRODUCT_ROOT"
    docker compose -f "$PRODUCT_COMPOSE_FILE" "$@"
  )
}

product_db_exec() {
  if [[ -n "${PRODUCT_DB_CONTAINER:-}" ]] && docker_container_exists "$PRODUCT_DB_CONTAINER"; then
    docker exec -i "$PRODUCT_DB_CONTAINER" "$@"
    return 0
  fi

  run_product_compose exec -T "$PRODUCT_DB_SERVICE" "$@"
}

ensure_product_stack_running() {
  local container_name
  local all_present=1
  local container_list=("$PRODUCT_DB_CONTAINER")

  if [[ "${#PRODUCT_APP_CONTAINERS[@]}" -gt 0 ]]; then
    container_list+=("${PRODUCT_APP_CONTAINERS[@]}")
  fi

  for container_name in "${container_list[@]}"; do
    if ! docker_container_exists "$container_name"; then
      all_present=0
      break
    fi
  done

  if [[ "$all_present" -eq 1 ]]; then
    for container_name in "${container_list[@]}"; do
      if ! docker_container_running "$container_name"; then
        docker start "$container_name" >/dev/null
      fi
    done
    return 0
  fi

  run_product_compose up -d --build
}

wait_for_product_db() {
  echo "Cekam product bazu..."
  until product_db_exec mariadb-admin ping -h 127.0.0.1 -uroot "-p${PRODUCT_DB_ROOT_PASSWORD}" --silent >/dev/null 2>&1; do
    sleep 2
  done
}

detect_product_compose() {
  if [[ -f "$PRODUCT_ROOT/docker-compose.fcc-new.yml" ]]; then
    PRODUCT_COMPOSE_FILE="$PRODUCT_ROOT/docker-compose.fcc-new.yml"
    PRODUCT_DB_SERVICE="db_fcc_new"
    PRODUCT_DB_ROOT_PASSWORD="root"
    PRODUCT_NEEDS_VOLUME=1
    PRODUCT_DB_CONTAINER="radna-verzija-db"
    PRODUCT_APP_CONTAINERS=("radna-verzija-app" "radna-verzija-cron")
    return 0
  fi

  if [[ -f "$PRODUCT_ROOT/docker-compose.yml" ]]; then
    PRODUCT_COMPOSE_FILE="$PRODUCT_ROOT/docker-compose.yml"
    PRODUCT_DB_SERVICE="db"
    PRODUCT_DB_ROOT_PASSWORD="root"
    PRODUCT_NEEDS_VOLUME=0
    PRODUCT_DB_CONTAINER="db"
    PRODUCT_APP_CONTAINERS=("app" "cron")
    return 0
  fi

  return 1
}

recreate_product_db() {
  local db_name="$1"
  validate_db_name "$db_name"
  product_db_exec mariadb -uroot "-p${PRODUCT_DB_ROOT_PASSWORD}" -e "
    DROP DATABASE IF EXISTS \`${db_name}\`;
    CREATE DATABASE \`${db_name}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  "
}

import_product_db() {
  local db_name="$1"
  local dump_file="$2"
  echo "Restore product baze: $db_name <- $dump_file"
  gzip -dc "$dump_file" | product_db_exec mariadb -uroot "-p${PRODUCT_DB_ROOT_PASSWORD}" "$db_name"
}

restore_product() {
  local dump_dir="$PRODUCT_ROOT/baze"
  local db_name
  local dump_file
  local restored_any=0

  if [[ ! -d "$PRODUCT_ROOT" ]]; then
    echo "Product folder ne postoji na: $PRODUCT_ROOT"
    exit 1
  fi

  if ! detect_product_compose; then
    echo "Nisam pronasao docker-compose za product u: $PRODUCT_ROOT"
    exit 1
  fi

  if [[ ! -d "$dump_dir" ]]; then
    echo "Product dump folder ne postoji: $dump_dir"
    exit 1
  fi

  if [[ "$PRODUCT_NEEDS_VOLUME" -eq 1 ]]; then
    docker volume create fcc-new_db_data_fcc_new >/dev/null
  fi

  echo "Pokrecem product stack..."
  ensure_product_stack_running
  wait_for_product_db

  for db_name in $(latest_dump_dbs "$dump_dir"); do
    dump_file="$(latest_dump_for_db "$dump_dir" "$db_name")"
    [[ -z "$dump_file" ]] && continue
    recreate_product_db "$db_name"
    import_product_db "$db_name" "$dump_file"
    restored_any=1
  done

  if [[ "$restored_any" -eq 0 ]]; then
    echo "Nisam pronasao product dumpove u $dump_dir."
    exit 1
  fi
}

while [[ $# -gt 0 ]]; do
  case "$1" in
    --product-root)
      shift
      if [[ $# -eq 0 ]]; then
        echo "Nedostaje vrijednost za --product-root"
        exit 1
      fi
      PRODUCT_ROOT="$1"
      ;;
    --skip-avc)
      SKIP_AVC=1
      ;;
    --skip-product)
      SKIP_PRODUCT=1
      ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "Nepoznata opcija: $1"
      usage
      exit 1
      ;;
  esac
  shift
done

require_command docker
require_command gzip

if [[ "$SKIP_AVC" -eq 0 ]]; then
  load_avc_env
  restore_avc
fi

if [[ "$SKIP_PRODUCT" -eq 0 ]]; then
  restore_product
fi

echo
echo "Restore zavrsen."
echo "Provjeri:"
echo "  - AVC WordPress: http://localhost:${WP_PORT:-8080}"
echo "  - AVC platform:  http://localhost:${PLATFORM_PORT:-8082}"
echo "  - Product:       http://localhost:8091"
