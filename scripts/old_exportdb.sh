#!/bin/bash
# exportdb.sh — consistent dump, no events, no definers, timestamped, drop table/view, de-qualify schema
# Usage: adjust CONFIG below or override via env: DB=dev-gp OUTDIR=... ./exportdb.sh
set -euo pipefail

# --- CONFIG (override with env vars) ---
MYSQLDUMP="${MYSQLDUMP:-mysqldump}"
USER="${USER:-pma}"
HOST="${HOST:-localhost}"
DB="${DB:-dev-gp}"
OUTDIR="${OUTDIR:-/var/www/app84/public/gp2025/sql}"
# --------------------------------------

mkdir -p "$OUTDIR"
STAMP=$(date +"%Y-%m-%d_%H-%M-%S")
OUTFILE="$OUTDIR/${DB}_${STAMP}.sql"

# temp file
TMP="$(mktemp -t exportdb_${DB}.XXXXXX.sql)"
trap 'rm -f "$TMP"' EXIT

# Detect mysqldump feature flags (MariaDB vs MySQL variations)
HELP="$("$MYSQLDUMP" --help 2>&1 || true)"

NO_DEFINER_SUPPORTED=0
if echo "$HELP" | grep -q -- "--no-definer"; then
  NO_DEFINER_SUPPORTED=1
fi

SKIP_DEFINER_SUPPORTED=0
if echo "$HELP" | grep -q -- "--skip-definer"; then
  SKIP_DEFINER_SUPPORTED=1
fi

ADD_DROP_VIEW_SUPPORTED=0
if echo "$HELP" | grep -q -- "--add-drop-view"; then
  ADD_DROP_VIEW_SUPPORTED=1
fi

# Common mysqldump options
# - single transaction (InnoDB), quick streaming, add-drop-table for easy re-imports
# - routines & triggers (no events unless explicitly asked), consistent GTID handling
OPTS=(
  --user="$USER"
  --host="$HOST"
  --single-transaction
  --quick
  --routines
  --triggers
  --add-drop-table
  --set-gtid-purged=OFF
  --default-character-set=utf8mb4
)

# Prefer native definer stripping if available
if [[ $NO_DEFINER_SUPPORTED -eq 1 ]]; then
  OPTS+=(--no-definer)
elif [[ $SKIP_DEFINER_SUPPORTED -eq 1 ]]; then
  OPTS+=(--skip-definer)
fi

# Add DROP VIEW if supported natively
if [[ $ADD_DROP_VIEW_SUPPORTED -eq 1 ]]; then
  OPTS+=(--add-drop-view)
fi

# Dump a single DB (positional arg), so no CREATE DATABASE/USE in output
if ! "$MYSQLDUMP" "${OPTS[@]}" "$DB" > "$TMP"; then
  echo "❌ mysqldump failed." >&2
  exit 1
fi

# --- Portable in-place sed (GNU/Linux & BSD/macOS) ---
sed_inplace() {
  # usage: sed_inplace 's/old/new/g' FILE
  if sed --version >/dev/null 2>&1; then
    sed -E -i "$@"
  else
    # BSD sed (no --version; -i requires a backup suffix which can be empty)
    local last_arg="${@: -1}"
    sed -E -i '' "${@:1:$(($#-1))}" "$last_arg"
  fi
}

# --- Remove schema qualifiers like `dev-gp`.`table` inside view/routine/trigger bodies ---
# Escape DB name for sed
ESCDB=$(printf '%s\n' "$DB" | sed 's/[][\/.^$*+?|(){}-]/\\&/g')

# Replace backticked qualifiers: `dev-gp`.`table` -> `table`
sed_inplace "s/\`$ESCDB\`\.\`([^\`]+)\`/\`\1\`/g" "$TMP"

# Also catch rare non-backticked cases: dev-gp.table -> table  (word boundary)
sed_inplace "s/\\b$ESCDB\\.([A-Za-z0-9_]+)/\\1/g" "$TMP"

# --- Strip DEFINER & 'SQL SECURITY DEFINER' if not handled natively by mysqldump ---
if [[ $NO_DEFINER_SUPPORTED -eq 0 && $SKIP_DEFINER_SUPPORTED -eq 0 ]]; then
  sed_inplace \
    -e 's/DEFINER=`[^`]*`@`[^`]*`[[:space:]]*//gI' \
    -e 's/DEFINER=[^ ]+[[:space:]]*//gI' \
    -e 's/SQL[[:space:]]+SECURITY[[:space:]]+DEFINER//gI' \
    "$TMP"
fi

# --- Inject DROP VIEW IF EXISTS when native --add-drop-view is missing ---
if [[ $ADD_DROP_VIEW_SUPPORTED -eq 0 ]]; then
  awk '{
    if (match($0, /^CREATE[[:space:]].*VIEW[[:space:]]+`([^`]+)`/, m)) {
      print "DROP VIEW IF EXISTS `" m[1] "`;";
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
