#!/bin/sh
#
#
#Execute this file from your crontab
#write a line like this
#4 5 * * * cd /usr/local/htdocs/egroupware/backup && ./egw.backup.sh
#
#but change the path to your egroupware web directory
#
#first  COl = minutes of the hour
#second COL = hour of the day
#
#this setting will run the backup each day at 5:04H
#take care to change ONLY to your path of php exec below if different
#to check run first time manually
#
umask 022

PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/local/bin
export PATH
php -q ./egw_data_backup.php

echo -e "\nbackup of the phpgroupware data done\n" ;

exit 0
