<?php
//
// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: upgrade.php 88 2010-04-12 21:37:21Z egalstad $

require_once(dirname(__FILE__).'/config.inc.php');
require_once(dirname(__FILE__).'/includes/auth.inc.php');
require_once(dirname(__FILE__).'/includes/utils.inc.php');
require_once(dirname(__FILE__).'/includes/pageparts.inc.php');

// Turn off error reporting for this file to keep utils functions from passing
// error information into the stdout
error_reporting(0);

// Initialization stuff
pre_init();
init_session();
check_prereqs();

// Do an actual upgrade from the CLI
if (PHP_SAPI == 'cli') {
	do_upgrade();
} else {
	header("Location: index.php");
}

// Run a couple functions that will be run on upgrade
function do_upgrade() {
	global $lstr;
	
	// Set installation flags
	set_db_version();
	set_install_version();
}