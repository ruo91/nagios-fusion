<?php  //topalertsproducers.php
// Top Alert Producers Component
//
// Copyright (c) 2010 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: topalertproducers.inc.php 155 2010-11-06 02:36:00Z egalstad $

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


//begin html 
do_page_start(array("page_title"=>'Top Alert Producers'),true);


display_dashlet('topalertproducers',"",null,DASHLET_MODE_OUTBOARD);

do_page_end(true);
exit();


?>