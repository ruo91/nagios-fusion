<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: page-home-main.php 75 2010-04-01 19:40:08Z egalstad $

require_once(dirname(__FILE__).'/common.inc.php');

// initialization stuff
pre_init();

// start session
init_session();

// grab GET or POST variables 
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication
check_authentication(false);

do_page();


function do_page(){

	do_page_start(
		array(
			"body_id" => "dashboard-home",
			),
		true
		);
?>

<h1>Nagios Fusion</h1>

<?php
	// show the homepage dashboard
	$homedash=get_dashboard_by_id(0,HOMEPAGE_DASHBOARD_ID);
	//print_r($homedash);
	display_dashboard_dashlets($homedash);
	
?>

<?php	
	do_page_end(true);
	}
?>
