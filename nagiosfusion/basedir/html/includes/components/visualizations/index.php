<?php
//
// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//

require_once('../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session(true);

// grab GET or POST variables 
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication
check_authentication();

include_once('column-grouped-stacked.inc.php');
include_once('line-basic.inc.php');

route_request();

function route_request() {

    $mode = grab_request_var('mode','default');

    switch($mode)
    {
        case 'api': // Graph only, render as JSON 
            fetch_graph();
            break;

        default: // Render as html 
            display_default_page();
            break;
    }
}

function display_default_page() {

    $graphtype = grab_request_var('graphtype', 'bar'); //graph template type 
    $objecttype = grab_request_var('objecttype', 'host');
    $div = grab_request_var('div', 'container');     

    // Start page html
    do_page_start(array("page_title" => 'Tactical Summary'), true);
    echo "<div id='container'></div>";

    $args = array('objecttype' => $objecttype, 'graphtype' => $graphtype, 'div' => $div);

    if ($graphtype == 'bar' && $objecttype == 'host')
        display_dashlet('visualization_hosthealth', '', $args, DASHLET_MODE_OUTBOARD);
    if ($graphtype == 'bar' && $objecttype == 'service')
        display_dashlet('visualization_servicehealth', '', $args, DASHLET_MODE_OUTBOARD);
    if ($graphtype == "line")
        display_dashlet('visualization_alert_histogram', '', $args, DASHLET_MODE_OUTBOARD);

    do_page_end(true);
    exit();
}

function fetch_graph() {
    $graphtype = grab_request_var('graphtype', 'bar');
    
    switch($graphtype)
    {
        case 'line':
            $content = fetch_line();
            break;

        default: // Bar
            $content = fetch_bar();
            break;

    }

    print $content;
}