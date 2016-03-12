#!/bin/sh -e
PATH=$PATH:/sbin:/usr/sbin
TS=$(date +%s)
error=false

checkerrors () {
	"$@"
	if [ $? -ne 0 ]; then
		error=true
	fi
}

###############################
# USAGE / HELP
###############################
usage () {
	echo ""
	echo "Use this script to upgrade your Nagios Fusion instance to the latest version."
	echo ""
	echo " -t | --time             	Send a timestamp for the log to be renamed as once finished"
	echo " -f | --file              The filename/url/location of the fusion update"
	echo ""
}

###############################
# GET THE VARIABLES
###############################
while [ -n "$1" ]; do
	case "$1" in
		-h | --help)
			usage
			exit 0
			;;
		-t | --time)
			TS=$2
			;;
		-f | --file)
			FILE=$2
			;;
	esac
	shift
done

# Create a log file
rm -rf /usr/local/nagiosfusion/tmp/upgrade.log
touch /usr/local/nagiosfusion/tmp/upgrade.log
chown nagios:nagios /usr/local/nagiosfusion/tmp/upgrade.log

# Backup Fusion before upgrade
echo "---- Starting Nagios Fusion Backup ----" > /usr/local/nagiosfusion/tmp/upgrade.log
cd /usr/local/nagiosfusion/scripts
./backup_fusion.sh -p autoupgrade_backup >> /usr/local/nagiosfusion/tmp/upgrade.log 2>&1

# Perform upgrade
echo "" >> /usr/local/nagiosfusion/tmp/upgrade.log
echo "---- Starting Nagios Fusion Upgrade ----" >> /usr/local/nagiosfusion/tmp/upgrade.log
echo "Cleaning up temp directory..." >> /usr/local/nagiosfusion/tmp/upgrade.log
cd /usr/local/nagiosfusion/tmp
rm -rf fusion*.tar.gz nagiosfusion fusion-*.tar.gz
echo "Downloading Latest Nagios Fusion Tarball..." >> /usr/local/nagiosfusion/tmp/upgrade.log
wget "$FILE"
if [ -f fusion-latest.tar.gz ]; then
        tar xzf fusion-latest.tar.gz
else
        tar xzf fusion-*.tar.gz
fi
cd nagiosfusion

checkerrors ./upgrade -n >> /usr/local/nagiosfusion/tmp/upgrade.log 2>&1
if $error ; then
	FN="failed.$TS"
else
	FN="success.$TS"
fi

# Make log directory if it doesnt exist and give proper permissions for apache to be able to read it
if [[ ! -e /usr/local/nagiosfusion/var/upgrades ]]; then
	mkdir -p /usr/local/nagiosfusion/var/upgrades
	chown nagios:nagios /usr/local/nagiosfusion/var/upgrades
	chmod 754 /usr/local/nagiosfusion/var/upgrades
	chmod +x /usr/local/nagiosfusion/var/upgrades
fi

# Copy over the file and give error or not
cp /usr/local/nagiosfusion/tmp/upgrade.log /usr/local/nagiosfusion/var/upgrades/$FN.log
chown -R nagios:nagios /usr/local/nagiosfusion/var/upgrades
chmod 754 /usr/local/nagiosfusion/var/upgrades/$FN.log