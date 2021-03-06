<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: index.php 120 2010-05-28 19:51:56Z egalstad $

require_once(dirname(__FILE__).'/../includes/common.inc.php');

// initialization stuff
pre_init();

// start session
init_session();

// grab GET or POST variables 
grab_request_vars();

// check prereqs
check_prereqs();

// check authentication
check_authentication();

draw_page();



function draw_page(){
	global $lstr;
	
	$pageopt=grab_request_var("pageopt","");
	
	do_page_start(array("page_title"=>$lstr['AccountInfoPageTitle']),false);
?>
	<div id="leftnav">
	<?php print_menu(MENU_ACCOUNT);?>
	</div>

	<div id="maincontent">
	<div id="maincontentspacer">
	<iframe src="<?php echo get_window_frame_url("main.php");?>" width="100%" frameborder="0" id="maincontentframe" name="maincontentframe">
	[Your user agent does not support frames or is currently configured not to display frames. ]
	</iframe>
	
	<div id="viewtools">
	<div id="popout">
	<a href="#"><img src="<?php echo get_base_url();?>/images/popout.png" border="0" alt="<?php echo $lstr['PopoutAlt'];?>" title="<?php echo $lstr['PopoutAlt'];?>"></a>
	</div>
	</div>
	
	</div>
	</div>
	


<?php	
	do_page_end(false);
	}


?>