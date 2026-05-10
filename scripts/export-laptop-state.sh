#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PRODUCT_ROOT="${HOME}/Documents/product"
SKIP_PRODUCT=0

usage() {
  cat <<EOF
Usage: $(basename "$0") [--product-root PATH] [--skip-product]

Creates fresh SQL dumps for the AVC workspace and, if present, the product
workspace so you can copy both projects to another machine and restore them.

Options:
  --product-root PATH   Override the default product path (${HOME}/Documents/product)
  --skip-product        Export only AVC
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
  until run_avc_compose exec -T db mariadb-admin ping -h localhost -uroot "-p${DB_ROOT_PASSWORD}" --silent >/dev/null 2>&1; do
    sleep 2
  done
}

list_avc_databases() {
  run_avc_compose exec -T db mariadb -uroot "-p${DB_ROOT_PASSWORD}" -N -e "
    SELECT schema_name
    FROM information_schema.schemata
    WHERE schema_name NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
    ORDER BY schema_name;
  "
}

run_product_compose() {
  (
    cd "$PRODUCT_ROOT"
    docker compose -f "$PRODUCT_COMPOSE_FILE" "$@"
  )
}

ensure_product_db_running() {
  if [[ -n "${PRODUCT_DB_CONTAINER:-}" ]] && docker_container_exists "$PRODUCT_DB_CONTAINER"; then
    if ! docker_container_running "$PRODUCT_DB_CONTAINER"; then
      echo "Pokrecem postojeci product DB container: $PRODUCT_DB_CONTAINER"
      docker start "$PRODUCT_DB_CONTAINER" >/dev/null
    fi
    return 0
  fi

  run_product_compose up -d "$PRODUCT_DB_SERVICE" >/dev/null
}

wait_for_product_db() {
  echo "Cekam product bazu..."
  until product_db_exec mariadb-admin ping -h localhost -uroot "-p${PRODUCT_DB_ROOT_PASSWORD}" --silent >/dev/null 2>&1; do
    sleep 2
  done
}

list_product_databases() {
  product_db_exec mariadb -uroot "-p${PRODUCT_DB_ROOT_PASSWORD}" -N -e "
    SELECT schema_name
    FROM information_schema.schemata
    WHERE schema_name NOT IN ('information_schema', 'mysql', 'performance_schema', 'sys')
    ORDER BY schema_name;
  "
}

product_db_exec() {
  if [[ -n "${PRODUCT_DB_CONTAINER:-}" ]] && docker_container_exists "$PRODUCT_DB_CONTAINER"; then
    docker exec -i "$PRODUCT_DB_CONTAINER" "$@"
    return 0
  fi

  run_product_compose exec -T "$PRODUCT_DB_SERVICE" "$@"
}

detect_product_compose() {
  if [[ -f "$PRODUCT_ROOT/docker-compose.fcc-new.yml" ]]; then
    PRODUCT_COMPOSE_FILE="$PRODUCT_ROOT/docker-compose.fcc-new.yml"
    PRODUCT_DB_SERVICE="db_fcc_new"
    PRODUCT_DB_ROOT_PASSWORD="root"
    PRODUCT_DB_CONTAINER="radna-verzija-db"
    return 0
  fi

  if [[ -f "$PRODUCT_ROOT/docker-compose.yml" ]]; then
    PRODUCT_COMPOSE_FILE="$PRODUCT_ROOT/docker-compose.yml"
    PRODUCT_DB_SERVICE="db"
    PRODUCT_DB_ROOT_PASSWORD="root"
    PRODUCT_DB_CONTAINER="db"
    return 0
  fi

  return 1
}

dump_avc_databases() {
  local timestamp="$1"
  local db_name
  local output_file

  mkdir -p "$ROOT_DIR/backups/db" "$ROOT_DIR/exports"

  echo "Pokrecem AVC bazu..."
  run_avc_compose up -d db >/dev/null
  wait_for_avc_db

  for db_name in $(list_avc_databases); do
    output_file="$ROOT_DIR/backups/db/${db_name}-laptop-${timestamp}.sql.gz"
    echo "Export AVC baze: $db_name -> $output_file"
    run_avc_compose exec -T db mariadb-dump -uroot "-p${DB_ROOT_PASSWORD}" --single-transaction --quick "$db_name" </dev/null | gzip > "$output_file"
    AVC_DUMPS+=("$output_file")
  done
}

dump_product_databases() {
  local timestamp="$1"
  local db_name
  local output_file

  if [[ "$SKIP_PRODUCT" -eq 1 ]]; then
    return 0
  fi

  if [[ ! -d "$PRODUCT_ROOT" ]]; then
    echo "Product folder ne postoji na: $PRODUCT_ROOT"
    echo "Preskacem product export."
    return 0
  fi

  if ! detect_product_compose; then
    echo "Nisam pronasao docker-compose za product u: $PRODUCT_ROOT"
    echo "Preskacem product export."
    return 0
  fi

  mkdir -p "$PRODUCT_ROOT/baze"

  echo "Pokrecem product bazu..."
  ensure_product_db_running
  wait_for_product_db

  for db_name in $(list_product_databases); do
    output_file="$PRODUCT_ROOT/baze/${db_name}-laptop-${timestamp}.sql.gz"
    echo "Export product baze: $db_name -> $output_file"
    product_db_exec mariadb-dump -uroot "-p${PRODUCT_DB_ROOT_PASSWORD}" --single-transaction --quick "$db_name" </dev/null | gzip > "$output_file"
    PRODUCT_DUMPS+=("$output_file")
  done
}

write_manifest() {
  local timestamp="$1"
  local manifest_file="$ROOT_DIR/exports/laptop-transfer-${timestamp}.txt"
  local item

  {
    echo "Laptop transfer manifest"
    echo "Created: $timestamp"
    echo
    echo "Copy these folders to the laptop:"
    echo "  - $ROOT_DIR"
    if [[ ${#PRODUCT_DUMPS[@]} -gt 0 || -d "$PRODUCT_ROOT" ]]; then
      echo "  - $PRODUCT_ROOT"
    fi
    echo
    echo "AVC dumps:"
    if [[ ${#AVC_DUMPS[@]} -eq 0 ]]; then
      echo "  - none"
    else
      for item in "${AVC_DUMPS[@]}"; do
        echo "  - $item"
      done
    fi
    echo
    echo "Product dumps:"
    if [[ ${#PRODUCT_DUMPS[@]} -eq 0 ]]; then
      echo "  - none"
    else
      for item in "${PRODUCT_DUMPS[@]}"; do
        echo "  - $item"
      done
    fi
    echo
    echo "Laptop restore command:"
    echo "  cd \"$ROOT_DIR\""
    echo "  ./scripts/setup-laptop-workspaces.sh --product-root \"$PRODUCT_ROOT\""
    echo
    echo "Optional Codex copy:"
    echo "  - ~/.codex/config.toml"
    echo "  - ~/.codex/skills/"
    echo "  - ~/.codex/plugins/"
    echo "  - ~/.codex/memories/"
  } > "$manifest_file"

  echo "Manifest spremljen u: $manifest_file"
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
require_command date

load_avc_env

TIMESTAMP="$(date '+%Y-%m-%d_%H-%M-%S')"
AVC_DUMPS=()
PRODUCT_DUMPS=()

dump_avc_databases "$TIMESTAMP"
dump_product_databases "$TIMESTAMP"
write_manifest "$TIMESTAMP"

echo
echo "Export zavrsen."
echo "Sad kopiraj AVC i product foldere na laptop, pa tamo pokreni:"
echo "  ./scripts/setup-laptop-workspaces.sh --product-root \"$PRODUCT_ROOT\""
