<?php //recentalerts.php

// Copyright (c) 2008-2010 Nagios Enterprises, LLC.  All rights reserved.
//
// 

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
do_page_start(array("page_title"=> gettext('Recent Notifications - Last 2 hours')),true);


display_dashlet('recentalerts',"",null,DASHLET_MODE_OUTBOARD);

do_page_end(true);
exit();



?>