<?php
// DASHBOARD DEPLOYMENT TOOL
//
// Copyright (c) 2010-2011 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: eventlog.php 359 2010-10-31 17:08:47Z egalstad $

require_once(dirname(__FILE__).'/../../common.inc.php');

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


route_request();

function route_request(){
	global $request;

	$update=grab_request_var("update");
	
	if($update==1){
		do_deploy_dashboards();
		}
	else
		show_deploy();
	}

function do_deploy_dashboards(){

	global $request;
	global $lstr;
	
	// user pressed the cancel button
	if(isset($request["cancelButton"]))
		header("Location: index.php");
		
	// check session
	check_nagios_session_protector();
	
	$errmsg=array();
	$errors=0;

	// get values
	$dashboards=grab_request_var("dashboards",array());
	$users=grab_request_var("users",array());

	// make sure we have requirements
	if(in_demo_mode()==true)
		$errmsg[$errors++]=$lstr['DemoModeChangeError'];
	$total=0;
	foreach($dashboards as $db){
		$selected=checkbox_binary(grab_array_var($db,"selected",0));
		if($selected==1){
			$total++;
			if($db["desttitle"]=="")
				$errmsg[$errors++]=gettext("Destination name cannot be blank.");
			}
		}
	if($total==0)
		$errmsg[$errors++]=gettext("You must select one or more dashboards to deploy.");
	if(count($users)==0)
		$errmsg[$errors++]=gettext("You must select at least one user.");
		
	// handle errors
	if($errors>0)
		show_deploy(true,$errmsg);
		
	// deploy the dashboards
	foreach($dashboards as $db){
	
		$selected=checkbox_binary(grab_array_var($db,"selected",0));
		if($selected==0)
			continue;
			
		$dbid=grab_array_var($db,"id");
		$keepsynced=checkbox_binary(grab_array_var($db,"keepsynced",0));
	
		$dbopts=array(
			"sourceuser" => $_SESSION["user_id"],
			"sourceid" => $dbid,
			"title" => $db["desttitle"],
			"keepsynced" => $keepsynced,
			);
	
		foreach($users as $uid => $u){
		
			$userid=intval($uid);
			if($userid==0)
				continue;

			deploy_dashboard_to_user($userid,$dbopts);
			}
		}

	// success!
	show_deploy(false,gettext("Dashboards deployed"));	
	}
	
function deploy_dashboard_to_user($destuserid,$dbopts){

	/*
	echo "DEPLOYING TO USER ID: $destuserid<BR>";
	print_r($dbopts);
	echo "<BR>";
	*/
	
	// get options
	$userid=grab_array_var($dbopts,"sourceuser",0);
	$sourceid=grab_array_var($dbopts,"sourceid",0);
	$title=grab_array_var($dbopts,"title","");
	$keepsynced=grab_array_var($dbopts,"keepsynced",0);

	// find source dashboard
	$sourcedashboards=get_dashboards($userid);
	$i=0;
	$source=null;
	foreach($sourcedashboards as $d){
		if($d["id"]==$sourceid){
			//echo "FOUND SOURCE DASHBOARD!<BR>";
			$source=$sourcedashboards[$i];
			break;
			}
		$i++;
		}
	// didn't find the original, so bail
	if($source==null)
		return false;
		
	
	$dest=$source;
		
	// dest gets a new title
	$dest["title"]=$title;
	
	// this gets set later
	//$dest["id"]=;
	
	// set options
	$dest["opts"]["sourceuserid"]=$userid;
	$dest["opts"]["sourceid"]=$sourceid;
	$dest["opts"]["keepsynced"]=$keepsynced;
		
	// get dest dashboards
	$destdashboards=get_dashboards($destuserid);
	
	/*
	echo "DEST DASHBOARDS:<BR>";
	print_r($destdashboards);
	echo "<BR>";
	*/

	$newdashboards=array();
	$did=null;
	//echo "SOURCEID: $sourceid<BR>";
	foreach($destdashboards as $d){
	
		$thedid=grab_array_var($d,"id");
		//echo "THEDID: $thedid<BR>";
		
		// special case for home dashboard
		if($sourceid=="home" && $thedid=="home"){
			$did=$sourcedid;
			//echo "FOUND HOME: $did<BR>";
			continue;
			}
	
		// overwrite/skip dashboards of the same source id (previously deployed dashboards)
		$sid=grab_array_var($d["opts"],"sourceid");
		if($sid==$sourceid){
			// save the id
			$did=grab_array_var($d,"id");
			//echo "FOUND DID: $did<BR>";
			continue;
			}
			
		// otherwise save the dashboard
		$newdashboards[]=$d;
		}
	// use the same id if possible
	if($did==null){
		if($sourceid=="home")
			$did="home";
		else
			$did=random_string(6);
		}
	//echo "NEW DID: $did<BR>";
	$dest["id"]=$did;
	// add new dashboard to the end
	$newdashboards[]=$dest;

	/*
	echo "NEW DASHBOARDS:<BR>";
	print_r($newdashboards);
	echo "<BR>";
	*/

	$dashboardsraw=serialize($newdashboards);
	set_user_meta($destuserid,"dashboards",$dashboardsraw,false);
	return true;

	}
	
function show_deploy($error=false,$msg=""){
	global $request;
	global $lstr;
	
	$dashboards=array();
	$users=array();
	
	$update=grab_request_var("update",0);
	
	if($update==0){
		// initialize dashboard info
		$dbs=get_dashboards(0);
		foreach($dbs as $db){
			$dashboards[]=array(
				"id" => $db["id"],
				"selected" => 0,
				"title" => $db["title"],
				"desttitle" => $db["title"],
				"keepsynced" => 0,
				"readonly" => 0,
				);
			}
		}
	else{
		$dashboards=grab_request_var("dashboards",array());
		$users=grab_request_var("users",array());
		}
	

	// start the HTML page
	do_page_start(array("page_title"=>"Dashboard Deployment Tool"),true);
	
?>
	<h1><?php echo gettext("Dashboard Deployment Tool"); ?></h1>
	
	<p>
	<?php echo gettext("This tool allows admins to deploy one or more of their own dashboards to other users. This can be useful when you design a dashboard that other users may want access to. If you want each user's copy of your dashboard to stay synchronized with your dashboard, select the 'Keep Synced' option."); ?>
	<!-- If you want to prevent users from modify the dashboard, select the "Read-Only" option.-->
	</p>
	<p>
	<strong><?php echo gettext("Note"); ?>:</strong> 
	<?php echo gettext("Re-deploying a dashboard that has already been deployed will overwrite the user's old copy of the deployed dashboard."); ?>
	</p>
 
<?php
	display_message($error,false,$msg);
?>

<?php
	//print_r($dashboards);
?>


	<form action="" method="post">
	<input type="hidden" name="update" value="1">
	<?php echo get_nagios_session_protector();?>
	
	<div class="sectionTitle"><?php echo gettext("Dashboards To Deploy"); ?></div>

	<p>
	<?php echo gettext("Specify which of your dashboards should be deployed."); ?>
	</p>

	<table class="standardtable">
	<thead>
	<tr>
		<th><?php echo gettext("Deploy"); ?></th>
		<th><?php echo gettext("ID"); ?></th>
		<th><?php echo gettext("Local Name"); ?></th>
		<th><?php echo gettext("Destination Name"); ?></th>
		<th><?php echo gettext("Keep Synced"); ?></th>
		<!--<th>Read-Only</th>--></tr>
	</thead>
	<tbody>
<?php
	foreach($dashboards as $db){
	
		$selected=checkbox_binary(grab_array_var($db,"selected","off"));
		$keepsynced=checkbox_binary(grab_array_var($db,"keepsynced","off"));
		$readonly=checkbox_binary(grab_array_var($db,"readonly","off"));
	
		echo "<tr>";
		echo "<input type='hidden' name='dashboards[".$db["id"]."][id]' value='".htmlentities($db["id"])."'>";
		echo "<input type='hidden' name='dashboards[".$db["id"]."][title]' value='".htmlentities($db["title"])."'>";
		echo "<td><input type='checkbox' name='dashboards[".$db["id"]."][selected]' ".is_checked($selected,1)."></td>";
		echo "<td>".$db["id"]."</td>";
		echo "<td>".$db["title"]."</td>";
		echo "<td><input type='text' name='dashboards[".$db["id"]."][desttitle]' value='".htmlentities($db["desttitle"])."'></td>";
		echo "<td><input type='checkbox' name='dashboards[".$db["id"]."][keepsynced]' ".is_checked($keepsynced,1)."></td>";
		//echo "<td><input type='checkbox' name='dashboards[".$db["id"]."][readonly]' ".is_checked($readonly,1)."></td>";
		echo "</tr>";
		}
?>
	</tbody>
	</table>
	
	<div class="sectionTitle"><?php echo gettext("Deploy To Users"); ?></div>
	
	<p>
	<?php echo gettext("Specify which users the dashboards should be deployed to."); ?>
	</p>

	<div style="overflow: auto; width: 275px; height: 80px; border: 1px solid gray; margin: 0 0 0 0px;">
	<?php
	$xml=get_xml_users();
	$username=get_user_attr(0,'username');
	$total_users=0;
	if($xml){
		foreach($xml->user as $user){

			if(!strcmp(strval($user->username),$username))
				continue;
				
			if(array_key_exists(strval($user->attributes()->id),$users))
				$ischecked="CHECKED";
			else
				$ischecked="";
			
			echo "<input type='checkbox' name='users[".$user->attributes()->id."]' ".$ischecked.">".$user->name." (".$user->username.")<br>";
			
			$total_users++;
			}
		}
	if($total_users==0)
		echo "There are no other users defined on the system.";
	?>
	</div>
	
	

	<div id="formButtons">
	<input type="submit" class="submitbutton" name="updateButton" value="Deploy Dashboards" id="updateButton">
	<input type="submit" class="submitbutton" name="cancelButton" value="<?php echo $lstr['CancelButton'];?>" id="cancelButton">
	</div>
	

	</form>
	
<?php		
	
	// closes the HTML page
	do_page_end(true);
	
	exit();
	}
	