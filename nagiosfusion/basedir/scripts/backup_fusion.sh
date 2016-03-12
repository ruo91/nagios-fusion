#!/bin/sh

BASEDIR=$(dirname $0)

###############################
# USAGE / HELP
###############################
usage () {
	echo ""
	echo "Use this script to backup Nagios XI."
	echo ""
		echo " -n | --name              Set the name of the backup minus the .tar.gz"
        echo " -p | --prepend           Prepend a string to the .tar.gz name"
        echo " -a | --append            Append a string to the .tar.gz name"
        echo " -d | --directory         Change the directory to store the compressed backup"
	echo ""
}

###############################
# ADDING LOGIC FOR NEW BACKUPS
###############################
while [ -n "$1" ]; do
	case "$1" in
		-h | --help)
			usage
			exit 0
			;;
		-n | --name)
			fullname=$2
			;;
		-p | --prepend)
			prepend=$2"."
			;;
		-a | --append)
			append="."$2
			;;
		-d | --directory)
			rootdir=$2
			;;
	esac
	shift
done

if [ -z $rootdir ]; then
	rootdir="/store/backups/nagiosfusion"
fi

# Move to root dir to store backups
cd $rootdir

#############################
# SET THE NAME & TIME
#############################
name=$fullname

if [ -z $fullname ]; then
	name=$prepend$ts$append
fi

# Get current Unix timestamp as name
ts=`date +%s`
if [ -z $name ]; then
	name=$ts
fi

# My working directory
mydir=$rootdir/$name

# Make directory for this specific backup
mkdir -p $mydir

##############################
# BACKUP DIRS
##############################

echo "Backing up Nagios Fusion..."
tar czfps $mydir/nagiosfusion.tar.gz /usr/local/nagiosfusion

##############################
# BACKUP DATABASES
##############################

echo "Backing up PostgresQL databases..."
mkdir -p $mydir/pgsql
pg_dump -c -U nagiosfusion nagiosfusion > $mydir/pgsql/nagiosfusion.sql
res=$?
if [ $res != 0 ]; then
	echo "Error backing up PostgresQL database 'nagiosxi' !"
	exit;
fi

##############################
# BACKUP CRONJOB ENTRIES
##############################
# Not necessary

##############################
# BACKUP SUDOERS
##############################
# Not necessary

##############################
# BACKUP LOGROTATE
##############################
echo "Backing up logrotate config files..."
mkdir -p $mydir/logrotate
cp -rp /etc/logrotate.d/nagiosfusion $mydir/logrotate

##############################
# BACKUP APACHE CONFIG FILES
##############################
echo "Backing up Apache config files..."
mkdir -p $mydir/httpd
cp -rp /etc/httpd/conf.d/nagiosfusion.conf $mydir/httpd

##############################
# COMPRESS BACKUP
##############################
echo "Compressing backup..."
tar czfps $name.tar.gz $name
rm -rf $name

if [ -s $name.tar.gz ];then

	echo " "
	echo "==============="
	echo "BACKUP COMPLETE"
	echo "==============="
	echo "Backup stored in $rootdir/$name.tar.gz"

	exit 0;
else
	echo " "
	echo "==============="
	echo "BACKUP FAILED"
	echo "==============="
	echo "File was not created at $rootdir/$name.tar.gz"

	exit 1;
fi