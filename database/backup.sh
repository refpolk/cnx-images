#!/bin/sh

# Dump the Images databse and copy the file to the Samba share on the SOL machine

mysqldump -u ulli Images | gzip > /run/user/1000/gvfs/smb-share:server=sol-file,share=samba/Backups/database/images_backup_`date +%F-%s`.sql.gz

# Delete backups older than 30 days

find /run/user/1000/gvfs/smb-share:server=sol-file,share=samba/Backups/database -mtime +30 -type f -delete

# To restore a backup use the below command:

# mysql -u ulli Images < backup_to_restore.sql