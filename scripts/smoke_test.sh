#!/usr/bin/env bash
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$PROJECT_DIR/.env"

get_env() {
  local key="$1"
  grep -E "^${key}=" "$ENV_FILE" | tail -n 1 | cut -d '=' -f2- | sed 's/^"//; s/"$//'
}

BASE_URL="${1:-$(get_env APP_URL)}"
if [[ -z "$BASE_URL" ]]; then
  echo "[ERROR] APP_URL is empty"
  exit 1
fi

CURL_OPTS=(-k -sS -o /dev/null -w "%{http_code}")

resolve_base_url() {
  local url="$1"
  local code
  code="$(curl "${CURL_OPTS[@]}" "$url/health" 2>/dev/null || true)"
  if [[ "$code" == "200" ]]; then
    echo "$url"
    return
  fi

  if [[ "$url" =~ ^https:// ]]; then
    local http_url
    http_url="${url/https:\/\//http://}"
    code="$(curl "${CURL_OPTS[@]}" "$http_url/health" 2>/dev/null || true)"
    if [[ "$code" == "200" ]]; then
      echo "$http_url"
      return
    fi
  fi

  echo "$url"
}

BASE_URL="$(resolve_base_url "$BASE_URL")"

check_code() {
  local path="$1"
  local expected_regex="$2"
  local code
  code="$(curl "${CURL_OPTS[@]}" "$BASE_URL$path")"
  if [[ ! "$code" =~ $expected_regex ]]; then
    echo "[FAIL] $path returned $code (expected: $expected_regex)"
    exit 1
  fi
  echo "[OK] $path -> $code"
}

echo "Running smoke test against: $BASE_URL"
check_code "/health" "^200$"
check_code "/" "^(200|301|302)$"
check_code "/admin/login" "^200$"
check_code "/admin/dashboard" "^(302|401|403)$"
check_code "/admin/reports" "^(302|401|403)$"

echo "[OK] Smoke test passed"
