#!/usr/bin/env bash
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$PROJECT_DIR/.env"
BACKUP_DIR="${1:-$PROJECT_DIR/storage/backups}"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "[ERROR] .env file not found: $ENV_FILE"
  exit 1
fi

mkdir -p "$BACKUP_DIR"

get_env() {
  local key="$1"
  grep -E "^${key}=" "$ENV_FILE" | tail -n 1 | cut -d '=' -f2- | sed 's/^"//; s/"$//'
}

DB_HOST="$(get_env DB_HOST)"
DB_PORT="$(get_env DB_PORT)"
DB_NAME="$(get_env DB_DATABASE)"
DB_USER="$(get_env DB_USERNAME)"
DB_PASS="$(get_env DB_PASSWORD)"

if [[ -z "$DB_NAME" || -z "$DB_USER" ]]; then
  echo "[ERROR] Missing DB settings in .env"
  exit 1
fi

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
OUT_FILE="$BACKUP_DIR/${DB_NAME}_${TIMESTAMP}.sql.gz"

export MYSQL_PWD="$DB_PASS"
mysqldump \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --user="$DB_USER" \
  --single-transaction \
  --quick \
  --routines \
  --triggers \
  "$DB_NAME" | gzip -9 > "$OUT_FILE"
unset MYSQL_PWD

chmod 600 "$OUT_FILE"

echo "[OK] Backup created: $OUT_FILE"
