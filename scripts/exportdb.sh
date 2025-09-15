#!/bin/bash
# exportdb.sh — consistent dump, no events; strips DEFINER; injects DROP VIEW; timestamped
# Optionally produces a second dump rewritten to a TARGET_DB (for hosts like StackCP).

set -euo pipefail

# --- CONFIG ---
MYSQLDUMP="/opt/lampp/bin/mysqldump"
MYSQL="/opt/lampp/bin/mysql"
USER="root"
HOST="localhost"
DB="dev-gp"
OUTDIR="/opt/lampp/htdocs/webroot/gp/gp2025/sql"

# If you want a copy tailored for a hosting DB, set TARGET_DB (e.g. TARGET_DB="gplan8-35303737a645").
# Leave empty to skip generating the mapped file.
TARGET_DB="plan8-35303737a645"
# ---------------

mkdir -p "$OUTDIR"
STAMP=$(date +"%Y-%m-%d_%H-%M-%S")
OUTFILE="$OUTDIR/${DB}_${STAMP}.sql"

# Prompt for password securely
read -s -p "Enter MySQL password for $USER@$HOST: " PASS; echo
echo "Exporting database '$DB' (consistent snapshot, no events, no definers)..."

# Detect mysqldump capabilities
HELP="$($MYSQLDUMP --help 2>/dev/null || true)"
ADD_DROP_VIEW_SUPPORTED=0
NO_DEFINER_SUPPORTED=0
SKIP_DEFINER_SUPPORTED=0
grep -q -- "--add-drop-view" <<<"$HELP" && ADD_DROP_VIEW_SUPPORTED=1 || true
grep -q -- "--no-definer"    <<<"$HELP" && NO_DEFINER_SUPPORTED=1 || true
grep -q -- "--skip-definer"  <<<"$HELP" && SKIP_DEFINER_SUPPORTED=1 || true

# Probe routines metadata to avoid 1558 errors on older MariaDB upgrades
INCLUDE_ROUTINES=1
ROUTINE_ERR="$($MYSQL -u "$USER" -p"$PASS" -h "$HOST" -N -e "SHOW FUNCTION STATUS WHERE Db = '$DB';" 2>&1 >/dev/null || true)"
if [[ "$ROUTINE_ERR" == *"Column count of mysql.proc is wrong"* ]] || [[ "$ROUTINE_ERR" == *"ERROR"* ]]; then
  INCLUDE_ROUTINES=0
  echo "Note: Skipping ROUTINES export (server routines metadata not compatible)."
fi

# Build dump options (no --events and no --databases; dumps a single DB)
OPTS=(
  -u "$USER" -p"$PASS" -h "$HOST"
  --add-drop-table
  --single-transaction
  --quick
  --lock-tables=false
  --triggers
)

[[ $INCLUDE_ROUTINES -eq 1 ]] && OPTS+=( --routines )

# Prefer native definer stripping if supported
if [[ $NO_DEFINER_SUPPORTED -eq 1 ]]; then
  OPTS+=( --no-definer )
elif [[ $SKIP_DEFINER_SUPPORTED -eq 1 ]]; then
  OPTS+=( --skip-definer )
fi

TMP="$(mktemp)"
trap 'rm -f "$TMP" "$TMP.withdrops" "$TMP.mapped"' EXIT

# Dump a single DB (positional arg), so no CREATE DATABASE/USE in output
if ! "$MYSQLDUMP" "${OPTS[@]}" "$DB" > "$TMP"; then
  echo "❌ mysqldump failed." >&2
  exit 1
fi

# If no native definer-stripping, remove DEFINER & SQL SECURITY DEFINER
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
      print "DROP VIEW IF EXISTS `" m[1] "`;"
    }
    print $0
  }' "$TMP" > "$TMP.withdrops" && mv "$TMP.withdrops" "$TMP"
fi

# Move the base dump into place
mv "$TMP" "$OUTFILE"
trap - EXIT

echo "✅ Exported: $OUTFILE"

# If TARGET_DB is set and differs from DB, produce a mapped copy for hosting import
if [[ -n "${TARGET_DB}" && "${TARGET_DB}" != "${DB}" ]]; then
  OUTFILE_TARGET="$OUTDIR/${DB}_${STAMP}__target-${TARGET_DB}.sql"
  cp "$OUTFILE" "$OUTFILE_TARGET"

  # Sanitize for target: drop CREATE/USE (defensive), rewrite qualifiers `DB`. to `TARGET_DB`.
  sed -E -i \
    -e '/^CREATE DATABASE[[:space:]]/Id' \
    -e "s/^USE[[:space:]]+\`[^`]+\`;/USE \`${TARGET_DB}\`;/I" \
    -e "s/\\`${DB}\\`\\./\`${TARGET_DB}\`./g" \
    "$OUTFILE_TARGET"

  echo "🎯 Mapped copy for target DB '${TARGET_DB}': $OUTFILE_TARGET"
  echo "Import with:"
  echo "    $MYSQL -h<host> -u<user> -p --database='${TARGET_DB}' < \"$OUTFILE_TARGET\""
else
  echo "Import into any target DB with:"
  echo "    $MYSQL -u root -p <target_db> < \"$OUTFILE\""
fi
