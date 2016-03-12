<?php

// Copyright (c) 2008-2010 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: summary.php 75 2010-04-01 19:40:08Z mguthrie $

require_once('../componenthelper.inc.php');

// initialization stuff
pre_init();

// start session
init_session(true);

// grab GET or POST variables 
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication
check_authentication();


show_summary();


function show_summary($error=false,$msg='') {

	//begin html 
	do_page_start(array("page_title"=>'Tactical Summary'),true);
	print "<h1>".gettext("Tactical Summary")."</h1>"; 
	print display_message($error,false,$msg); 
	print "<div class='servertacsummary'>";

	//print "hello world!"; 
	$dargs=array();
	display_dashlet("tacsummary","",$dargs,DASHLET_MODE_OUTBOARD);
	print "</div>";

	do_page_end(true);
	exit();
}




?>