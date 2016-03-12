<?php
//
// Copyright (c) 2008-2015 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: config.inc.php 88 2010-04-12 21:37:21Z egalstad $


// base url
$cfg['base_url']="/nagiosfusion";  // do not include http(s) or host name - this is the base from "http://localhost"

// base root directory where fusion is installed
$cfg['root_dir']="/usr/local/nagiosfusion";

// enable auto-login to remote Nagios servers when possible
$cfg['enable_auto_login']=true;

//default server timeout, connection timeout for remote nagios servers
$cfg['default_timeout']=60;


// default server, db, connection settings
$cfg['dbtype']='';
$cfg['dbserver']='localhost';

// db-specific connection information
$cfg['db_info']=array(
	"nagiosfusion" => array(
		"dbtype" => 'pgsql',
		"dbserver" => 'localhost',
		"user" => 'nagiosfusion',
		"pwd" => 'n@gweb',
		"db" => 'nagiosfusion',
		"dbmaint" => array(		// variables affecting maintenance of db
			"max_commands_age" => 480, // max time (minutes) to keep commands
			"max_events_age" => 480, // max time (minutes) to keep events
			"optimize_interval" => 60, // time (in minutes) between db optimization runs
			"repair_interval" => 120, // time (in minutes) between db repair runs
			),
		),
	);

// db-specific table prefixes
$cfg['db_prefix']=array(
	"nagiosfusion" => "fusion_",	// prefix for fusion tables
	);

$cfg['script_dir'] = "/usr/local/nagiosfusion/scripts";
$cfg['demo_mode'] = false; // is this in demo mode
$cfg['default_language'] = 'en_EN'; // default language
$cfg['default_theme'] = '';	// default theme
$cfg['languages'] = array();

/*********   DO NOT MODIFY ANYTHING BELOW THIS LINE   **********/

$cfg['online_help_url']="http://support.nagios.com/"; // comment this out to disable online help links
$cfg['feedback_url']="http://api.nagios.com/feedback/";
$cfg['privacy_policy_url']="http://www.nagios.com/legal/privacypolicy/";

$cfg['db_version']=113;

$cfg['default_result_records']="100000";

$cfg['subsystem_ticket']="12345";  // default - this gets reset...

///////// keep these in order /////////

// include generic db defs
require_once(dirname(__FILE__).'/includes/db.inc.php');

// include generic  definitions
require_once(dirname(__FILE__).'/db/common.inc.php');
// include db-specific definitions
//require_once(dirname(__FILE__).'/db/'.$cfg['dbtype'].'.inc.php');

?>