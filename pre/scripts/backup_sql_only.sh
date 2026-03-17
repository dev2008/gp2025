#!/usr/bin/env bash
# backup_sql_only.sh — local DB dumps to ~/sql

###############################################################################
#                                CONFIG                                       #
###############################################################################

# Mail
EMAIL="gp@milnesconsultancy.co.uk"
SEND_SUCCESS_MAIL=true
CLIENT="GP"
EMAIL_SUBJECT_PREFIX="[${CLIENT} backup]"

# Local dump destination
SQL_DIR="${HOME}/sql"

# Databases (each with own creds). Add more entries as needed.
# Format per item: 'name=<db>|host=<host>|user=<user>|pass=<password>'
DBS=(
  'name=cricket-36399056|host=shareddb-f.hosting.stackcp.net|user=cricket-36399056|pass=Cccq5pk79i#'
  'name=gplan8-35303737a645|host=sdb-78.hosting.stackcp.net|user=gplan8-35303737a645|pass=hnebllfgibsb0n0irghsalEe'
)

###############################################################################
#                          INTERNALS (no need to edit)                        #
###############################################################################

set -Eeuo pipefail
set +H        # turn off history expansion so '!' in passwords won't bite
umask 077

PATH="/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:$PATH"

LOG_DIR="${HOME}/logs"
LOG_FILE="${LOG_DIR}/backup-sql-only.log"
LOCK_DIR="${HOME}/.cache/locks"
LOCK_FILE="${LOCK_DIR}/backup-sql-only.lock"

mkdir -p "${SQL_DIR}" "${LOG_DIR}" "${LOCK_DIR}"

log() {
  printf '%s [%s] %s\n' "$(date '+%F %T')" "$1" "$2" | tee -a "${LOG_FILE}"
}

CURRENT_STAGE="(not started)"

have_cmd() { command -v "$1" >/dev/null 2>&1; }

send_email() {
  local subject="$1" body="$2"
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

for c in mysqldump date; do
  have_cmd "$c" || { log "ERROR" "Required command not found: ${c}"; exit 2; }
done

get_field() {
  local key="$1" data="$2" kv
  local IFS='|'
  for kv in $data; do
    case "$kv" in
      ${key}=*) printf '%s' "${kv#*=}"; return 0 ;;
    esac
  done
  return 1
}

###############################################################################
#                                WORKFLOW                                     #
###############################################################################

SNAP_TS="$(date +%F_%H-%M-%S)"

CURRENT_STAGE="Start job"
log "INFO" "===== Backup job start for ${CLIENT} at ${SNAP_TS} ====="
log "INFO" "Dumping to: ${SQL_DIR}"

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

  safe_db="${DB_NAME//[^A-Za-z0-9_.-]/_}"
  dump_file="${SQL_DIR}/${CLIENT}-${i}-${safe_db}-${SNAP_TS}.sql"

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

CURRENT_STAGE="End job"
log "INFO" "===== Backup job end for ${CLIENT} at $(date '+%F %T') ====="

if [[ "${SEND_SUCCESS_MAIL}" == true ]]; then
  tail_snippet="$(tail -n 200 -- "${LOG_FILE}" || true)"
  send_email "${EMAIL_SUBJECT_PREFIX} SUCCESS" \
             "Backup completed successfully at $(date '+%F %T').\nDumps saved to: ${SQL_DIR}\n\nLast 200 log lines:\n\n${tail_snippet}"
  log "INFO" "Success email sent."
fi

exit 0
