#!/usr/bin/env bash
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKUP_DIR="${1:-/var/backups/project2}"
RETENTION_DAYS="${2:-14}"

if [[ ! -d "$BACKUP_DIR" ]]; then
  echo "[WARN] Backup directory not found: $BACKUP_DIR"
  exit 0
fi

DELETED=$(find "$BACKUP_DIR" -type f -name '*.sql.gz' -mtime "+$RETENTION_DAYS" -print -delete | wc -l)
echo "[OK] Deleted $DELETED backup file(s) older than $RETENTION_DAYS day(s) from $BACKUP_DIR"
