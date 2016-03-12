<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: main.php 75 2010-04-01 19:40:08Z egalstad $

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
		do_updateauth();
	else
		show_updateauth();
	exit;
	}
	
	
function show_updateauth($error=false,$msg=""){
	global $request;
	global $lstr;
	
	// get servers list
	$servers=get_servers();
	
	// get saved credentials
	$sc=get_option("server_credentials");
	if($sc==null)
		$sc=array();
	else
		$sc=unserialize($sc);

	
	do_page_start(array("page_title"=>$lstr['ConfigAuthPageTitle']),true);
?>

	<h1><?php echo $lstr['ConfigAuthPageHeader'];?></h1>
	

<?php
	display_message($error,false,$msg);
?>

	<p>
	<?php echo $lstr['ConfigAuthPageNotes'];?>
	</p>
	

	<form id="updateAuthForm" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>?page=<?php echo PAGE_ACCTINFO;?>">
	<?php echo get_nagios_session_protector();?>
	<input type="hidden" name="update" value="1" />
	
	<table id="authTable" class="tablesorter" style="width: 100%;">
	<thead> 
	<tr>
	<th><?php echo gettext("Server"); ?></th>
	<th><?php echo gettext("Server Type"); ?></th>
	<th><?php echo gettext("Display"); ?></th>
	<th><?php echo gettext("Auth Type"); ?></th>
	<th><?php echo gettext("Server Status"); ?></th>
	<th><?php echo gettext("Username"); ?></th>
	<th><?php echo gettext("Password"); ?></th>
	<th><?php echo gettext("Auth Check"); ?></th>
	</tr>
	</thead> 

	<tbody>
<?php

	$x=0;
	
	if(count($servers)==0){
		echo "<tr><td colspan='8'>".gettext("No fused servers have been defined").".";
		if(is_admin())
			echo "  <a href='".get_base_url()."config/?fusionwindow=servers.php' target='_parent'>".gettext("Define fused servers now")."</a>.";
		echo "</td></tr>";
		}

	else foreach($servers as $id => $server){
	
		// defaults
		if(!array_key_exists($id,$sc)){
			$sc[$id]=array(
				"username" => $_SESSION["username"],
				"password" => "",
				"display" => 1,
				);
			}
			
		// added in beta 3
		if(!array_key_exists("display",$sc[$id])){
			if(($x%2)==0)
				$sc[$id]["display"]=0;
			else
			$sc[$id]["display"]=1;
			}
			
	
		$x++;
	
		$checked="";
		$classes="";
		
		if(($x%2)==0)
			$classes.=" even";
		else
			$classes.=" odd";
		
		$oid=$id;
				
		echo "<tr";
		if(have_value($classes))
			echo " class='".$classes."'";
		echo ">";
		echo "<td><a href='".$server["url"]."' target='_blank'>".$server["name"]."</a></td>";
		
		$server_type=get_server_type_from_short_name($server["type"]);
		echo "<td>".$server_type."</td>";
		
		echo "<td><input type='checkbox' name='sc[".$id."][display]' ".is_checked($sc[$id]["display"],"1")."></td>";
		
		echo "<td>".$server["auth"]."</td>";

		$server_result=false;
		$server_output="";
		get_server_state($id,$server_result,$server_output);
		$img="";
		if($server_result==false){
			$img=theme_image('critical_small.png');
			}
		else{
			$img=theme_image('ok_small.png');
			}
		echo "<td><img src='".$img."' alt='".htmlentities($server_output)."' title='".htmlentities($server_output)."'> ".htmlentities($server_output)."</td>";
		
		echo "<td><input type='text' name='sc[".$id."][username]' value='".htmlentities($sc[$id]["username"])."' size='12'></td>";

		$pass=$sc[$id]["password"];
		if(in_demo_mode()==true)
			$pass="********";
		echo "<td><input type='password' name='sc[".$id."][password]' value='".htmlentities($pass)."' size='12'></td>";
		
		$server_result=false;
		$server_output="";
		check_server_auth($id,$sc[$id]["username"],$sc[$id]["password"],$server_result,$server_output);
		$img="";
		if($server_result==false){
			$img=theme_image('critical_small.png');
			}
		else{
			$img=theme_image('ok_small.png');
			}
		echo "<td><img src='".$img."' alt='".htmlentities($server_output)."' title='".htmlentities($server_output)."'> ".htmlentities($server_output)."</td>";
		

		echo "</tr>\n";
		
		}
?>
	
	</tbody>
	</table>
	
	
<?php
	if($x>0){
?>
	<!--<div class="sectionTitle">-->
	<div id="formButtons">
	<input type="submit" class="submitbutton" name="updateButton" value="<?php echo $lstr['UpdateSettingsButton'];?>" id="updateButton" />
	<input type="submit" class="submitbutton" name="cancelButton" value="<?php echo $lstr['CancelButton'];?>" id="cancelButton" />
	</div>
	<!--</div>-->
<?php
		}
?>
	
	<!--</fieldset>-->
	</form>

	

<?php

	do_page_end(true);
	exit();
	}


function do_updateauth(){
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
	$sc=grab_request_var("sc",array());
	
	foreach($sc as $sid => $sinfo){
		$sc[$sid]["display"]=checkbox_binary($sc[$sid]["display"]);
		}
	//print_r($sc);
	
	
	//print_r($sc);	
	//exit();

	// check for errors
	if(in_demo_mode()==true)
		$errmsg[$errors++]=$lstr['DemoModeChangeError'];
		
	
	// handle errors
	if($errors>0)
		show_updateprefs(true,$errmsg);
	
	//$ignore_notice_update=grab_request_var("ignore_notice_update",0);
	//if($ignore_notice_update=="on")
	//	$ignore_notice_update=1;

	// set new prefs
	set_option("server_credentials",serialize($sc));

	// success!
	show_updateauth(false,$lstr['UserPrefsUpdatedText']);
	}
	
	


?>