#!/usr/bin/env bash
set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$PROJECT_DIR/.env"
HEALTH_URL="${1:-http://127.0.0.1:8080/health/deep}"
STATE_FILE="${2:-/tmp/project2_health_last_status.txt}"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "[ERROR] .env not found: $ENV_FILE"
  exit 1
fi

get_env() {
  local key="$1"
  grep -E "^${key}=" "$ENV_FILE" | tail -n 1 | cut -d '=' -f2- | sed 's/^"//; s/"$//'
}

LINE_TOKEN="$(get_env LINE_CHANNEL_ACCESS_TOKEN)"
LINE_USER_ID="$(get_env LINE_ADMIN_USER_ID)"

if [[ -z "$LINE_TOKEN" || -z "$LINE_USER_ID" ]]; then
  echo "[WARN] LINE token/user not configured; skipping notify"
  exit 0
fi

RESP="$(curl -k -sS "$HEALTH_URL")"
STATUS="$(php -r '$j=json_decode(stream_get_contents(STDIN),true); echo $j["status"] ?? "unknown";' <<< "$RESP")"
TS="$(date '+%Y-%m-%d %H:%M:%S')"

if [[ "$STATUS" == "ok" ]]; then
  echo "ok" > "$STATE_FILE"
  echo "[OK] health status is ok"
  exit 0
fi

LAST=""
if [[ -f "$STATE_FILE" ]]; then
  LAST="$(cat "$STATE_FILE" 2>/dev/null || true)"
fi

# Send only when state changes to degraded to avoid noise
if [[ "$LAST" == "degraded" ]]; then
  echo "[INFO] already degraded; no duplicate notify"
  exit 0
fi

DETAIL="$(php -r '
$j=json_decode(stream_get_contents(STDIN),true);
$checks=$j["checks"]??[];
$out=[];
foreach($checks as $k=>$v){
  if(!(($v["ok"]??false)===true)){
    $out[]=$k.":".($v["message"]??"failed");
  }
}
echo implode(", ",$out);
' <<< "$RESP")"

MSG="[ALERT] Project2 health degraded\nTime: $TS\nStatus: $STATUS\nIssue: ${DETAIL:-unknown}"

curl -sS -X POST "https://api.line.me/v2/bot/message/push" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $LINE_TOKEN" \
  -d "{\"to\":\"$LINE_USER_ID\",\"messages\":[{\"type\":\"text\",\"text\":\"$MSG\"}]}" >/dev/null

echo "degraded" > "$STATE_FILE"
echo "[ALERT] notified LINE admin"
