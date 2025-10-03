#!/bin/bash
# exportdb.sh — consistent dump, no definers, timestamped, drop view, de-qualify schema
# Usage examples:
#   DB=dev-gp DB_USER=pma DB_HOST=localhost ./exportdb.sh   # will prompt for password
#   DB=dev-gp DB_USER=pma DB_PASS=secret DB_HOST=localhost ./exportdb.sh
#   LOGIN_PATH=prod-db DB=pre-gp ./exportdb.sh
set -euo pipefail

# --- CONFIG (override with env vars) ---
MYSQLDUMP="${MYSQLDUMP:-mysqldump}"
DB="${DB:-dev-gp}"
OUTDIR="${OUTDIR:-/var/www/app84/public/gp2025/sql}"

# Credentials (priority: LOGIN_PATH > explicit env vars > client defaults file)
LOGIN_PATH="${LOGIN_PATH:-}"
DB_USER="${DB_USER:-${MYSQL_USER:-}}"
DB_PASS="${DB_PASS:-${DB_PASSWORD:-${MYSQL_PWD:-}}}"
DB_HOST="${DB_HOST:-localhost}"
DB_PORT="${DB_PORT:-}"
# --------------------------------------

mkdir -p "$OUTDIR"
STAMP=$(date +"%Y-%m-%d_%H-%M-%S")
OUTFILE="$OUTDIR/${DB}_${STAMP}.sql"

# temp file
TMP="$(mktemp -t exportdb_${DB}.XXXXXX.sql)"
trap 'rm -f "$TMP"' EXIT

# Detect mysqldump feature flags
HELP="$("$MYSQLDUMP" --help 2>&1 || true)"
have_flag() { echo "$HELP" | grep -q -- " $1"; }

NO_DEFINER_SUPPORTED=0; have_flag --no-definer && NO_DEFINER_SUPPORTED=1
SKIP_DEFINER_SUPPORTED=0; have_flag --skip-definer && SKIP_DEFINER_SUPPORTED=1
ADD_DROP_VIEW_SUPPORTED=0; have_flag --add-drop-view && ADD_DROP_VIEW_SUPPORTED=1
SET_GTID_PURGED_SUPPORTED=0; have_flag --set-gtid-purged && SET_GTID_PURGED_SUPPORTED=1
COLUMN_STATS_SUPPORTED=0; have_flag --column-statistics && COLUMN_STATS_SUPPORTED=1

# Base options
OPTS=(
  --single-transaction
  --quick
  --routines
  --triggers
  --add-drop-table
  --default-character-set=utf8mb4
)

# Credentials handling
if [[ -n "$LOGIN_PATH" ]]; then
  OPTS+=(--login-path="$LOGIN_PATH")
else
  [[ -n "$DB_USER" ]] && OPTS+=(--user="$DB_USER")
  [[ -n "$DB_HOST" ]] && OPTS+=(--host="$DB_HOST")
  [[ -n "$DB_PORT" ]] && OPTS+=(--port="$DB_PORT")
  if [[ -z "${DB_PASS}" && -z "${MYSQL_PWD:-}" ]]; then
    # Force interactive prompt on ALL builds
    OPTS+=(-p)
  fi
fi

# Prefer native definer stripping if available
if [[ $NO_DEFINER_SUPPORTED -eq 1 ]]; then
  OPTS+=(--no-definer)
elif [[ $SKIP_DEFINER_SUPPORTED -eq 1 ]]; then
  OPTS+=(--skip-definer)
fi

[[ $ADD_DROP_VIEW_SUPPORTED -eq 1 ]] && OPTS+=(--add-drop-view)
[[ $SET_GTID_PURGED_SUPPORTED -eq 1 ]] && OPTS+=(--set-gtid-purged=OFF)
[[ $COLUMN_STATS_SUPPORTED -eq 1 ]] && OPTS+=(--column-statistics=0)

# --- Run dump (use MYSQL_PWD only if DB_PASS provided) ---
if [[ -n "$DB_PASS" && -z "${LOGIN_PATH}" ]]; then
  MYSQL_PWD="$DB_PASS" "$MYSQLDUMP" "${OPTS[@]}" "$DB" > "$TMP"
else
  "$MYSQLDUMP" "${OPTS[@]}" "$DB" > "$TMP"
fi

# --- Portable in-place sed (GNU/Linux & BSD/macOS) ---
sed_inplace() {
  if sed --version >/dev/null 2>&1; then
    sed -E -i "$@"
  else
    local last_arg="${@: -1}"
    sed -E -i '' "${@:1:$(($#-1))}" "$last_arg"
  fi
}

# --- Remove schema qualifiers like `dev-gp`.`table` ---
ESCDB=$(printf '%s\n' "$DB" | sed 's/[][\/.^$*+?|(){}-]/\\&/g')
sed_inplace "s/\`$ESCDB\`\.\`([^\`]+)\`/\`\1\`/g" "$TMP"
sed_inplace "s/\\b$ESCDB\\.([A-Za-z0-9_]+)/\\1/g" "$TMP"

# --- Strip DEFINER & 'SQL SECURITY DEFINER' if not handled natively ---
if [[ $NO_DEFINER_SUPPORTED -eq 0 && $SKIP_DEFINER_SUPPORTED -eq 0 ]]; then
  sed_inplace \
    -e 's/DEFINER=`[^`]*`@`[^`]*`[[:space:]]*//gI' \
    -e 's/DEFINER=[^ ]+[[:space:]]*//gI' \
    -e 's/SQL[[:space:]]+SECURITY[[:space:]]+DEFINER//gI' \
    "$TMP"
fi

# --- Inject DROP VIEW IF EXISTS if needed ---
if [[ $ADD_DROP_VIEW_SUPPORTED -eq 0 ]]; then
  awk '{
    if (match($0, /^CREATE[[:space:]].*VIEW[[:space:]]+`([^`]+)`/, m)) {
      print "DROP VIEW IF EXISTS `" m[1] "`;"
    }
    print $0;
  }' "$TMP" > "$TMP.withdrops" && mv "$TMP.withdrops" "$TMP"
fi

# Finalize
mv "$TMP" "$OUTFILE"
trap - EXIT

echo "✅ Exported to: $OUTFILE"
echo "Import into any target DB (e.g. pre-gp) with:"
echo "    mysql -u root -p pre-gp < \"$OUTFILE\""
