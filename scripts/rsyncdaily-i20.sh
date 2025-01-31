#!/bin/bash
#Backup All Files

/usr/bin/rsync -avH /home/sites/27a/8/826366cb9c/public_html zh2342@zh2342.rsync.net:gplan
/usr/bin/rsync -avH /home/sites/27a/8/826366cb9c/backups zh2342@zh2342.rsync.net:gp_backups
