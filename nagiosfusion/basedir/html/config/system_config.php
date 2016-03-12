<?php  //system_config.php 

//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//

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

// Only admins can access this page
if(is_admin() == false){
	echo $lstr['NotAuthorizedErrorText'];
	exit();
}

// route request
route_request();


function route_request(){
	global $request;

	if(isset($request['update']))
		do_updateconfig();
	else
		show_updateconfig();
	exit;
	}
	
	
function show_updateconfig($error=false,$msg=""){
	global $request;
	global $lstr;

	//used if no sessions are active 
	$passive_interval = is_null(get_option('passive_polling_interval')) ? 270 : get_option('passive_polling_interval');
	//use during active web sessions for more current data 
	$active_interval = is_null(get_option('active_polling_interval')) ? 30 : get_option('active_polling_interval');
	
	
	do_page_start(array("page_title"=>'System Configuration'),true);

	
	$html="
	<h1> System Configuration </h1> 

	".display_message($error,false,$msg)."	

	<p>".gettext("Global system settings for Nagios Fusion").".</p>
	
	<div class='container'>
	<form id='updateAuthForm' method='post' action='".htmlentities($_SERVER['PHP_SELF'])."?page=".PAGE_SYSCONFIG."'>
	". get_nagios_session_protector()."
	<input type='hidden' name='update' value='1' />
	<hr />
	<p>".gettext("The active interval is the frequency in seconds which Fusion polls the remote servers for data while actively being used")."</p>
	<label for='active_interval'>".gettext("Active Polling Interval")."</label><br />
	<input type='text' name='active_interval' id='active_interval' size='3' value='{$active_interval}' /> (10-180) ".gettext("seconds")."<br />
	<hr />
	<p>".gettext("The passive interval is the frequency in seconds which Fusion polls the remote servers for data while the Fusion interface is not in use. 
	Some components in Fusion will continue to aquire data for trending purposes").".</p>
	<label for='passive_interval'>".gettext("Passive Polling Interval")."</label><br />
	<input type='text' name='passive_interval' id='passive_interval' size='3' value='{$passive_interval}' /> (30-270) ".gettext("seconds")." <br />
	
			
	<div id='formButtons'>
	<input type='submit' class='submitbutton' name='updateButton' value='".$lstr['UpdateSettingsButton']."' id='updateButton' />
	<input type='submit' class='submitbutton' name='cancelButton' value='".$lstr['CancelButton']."' id='cancelButton' />
	</div>
	
	</form>
	</div> <!-- end main container -->"; 

	print $html; 	
	do_page_end(true);
	exit();
}


function do_updateconfig(){
	global $request;
	global $lstr;

	// check session
	check_nagios_session_protector();

	// user pressed the cancel button
	if(isset($request["cancelButton"]))
		header("Location: main.php");
	
	$errmsg=array();
	$errors=0;
	
	// grab variables
	$active=grab_request_var("active_interval",30);
	$passive=grab_request_var("passive_interval",270); 
	
	
	// handle errors
	if($errors>0)
		show_updateconfig(true,$errmsg);
	

	// set new prefs
	set_option("active_polling_interval",$active);
	set_option("passive_polling_interval",$passive);

	// success!
	show_updateconfig(false,$lstr['UserPrefsUpdatedText']);
	}
	
	


?>