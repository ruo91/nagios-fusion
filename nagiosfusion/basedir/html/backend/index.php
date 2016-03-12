<?php
// FUSION BACKEND
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: index.php 179 2010-06-22 21:53:04Z egalstad $

require_once(dirname(__FILE__).'/includes/constants.inc.php');  // <--- THIS MUST COME FIRST!
require_once(dirname(__FILE__).'/config-backend.inc.php');
require_once(dirname(__FILE__).'/includes/common.inc.php');


// start session
init_session();

// grab GET or POST variables 
grab_request_vars(false);

// check prereqs
check_backend_prereqs();

// check authentication
check_backend_authentication();

// handle request
route_request();


function route_request(){
	global $request;
	global $page_start_time;
	global $page_end_time;

	// for debugging execution time
	$debug=grab_request_var("debug","");
	if(have_value($debug)){
		// timer info
		$page_start_time=get_timer();
		}

	// get command
	$cmd=strtolower(grab_request_var("cmd",""));
		
	// handle the command
	switch($cmd){
	
	// hello
	case "hello":
		fetch_backend_info();
		break;
		
	// system statistics
	case "getsysstat":
		fetch_sysstat_info();
		break;
		
	// users
	case "getusers":
		fetch_users();
		break;
		
	// command subsystem
	case "submitcommand":
		backend_submit_command();
		break;
	case "getcommands":
		backend_get_command_status();
		break;
		
	// default
	default:
		handle_backend_error("Invalid or no command specified.");
		exit;
		}

	// for debugging execution time
	if(have_value($debug)){
		// timer info
		$page_end_time=get_timer();
		$page_time=get_timer_diff($page_start_time,$page_end_time);
		echo "\n\nFinished in ".$page_time." seconds";
		}
	}



// return some information about XI and the backend	
function fetch_backend_info(){

	output_backend_header();

	echo "<backendinfo>\n";
	echo "  <productinfo>\n";
	xml_field(2,"productname",get_product_name());
	xml_field(2,"productversion",get_product_version());
	xml_field(2,"productbuild",get_product_build());
	echo "  </productinfo>\n";
	echo "  <apis>\n";
	xml_field(2,"backend",get_backend_url());
	echo "  </apis>\n";
	echo "</backendinfo>\n";
	}

	


?>