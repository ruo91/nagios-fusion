<?php
//
// Copyright (c) 2008-2010 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: main.php 75 2010-04-01 19:40:08Z egalstad $

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


// route request
route_request();


function route_request(){
	global $request;

	show_tac();
	
	exit;
	}
	
	
function show_tac($error=false,$msg=""){
	global $request;
	global $lstr;
	
	
	do_page_start(array("page_title"=>$lstr['TacticalOverviewPageTitle']),true);
?>

	<h1><?php echo $lstr['TacticalOverviewPageHeader'];?></h1>
	

<?php
	display_message($error,false,$msg);
?>

<?php

	// get saved credentials
	$sc=get_option("server_credentials");
	if($sc==null)
		$sc=array();
	else
		$sc=unserialize($sc);	

	$servers=get_servers();
	$visible_servers=0;
	foreach($servers as $sid => $sinfo){
	
		// should server be displayed?
		$display_server=grab_array_var($sc[$sid],"display",1);
		if($display_server==0)
			continue;
			
		$visible_servers++;
		
		echo "<div class='servertacoverview servertacoverview-$sid'>";
		$dargs=array(
			DASHLET_ARGS => array(
				"id" => $sid,
				),
			);
		display_dashlet("fusioncore_server_tactical_overview","",$dargs,DASHLET_MODE_OUTBOARD);
		echo "</div>";
				
		}
		
	if(count($servers)==0){
?>
		<p>
		<b><?php echo gettext("Attention"); ?></b>: <?php echo gettext("You have not defined any fused servers yet"); ?>.  
		<a target="_parent" href="<?php echo get_base_url();?>config/?fusionwindow=servers.php"><?php echo gettext("Define some now"); ?></a>.
		</p>
<?php
		}
	else if($visible_servers==0){
?>
		<p>
		<b><?php echo gettext("Attention"); ?></b>: <?php echo gettext("You have chosen not to display any fused servers"); ?>.  
		<a target="_parent" href="<?php echo get_base_url();?>config/?fusionwindow=main.php"><?php echo gettext("Change settings"); ?></a>.
		</p>
<?php
		}
?>

	

<?php

	do_page_end(true);
	exit();
	}


?>