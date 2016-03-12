<?php
//
// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: index.php 75 2010-04-01 19:40:08Z egalstad $

require_once(dirname(__FILE__).'/includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// Grab GET or POST variables 
grab_request_vars();

// Check prereqs
check_prereqs();

// Check authentication is done in individual pages

route_request_main();

function route_request_main() {
    global $request;

    // Get user's dashboards and add to home menu
    add_user_dashboards_to_menu(MENU_HOME, 201);
    $default_page = PAGE_HOME;

    if (is_authenticated() == false) {
        header("Location: ".get_base_url().PAGEFILE_LOGIN);
    }

    // Actually show the page
    $page = grab_request_var("page", $default_page);
    display_page($page);
}
    
function display_page($page=PAGE_HOME) {

    $filename = dirname(__FILE__).'/includes/page-'.$page.'.php';
    $errorfile = dirname(__FILE__).'/includes/page-missing.php';

    if (file_exists($filename)) {
        include_once($filename);
    } else {
        include_once($errorfile);
    }
}