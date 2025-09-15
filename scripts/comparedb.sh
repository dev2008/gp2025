#!/bin/bash
# comparedb.sh — print diffs, otherwise say all good
set -euo pipefail

MYSQL="/opt/lampp/bin/mysql"
USER="root"
HOST="localhost"
SRC_DB="dev-gp"
DST_DB="pre-gp"

read -s -p "Enter MySQL password for $USER@$HOST: " PASS; echo

tables="$($MYSQL -u "$USER" -p"$PASS" -h "$HOST" -N -e "SHOW TABLES FROM \`$SRC_DB\`")"

total=0
diffs=0

while IFS= read -r T; do
  [[ -z "$T" ]] && continue
  ((total++))
  DEV=$($MYSQL -u "$USER" -p"$PASS" -h "$HOST" -N -e "SELECT COUNT(*) FROM \`$SRC_DB\`.\`$T\`;")
  PRE=$($MYSQL -u "$USER" -p"$PASS" -h "$HOST" -N -e "SELECT COUNT(*) FROM \`$DST_DB\`.\`$T\`;")
  if [[ "$DEV" != "$PRE" ]]; then
    printf "%-40s dev=%-10s pre=%-10s\n" "$T" "$DEV" "$PRE"
    ((diffs++))
  fi
done <<< "$tables"

if (( diffs == 0 )); then
  echo "✅ All $total table row counts match between $SRC_DB and $DST_DB."
else
  echo "❌ $diffs of $total tables differ."
  exit 1
fi
