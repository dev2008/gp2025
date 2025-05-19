#!/bin/bash
set -euo pipefail
START_TIME=$(date +%s)

# === CONFIG SECTION ===
LOGFILE="/home/sites/27a/8/826366cb9c/logs/backup_sync.log"
EMAIL="alan@gameplan.org.uk"
SEND_SUCCESS_MAIL=true
DRY_RUN=false

BACKUP_BASE="/home/sites/27a/8/826366cb9c/ext_sync"
ARCHIVE_TARGET="/home/sites/27a/8/826366cb9c/transfer"
REMOTE_TARGET="zh2342s1@zh2342s1.rsync.net:gplan"
DB_HOST="sdb-78.hosting.stackcp.net"
DB_USER="gplan8-35303737a645"
DB_NAME="gplan8-35303737a645"
DB_PASS="hnebllfgibsb0n0irghsalEe"

# === END CONFIG ===

log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a "$LOGFILE"
}

fail_exit() {
    log "$1"
    echo "$1" | mail -s "$2" "$EMAIL"
    exit 1
}

simulate_or_run() {
    if [ "$DRY_RUN" = true ]; then
        log "[DRY-RUN] $1"
    else
        eval "$1"
    fi
}

TIMESTAMP=$(date +"%Y_%m_%d_%H_%M_%S")
BACKUP_DIR="${BACKUP_BASE}/${TIMESTAMP}"
ARCHIVE="${BACKUP_BASE}/${TIMESTAMP}.tar.gz"

log "=== Backup & Sync Started === (DRY_RUN=$DRY_RUN)"
simulate_or_run "mkdir -p '$BACKUP_DIR'" || fail_exit "GP Backup failed at directory creation" "GP Backup Failed - Directory Creation"

if [ "$DRY_RUN" = false ]; then
    mysqldump -h"$DB_HOST" -u"$DB_USER" --password="$DB_PASS" "$DB_NAME" > "$BACKUP_DIR/gplan2025.sql" \
        || fail_exit "GP Backup failed during database dump" "GP Backup Failed - Database Dump"
    log "Database dumped"
else
    log "[DRY-RUN] Simulating mysqldump > $BACKUP_DIR/gplan2025.sql"
fi

simulate_or_run "tar -cvzf '$ARCHIVE' -C '$BACKUP_BASE' '$TIMESTAMP'" \
    || fail_exit "GP Backup failed during compression" "GP Backup Failed - Compression"
log "Archive creation step done"

# === Archive Verification Step ===
if [ "$DRY_RUN" = false ]; then
    if tar -tzf "$ARCHIVE" > /dev/null; then
        log "Archive verified successfully"
    else
        fail_exit "GP Archive verification failed" "GP Backup Failed - Archive Verification"
    fi
else
    log "[DRY-RUN] Simulating archive verification"
fi

simulate_or_run "mv '$ARCHIVE' '$ARCHIVE_TARGET/'" \
    || fail_exit "GP Backup failed during archive move" "GP Backup Failed - File Move"
log "Archive move step done"
ARCHIVE_SIZE=$(du -h "$ARCHIVE_TARGET/$(basename "$ARCHIVE")" | cut -f1)

simulate_or_run "rm -rf '$BACKUP_DIR'" \
    || fail_exit "GP Backup failed during cleanup" "GP Backup Failed - Cleanup"
log "Temp directory cleanup step done"

if [ "$DRY_RUN" = false ]; then
    /usr/bin/rsync -avH /home/sites/27a/8/826366cb9c/ "$REMOTE_TARGET" \
        || fail_exit "GP Rsync failed" "GP Sync Failed"
else
    log "[DRY-RUN] Simulating rsync to $REMOTE_TARGET"
fi
log "Rsync step done"

simulate_or_run "find '$BACKUP_BASE' -type f -delete" \
    || fail_exit "GP Cleanup failed after rsync" "GP Cleanup Failed After Rsync"
log "Post-rsync cleanup step done"

simulate_or_run "find '$ARCHIVE_TARGET' -type f -delete" || fail_exit "GP Temp cleanup failed" "GP Cleanup Failed - Temp Purge"
log "All files in $ARCHIVE_TARGET deleted after successful sync"


log "=== Backup & Sync Completed Successfully ==="


log "Final archive stored: $ARCHIVE_TARGET/$(basename "$ARCHIVE")"
log "Archive size: $ARCHIVE_SIZE"
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))
log "Total runtime: ${DURATION}s"


if [ "$SEND_SUCCESS_MAIL" = true ]; then
    tail -13 "$LOGFILE" | mail -s "SUCCESS - GP Backup & Sync  [$TIMESTAMP]" "$EMAIL"
fi

# === End of script ===
