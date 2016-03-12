<?php
//
// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: main.php 75 2010-04-01 19:40:08Z egalstad $

require_once(dirname(__FILE__).'/../includes/common.inc.php');

// Initialization stuff
pre_init();
init_session();

// grab GET or POST variables 
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication
check_authentication(false);

route_request();

function route_request() {
    global $request;
    
    $pageopt = grab_request_var("pageopt", "info");
    switch ($pageopt) {
        default:
            show_help_page();
            break;
    }
}

function show_missing_feature_page() {
    do_missing_feature_page();
}

function show_help_page() {
    global $lstr;

    do_page_start(array("page_title" => $lstr['HelpPageTitle']), true);
?>
    <h1><?php echo $lstr['HelpPageHeader']; ?></h1>
    <?php display_dashlet("fusioncore_getting_started", "", null, DASHLET_MODE_OUTBOARD); ?>
    
    <div class="sectionTitle"><?php echo $lstr['HelpPageGeneralSectionTitle'];?></div>
    <div style="float: left; margin-right: 15px;">
        <p><?php echo gettext("Get help for Nagios Fusion online."); ?></p>
        <ul>
            <li><a href='http://library.nagios.com/'><b><?php echo gettext("Visit the Nagios Library"); ?></b></a></li>
            <li><a href='http://support.nagios.com/forum'><b><?php echo gettext("Visit the Support Forum"); ?></b></a></li>
            <li><a href='http://support.nagios.com/wiki'><b><?php echo gettext("Visit the Support Wiki"); ?></b></a></li>
        </ul>

    <?php $backend_url = get_product_portal_backend_url(); ?>
    <div class="sectionTitle"><?php echo $lstr['HelpPageMoreOptionsSectionTitle'];?></div>
    <ul>
        <li>
            <a href="<?php echo $backend_url; ?>&opt=learn" target="_blank"><b><?php echo gettext("Learn about Fusion"); ?></b></a><br>
            <?php echo gettext("Learn more about Fusion and its capabilities."); ?>
        </li>
        <li>
            <a href="<?php echo $backend_url; ?>&opt=newsletter" target="_blank"><b><?php echo gettext("Signup for Fusion news"); ?></b></a><br>
            <?php echo gettext("Stay informed of the latest updates and happenings for Fusion."); ?>
        </li>
    </ul>

<?php
    do_page_end(true);
}