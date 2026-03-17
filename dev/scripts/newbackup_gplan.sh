#!/usr/bin/env bash
# backup_snapshots.sh — rsync.net snapshot backups with DB dumps

###############################################################################
#                                CONFIG                                       #
###############################################################################

# Mail
EMAIL="gp@milnesconsultancy.co.uk"
SEND_SUCCESS_MAIL=true
CLIENT="Gameplan"
EMAIL_SUBJECT_PREFIX="[${CLIENT} backup]"

# Base paths (local)
BASE="/home/sites/27a/8/826366cb9c"
SQL_DIR="${BASE}/sql"
WEB_DIR="${BASE}/public_html"

# Databases (each with own creds). Add more entries as needed.
# Format per item: 'name=<db>|host=<host>|user=<user>|pass=<password>'
DBS=(
  'name=cricket-36399056|host=shareddb-f.hosting.stackcp.net|user=cricket-36399056|pass=Cccq5pk79i#'
  'name=gplan8-35303737a645|host=sdb-78.hosting.stackcp.net|user=gplan8-35303737a645|pass=hnebllfgibsb0n0irghsalEe'
)

# Remote rsync.net target (SSH keys already set up)
REMOTE_USER="zh2342"
REMOTE_HOST="zh2342.rsync.net"
REMOTE_BASE="gameplan"                  # remote base folder (relative to remote $HOME)
REMOTE_SNAP="${REMOTE_BASE}/snapshots"     # snapshot root on remote

# Logs & lock (local)
LOG_DIR="${BASE}/logs"
LOG_FILE="${LOG_DIR}/backup-snapshots.log"
LOCK_DIR="${BASE}/.cache/locks"
LOCK_FILE="${LOCK_DIR}/backup-snapshots.lock"

# after CONFIG (EMAIL/CLIENT/BASE/...):
mkdir -p "${LOG_DIR}" "${LOCK_DIR}" "${SQL_DIR}"

# define log() early so guards can use it
log() {
  printf '%s [%s] %s\n' "$(date '+%F %T')" "$1" "$2" | tee -a "${LOG_FILE}"
}

ensure_abs() { local n="$1" p="$2"; [[ "$p" = /* ]] || { log "ERROR" "$n must be absolute: $p"; exit 2; }; }
ensure_dir() { local p="$1"; [[ -d "$p" ]] || { log "ERROR" "Missing directory: $p"; exit 2; }; }

# (optional) normalize
BASE="${BASE%/}"; SQL_DIR="${SQL_DIR%/}"; WEB_DIR="${WEB_DIR%/}"

ensure_abs "BASE" "$BASE"
ensure_abs "SQL_DIR" "$SQL_DIR"
ensure_abs "WEB_DIR" "$WEB_DIR"
ensure_dir "$SQL_DIR"
ensure_dir "$WEB_DIR"

# Optional: rsync transfer tuning
RSYNC_BW_LIMIT=""         # e.g. "40960" for 40MB/s, or leave blank for unlimited

# sanity checks
[[ "$BASE" == /* ]] || { log "ERROR" "BASE must be absolute: '$BASE'"; exit 2; }
[[ "$SQL_DIR" == /* ]] || { log "ERROR" "SQL_DIR must be absolute: '$SQL_DIR'"; exit 2; }
[[ "$WEB_DIR" == /* ]] || { log "ERROR" "WEB_DIR must be absolute: '$WEB_DIR'"; exit 2; }

case "$SQL_DIR" in "$BASE"/*) ;; *) log "ERROR" "SQL_DIR must be under BASE"; exit 2;; esac
case "$WEB_DIR" in "$BASE"/*) ;; *) log "ERROR" "WEB_DIR must be under BASE"; exit 2;; esac

log "INFO" "Resolved paths: BASE=$BASE SQL_DIR=$SQL_DIR WEB_DIR=$WEB_DIR"

log "INFO" "Remote target: ${REMOTE_USER}@${REMOTE_HOST} base='${REMOTE_SNAP}'"


###############################################################################
#                          INTERNALS (no need to edit)                        #
###############################################################################

set -Eeuo pipefail
set +H        # turn off history expansion so '!' in passwords won't bite
umask 077

PATH="/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:$PATH"
SSH_TARGET="${REMOTE_USER}@${REMOTE_HOST}"
RSYNC_SSH=(ssh -o BatchMode=yes -o StrictHostKeyChecking=accept-new -o ConnectTimeout=20)
RSYNC_COMMON=(-aH --delete --numeric-ids --delete-excluded -z --partial --human-readable -e "${RSYNC_SSH[*]}")
[[ -n "${RSYNC_BW_LIMIT}" ]] && RSYNC_COMMON+=(--bwlimit="${RSYNC_BW_LIMIT}")

SNAP_TS="$(date +%F_%H-%M-%S)"
SNAP_DIR="${REMOTE_SNAP}/${SNAP_TS}"

CURRENT_STAGE="(not started)"

have_cmd() { command -v "$1" >/dev/null 2>&1; }

send_email() {
  local subject="$1"
  local body="$2"
  if have_cmd mail; then
    printf '%s\n' "$body" | mail -s "$subject" "$EMAIL"
  elif have_cmd mailx; then
    printf '%s\n' "$body" | mailx -s "$subject" "$EMAIL"
  elif have_cmd sendmail; then
    { printf 'To: %s\nSubject: %s\n\n%s\n' "$EMAIL" "$subject" "$body"; } | sendmail -t
  else
    log "WARN" "No mailer available; could not send email: ${subject}"
  fi
}

on_error() {
  local ec=$?
  local tail_snippet
  tail_snippet="$(tail -n 200 -- "${LOG_FILE}" || true)"
  send_email "${EMAIL_SUBJECT_PREFIX} FAILED at stage: ${CURRENT_STAGE}" \
             "Stage: ${CURRENT_STAGE}\nExit code: $ec\n\nLast 200 log lines:\n\n${tail_snippet}"
  log "ERROR" "Failed at stage: ${CURRENT_STAGE} (exit $ec). Email sent."
  rm -f -- "${LOCK_FILE}" || true
  exit $ec
}
trap on_error ERR

# Simple PID file lock
if [[ -f "${LOCK_FILE}" ]]; then
  old_pid="$(cat "${LOCK_FILE}" || true)"
  if [[ -n "${old_pid}" ]] && kill -0 "${old_pid}" 2>/dev/null; then
    log "ERROR" "Another run is active (pid ${old_pid}). Exiting."
    exit 1
  else
    log "WARN" "Stale lock (pid ${old_pid}) found; removing."
    rm -f -- "${LOCK_FILE}"
  fi
fi
echo "$$" > "${LOCK_FILE}"
trap 'rm -f -- "${LOCK_FILE}"' EXIT

require_cmds=(ssh rsync mysqldump date find dirname basename)
for c in "${require_cmds[@]}"; do
  if ! have_cmd "$c"; then
    log "ERROR" "Required command not found: ${c}"
    exit 2
  fi
done

get_field() {  # key, "name=...|host=...|user=...|pass=..."
  local key="$1" data="$2" kv
  local IFS='|'
  for kv in $data; do
    case "$kv" in
      ${key}=*) printf '%s' "${kv#*=}"; return 0 ;;
    esac
  done
  return 1
}

remote_exists() {  # path on remote
  "${RSYNC_SSH[@]}" "${SSH_TARGET}" "test -e '$1'"
}

make_remote_dirs() {  # creates REMOTE_SNAP and target snapshot subdirs
  "${RSYNC_SSH[@]}" "${SSH_TARGET}" "mkdir -p '${REMOTE_SNAP}' '${SNAP_DIR}/sql' '${SNAP_DIR}/web'"
}

update_current_symlink() {
  "${RSYNC_SSH[@]}" "${SSH_TARGET}" "rm -f '${REMOTE_SNAP}/current'"
  "${RSYNC_SSH[@]}" "${SSH_TARGET}" "ln -s '${SNAP_TS}' '${REMOTE_SNAP}/current'"
  log "INFO" "Updated '${REMOTE_SNAP}/current' → '${SNAP_TS}'"
}

rsync_with_snapshot() {  # src_dir (local), subdir_on_remote (sql|web)
  local src="$1" sub="$2"
  local link_rel="../../current/${sub}"
  local used="link-dest=${link_rel}"

  # Try with link-dest first; on any rsync error, retry once without it
  if ! rsync "${RSYNC_COMMON[@]}" --link-dest="${link_rel}" -- \
      "${src%/}/" "${SSH_TARGET}:${SNAP_DIR}/${sub}/" >>"${LOG_FILE}" 2>&1; then
    used="link-dest=none (fallback)"
    rsync "${RSYNC_COMMON[@]}" -- \
      "${src%/}/" "${SSH_TARGET}:${SNAP_DIR}/${sub}/" >>"${LOG_FILE}" 2>&1
  fi

  log "INFO" "Rsync ${sub} → ${SNAP_DIR}/${sub} (${used})"
}

###############################################################################
#                                WORKFLOW                                     #
###############################################################################

CURRENT_STAGE="Start job"
log "INFO" "===== Backup job start for ${CLIENT} at ${SNAP_TS} ====="

CURRENT_STAGE="Remote setup (mkdir snapshot dirs)"
make_remote_dirs
log "INFO" "Remote snapshot directories prepared at ${SNAP_DIR}"

CURRENT_STAGE="Dump databases"
i=1
for dbdef in "${DBS[@]}"; do
  DB_NAME="$(get_field name "$dbdef")"
  DB_HOST="$(get_field host "$dbdef")"
  DB_USER="$(get_field user "$dbdef")"
  DB_PASS="$(get_field pass "$dbdef")"

  [[ -z "${DB_NAME}" || -z "${DB_HOST}" || -z "${DB_USER}" || -z "${DB_PASS}" ]] && {
    log "ERROR" "DB definition #${i} is incomplete."
    exit 3
  }

  safe_db="${DB_NAME//[^A-Za-z0-9_.-]/_}"   # sanitise db name for filesystem
  dump_file="${SQL_DIR}/${CLIENT}-${i}-${safe_db}.sql"

  log "INFO" "Dumping DB #${i} (${DB_NAME}) from ${DB_HOST} → ${dump_file}"
  mysqldump \
    -h "${DB_HOST}" -u "${DB_USER}" -p"${DB_PASS}" \
    --single-transaction --quick --skip-lock-tables \
    --default-character-set=utf8mb4 \
    --skip-triggers \
    "${DB_NAME}" > "${dump_file}"

  if [[ ! -s "${dump_file}" ]]; then
    log "ERROR" "Dump for DB #${i} (${DB_NAME}) created but empty."
    exit 4
  fi
  log "INFO" "Dumped ${DB_NAME} (DB #${i}); size=$(stat -c%s "${dump_file}" 2>/dev/null || wc -c <"${dump_file}") bytes"
  ((i++))
done

CURRENT_STAGE="Rsync SQL dumps (snapshot)"
rsync_with_snapshot "${SQL_DIR}" "sql"

CURRENT_STAGE="Rsync WEB_DIR (snapshot)"
rsync_with_snapshot "${WEB_DIR}" "web"

CURRENT_STAGE="Finalize snapshot (update current symlink)"
update_current_symlink

# Verify the new snapshot itself (direct paths; not via 'current')
CURRENT_STAGE="Verify new snapshot contents"
"${RSYNC_SSH[@]}" "${SSH_TARGET}" "ls -ld '${SNAP_DIR}/sql' '${SNAP_DIR}/web'"

# Mark snapshot complete (no output redirection allowed on rsync.net)
CURRENT_STAGE="Mark snapshot complete"
"${RSYNC_SSH[@]}" "${SSH_TARGET}" "touch '${SNAP_DIR}/_OK'"

# Flip 'current' to the new snapshot (relative symlink)
CURRENT_STAGE="Finalize snapshot (update current symlink)"

# Show where 'current' points (informational)
CURRENT_STAGE="Show current symlink"
"${RSYNC_SSH[@]}" "${SSH_TARGET}" "ls -l '${REMOTE_SNAP}/current'"


CURRENT_STAGE="Clear local SQL dumps"
find "${SQL_DIR}" -maxdepth 1 -type f -name "${CLIENT}-*.sql" -print -delete >>"${LOG_FILE}" 2>&1 || true
log "INFO" "Cleared local SQL dumps for ${CLIENT}"

CURRENT_STAGE="End job"
log "INFO" "===== Backup job end for ${CLIENT} at $(date '+%F %T') ====="

if [[ "${SEND_SUCCESS_MAIL}" == true ]]; then
  tail_snippet="$(tail -n 200 -- "${LOG_FILE}" || true)"
  send_email "${EMAIL_SUBJECT_PREFIX} SUCCESS" \
             "Backup completed successfully at $(date '+%F %T').\nSnapshot: ${SNAP_DIR}\n\nLast 200 log lines:\n\n${tail_snippet}"
  log "INFO" "Success email sent."
fi

exit 0
