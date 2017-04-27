#chttps://dev.mysql.com/doc/refman/5.7/en/mysqldump.html

#https://www.digitalocean.com/community/tutorials/how-to-import-and-export-databases-and-reset-a-root-password-in-mysql
#https://www.digitalocean.com/community/tutorials/how-to-backup-mysql-databases-on-an-ubuntu-vps

#http://askubuntu.com/questions/698845/automatic-backup-of-mysql-on-ubuntu

#http://stackoverflow.com/questions/9293042/how-to-perform-a-mysqldump-without-a-password-prompt?answertab=votes#tab-top

mysqldump -u ulli -p Sherwood109 Images | gzip > ./images_backup_`date +%F`.sql.gz


/usr/local/mysql/bin/mysqldump -u ulli -pSherwood109 Images | gzip > /Users/julien/Documents/Dev/Connexions/cnx-images/database/images_backup_`date +%F`.sql.gz