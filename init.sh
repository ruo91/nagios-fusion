#!/bin/sh

##Fusion 2012 installer 

fusionvar() {
	./fusionvar "$1" "$2"
	eval "$1"=\"\$2\"
}

# Fusion version
fusionvar fusionver $(sed -n '/full/ s/.*=\(.*\)/\L\1/p' ./nagiosfusion/basedir/var/fusionversion)

# OS-related variables have a detailed long variable, and a more useful short
# one: distro/dist, version/ver, architecture/arch. If in doubt, use the short
. ./get-os-info
fusionvar distro  "$distro"
fusionvar version "$version"
fusionvar ver     "${version%%.*}" # short major version, e.g. "6" instead of "6.2"
fusionvar architecture "$architecture"

# Set dist variable like before (el5/el6 on both CentOS & Red Hat)
case "$distro" in
	CentOS | RedHatEnterpriseServer )
		fusionvar dist "el$ver"
		;;
	Debian )
		fusionvar dist "debian$ver"
		;;
	*)
		fusionvar dist $(echo "$distro$version" | tr A-Z a-z)
esac

# i386 is a more useful value than i686 for el5, because repo paths and
# package names use i386
if [ "$dist $architecture" = "el5 i686" ]; then
	fusionvar arch i386
else
	fusionvar arch "$architecture"
fi

case "$dist" in
	el5 | el6 | el7)
		if [ "$arch" = "x86_64" ]; then
			fusionvar php_extension_dir /usr/lib64/php/modules
		fi
		;;
	*)
		:
esac
