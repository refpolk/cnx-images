#!/bin/sh

mysqldump -u ulli Images | gzip > /run/user/1000/gvfs/smb-share:server=sol-file,share=samba/Backups/database/images_backup_`date +%F-%s`.sql.gz