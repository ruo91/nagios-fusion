#!/bin/sh

# Make sure we have the backup file
if [ $# != 1 ]; then
	echo "Usage: $0 <backupfile>"
	echo "This script restores your Fusion system using a previously made Nagios Fusion backup file."
	exit 1
fi
backupfile=$1

# Must be root
me=`whoami`
if [ $me != "root" ]; then
	echo "You must be root to run this script."
	exit 1
fi

rootdir=/store/backups/nagiosfusion

##############################
# MAKE SURE BACKUP FILE EXIST
##############################
if [ ! -f $backupfile ]; then
	echo "Unable to find backup file $backupfile!"
	exit 1
fi

##############################
# MAKE TEMP RESTORE DIRECTORY
##############################
#ts=`echo $backupfile | cut -d . -f 1`
ts=`date +%s`
echo "TS=$ts"
mydir=${rootdir}/${ts}-restore
mkdir -p $mydir
if [ ! -d $mydir ]; then
	echo "Unable to create restore directory $mydir!"
	exit 1
fi


##############################
# UN-TAR BACKUP
##############################
echo "Extracting backup to $mydir..."
cd $mydir
tar xzfps $backupfile

# Change to subdirectory
cd `ls`

# Make sure we have some directories here...
backupdir=`pwd`
echo "In $backupdir..."
if [ ! -f nagiosfusion.tar.gz ]; then
	echo "Unable to find files to restore in $backupdir"
	exit 1
fi

echo "Backup files look okay.  Preparing to restore..."


##############################
# SHUTDOWN SERVICES
##############################
echo "Shutting down services..."


##############################
# RESTORE DIRS
##############################
rootdir=/
echo "Restoring directories to ${rootdir}..."

# Nagios xI
echo "Restoring Nagios Fusion..."
rm -rf /usr/local/nagiosfusion
cd $rootdir && tar xzfps $backupdir/nagiosfusion.tar.gz 

cd $backupdir

##############################
# RESTORE DATABASES
##############################

echo "Restoring PostgresQL databases..."
psql -U nagiosfusion nagiosfusion < pgsql/nagiosfusion.sql
res=$?
if [ $res != 0 ]; then
	echo "Error restoring PostgresQL database 'nagiosfusion' !"
	exit;
fi

echo "Restarting database servers..."
/etc/init.d/postgresql restart

##############################
# RESTORE CRONJOB ENTRIES
##############################
# Not necessary

##############################
# RESTORE SUDOERS
##############################
# Not necessary

##############################
# RESTORE LOGROTATE
##############################
echo "Restoring logrotate config files..."
cp -rp logrotate/nagiosfusion /etc/logrotate.d

##############################
# RESTORE APACHE CONFIG FILES
##############################
echo "Restoring Apache config files..."
cp -rp httpd/*.conf /etc/httpd/conf.d


##############################
# RESTART SERVICES
##############################


##############################
# DELETE TEMP RESTORE DIRECTORY
##############################

rm -rf $mydir

echo " "
echo "==============="
echo "RESTORE COMPLETE"
echo "==============="

exit 0;