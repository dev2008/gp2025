#!/bin/bash
# exportdb.sh — consistent dump, no events, no definers, timestamped, drop table/view

set -euo pipefail

# --- CONFIG ---
MYSQLDUMP="/opt/lampp/bin/mysqldump"
USER="root"
HOST="localhost"
DB="dev-gp"
OUTDIR="/opt/lampp/htdocs/webroot/gp/gp2025/sql"
# ---------------

mkdir -p "$OUTDIR"
STAMP=$(date +"%Y-%m-%d_%H-%M-%S")
OUTFILE="$OUTDIR/${DB}_${STAMP}.sql"

read -s -p "Enter MySQL password for $USER@$HOST: " PASS; echo
echo "Exporting database '$DB' (consistent snapshot, no events, no definers)..."

# Detect supported mysqldump flags
HELP="$($MYSQLDUMP --help 2>/dev/null || true)"
ADD_DROP_VIEW_SUPPORTED=0
NO_DEFINER_SUPPORTED=0
SKIP_DEFINER_SUPPORTED=0
grep -q -- "--add-drop-view" <<<"$HELP" && ADD_DROP_VIEW_SUPPORTED=1
grep -q -- "--no-definer"    <<<"$HELP" && NO_DEFINER_SUPPORTED=1
grep -q -- "--skip-definer"  <<<"$HELP" && SKIP_DEFINER_SUPPORTED=1

# Build options (NOTE: no --events, and no --databases)
OPTS=(
  -u "$USER" -p"$PASS" -h "$HOST"
  --add-drop-table
  --single-transaction
  --quick
  --lock-tables=false
  --routines
  --triggers
)

# Prefer native definer stripping if supported
if [[ $NO_DEFINER_SUPPORTED -eq 1 ]]; then
  OPTS+=( --no-definer )
elif [[ $SKIP_DEFINER_SUPPORTED -eq 1 ]]; then
  OPTS+=( --skip-definer )
fi

TMP="$(mktemp)"
trap 'rm -f "$TMP" "$TMP.withdrops"' EXIT

# Dump a single DB (positional arg), so no CREATE DATABASE/USE in output
if ! "$MYSQLDUMP" "${OPTS[@]}" "$DB" > "$TMP"; then
  echo "❌ mysqldump failed." >&2
  exit 1
fi

# If no native definer stripping, remove DEFINER & SQL SECURITY DEFINER
if [[ $NO_DEFINER_SUPPORTED -eq 0 && $SKIP_DEFINER_SUPPORTED -eq 0 ]]; then
  sed -E -i \
    -e 's/DEFINER=`[^`]*`@`[^`]*`[[:space:]]*//gI' \
    -e 's/DEFINER=[^ ]+[[:space:]]*//gI' \
    -e 's/SQL[[:space:]]+SECURITY[[:space:]]+DEFINER//gI' \
    "$TMP"
fi

# If --add-drop-view isn’t supported, inject DROP VIEW before each CREATE VIEW
if [[ $ADD_DROP_VIEW_SUPPORTED -eq 0 ]]; then
  awk '{
    if (match($0, /^CREATE[[:space:]].*VIEW[[:space:]]+`([^`]+)`/, m)) {
      print "DROP VIEW IF EXISTS `" m[1] "`;";
    }
    print $0;
  }' "$TMP" > "$TMP.withdrops" && mv "$TMP.withdrops" "$TMP"
fi

mv "$TMP" "$OUTFILE"
trap - EXIT

echo "✅ Exported to: $OUTFILE"
echo "Import into any target DB (e.g. pre-gp) with:"
echo "    /opt/lampp/bin/mysql -u root -p pre-gp < \"$OUTFILE\""
