#!/bin/sh
PATH=$PATH:/sbin:/usr/sbin

###############################
# USAGE / HELP
###############################
usage () {
	echo ""
	echo "Use this script change your Timezone for your Nagios Fusion system. (PHP and Localtime)"
	echo ""
	echo " -z | --zone             	The Posix & PHP supported timezone you want to change to"
	echo "                                  Example Timezone: America/Chicago"
	echo ""
	echo " -h | --help             	Show the help section"
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
		-z | --zone)
			TZONE=$2
			;;
	esac
	shift
done

# Set the sysconfig clock time
echo 'ZONE="'$TZONE'"' > /etc/sysconfig/clock

# Set the localtime
ln -sf /usr/share/zoneinfo/$TZONE /etc/localtime

# Set the PHP timezone
\cp -f /etc/php.ini /etc/php.ini.backup
sed -ri "s~^;?date\.timezone *=.*~date.timezone = $TZONE~" /etc/php.ini

# sleep for 2 seconds
sleep 2

# Restart apache to make sure new timezone in php is set
service httpd reload

echo 'All timezone configurations updated to "'$TZONE'"'