#!/bin/bash
# Backup Databases

now1="/home/sites/27a/8/826366cb9c/backups"
now2=$(date +"%Y_%m_%d")
now="$now1$now2"

# Create the backup directory
mkdir $now1/$now2
if [ $? -ne 0 ]; then
    echo "Backup failed at directory creation" | mail -s "Backup Failed - Directory Creation" alan@gameplan.org.uk
    exit 1
fi

# Backup GP Prod 2023
mysqldump -h sdb-e.hosting.stackcp.net -u gplan2023-313833d21e --password=B24fi6bpv8# gplan2023-313833d21e > $now1/$now2/gp23.sql
if [ $? -ne 0 ]; then
    echo "Backup failed during backup database dump" | mail -s "Backup Failed - Database Dump" alan@gameplan.org.uk
    exit 1
fi

# Backup GP8
mysqldump -h sdb-78.hosting.stackcp.net -u gplan8-35303737a645 --password=hnebllfgibsb0n0irghsalEe gplan8-35303737a645 > $now1/$now2/gp8.sql
if [ $? -ne 0 ]; then
    echo "Backup failed during prod database dump" | mail -s "Backup Failed - Database Dump" alan@gameplan.org.uk
    exit 1
fi

# Compress the backup
tar cvzf $now.tar.gz $now1/$now2/
if [ $? -ne 0 ]; then
    echo "Backup failed during compression" | mail -s "Backup Failed - Compression" alan@gameplan.org.uk
    exit 1
fi

# Move the backup to the final destination
mv $now.tar.gz ~/backups/
if [ $? -ne 0 ]; then
    echo "Backup failed during file move" | mail -s "Backup Failed - File Move" alan@gameplan.org.uk
    exit 1
fi

# Clean up the temporary backup directory
rm -rf $now1/$now2
if [ $? -ne 0 ]; then
    echo "Backup failed during cleanup" | mail -s "Backup Failed - Cleanup" alan@gameplan.org.uk
    exit 1
fi

# All steps succeeded
echo "Backup completed successfully"
