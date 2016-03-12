<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: credentials.php 157 2010-06-11 20:53:39Z egalstad $

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
check_authentication(false);

// only admins can access this page
if(is_admin()==false){
	echo $lstr['NotAuthorizedErrorText'];
	exit();
	}

// route request
route_request();


function route_request(){
	global $request;
	
	if(in_demo_mode()==true)
		header("Location: main.php");
		
	// don't cache credentials, etc.
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache");
	header("Pragma: no-cache");

	if(isset($request['update']))
		do_update_options();
	else
		show_options();
	exit;
	}
	
	
function show_options($error=false,$msg=""){
	global $request;
	global $lstr;
	
	// get options
	//$url=grab_request_var("url",$url);

	do_page_start(array("page_title"=>$lstr['SecurityCredentialsPageTitle']),true);

?>
<?php

	$opt=get_option("security_credentials_updated");
	
	$old_subsystem_ticket=get_subsystem_ticket();
	if($opt==1)
		$subsystem_ticket=$old_subsystem_ticket;
	else
	$subsystem_ticket=random_string(12);
	

?>


	
	<h1><?php echo $lstr['SecurityCredentialsPageTitle'];?></h1>
	

<?php
	display_message($error,false,$msg);
?>

	<?php echo $lstr['SecurityCredentialsPageNotes'];?>


	<form id="manageOptionsForm" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>">
	<?php echo get_nagios_session_protector();?>

	
	<input type="hidden" name="options" value="1">
	<input type="hidden" name="update" value="1">
	

	<div class="sectionTitle"><?php echo $lstr['SubsystemCredentialsSectionTitle'];?></div>
	
	<?php echo $lstr['SubsystemCredentialsNote'];?>
	
	<table>

	<tr>
	<td>
	<label><?php echo $lstr['SubsystemTicketText'];?>:</label><br class="nobr" />
	</td>
	<td>
	<input type="text" size="15" name="subsystem_ticket" id="subsystem_ticket" value="<?php echo $subsystem_ticket;?>" class="textfield" /><!-- (<?php echo $lstr['CurrentText'];?>: <?php echo $old_subsystem_ticket;?>)--><br class="nobr" />
	</td>
	<tr>
		
		
	</table>



	<div id="formButtons">
	<input type="submit" class="submitbutton" name="updateButton" value="<?php echo $lstr['UpdateCredentialsButton'];?>" id="updateButton">
	<input type="submit" class="submitbutton" name="cancelButton" value="<?php echo $lstr['CancelButton'];?>" id="cancelButton">
	</div>
	

	<!--</fieldset>-->
	</form>
	
	


<?php

	do_page_end(true);
	exit();
	}


function do_update_options(){
	global $request;
	global $lstr;

	// check session
	check_nagios_session_protector();

	// user pressed the cancel button
	if(isset($request["cancelButton"]))
		header("Location: main.php");
	
	$errmsg=array();
	$errors=0;

	// get values
	$subsystem_ticket=grab_request_var("subsystem_ticket");

	// make sure we have requirements
	if(in_demo_mode()==true)
		$errmsg[$errors++]=$lstr['DemoModeChangeError'];
	if(have_value($subsystem_ticket)==false)
		$errmsg[$errors++]=$lstr["NoSubsystemTicketError"];

		
	// handle errors
	if($errors>0)
		show_options(true,$errmsg);
		
		
	// UPDATE PASSWORDS/TOKENS...
	
	// backend subsystem ticket
	set_option("subsystem_ticket",$subsystem_ticket);
	
	
	// mark that security credentials were updates
	set_option("security_credentials_updated",1);
		
	// success!
	show_options(false,$lstr['SecurityCredentialsUpdatedText']);
	}
		
		

function draw_menu(){
	//$m=get_admin_menu_items();
	//draw_menu_items($m);
	}
	
	

?>