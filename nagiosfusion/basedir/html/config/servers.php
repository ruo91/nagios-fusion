<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: users.php 75 2010-04-01 19:40:08Z egalstad $

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

	if(isset($request['update']))
		do_update_server();
	else if(isset($request['delete']) || (isset($request['multiButton']) && $request['multiButton']=='delete'))
		do_delete_server();
	else if(isset($request['edit']))
		show_edit_server();
	else
		show_servers();
	exit;
	}


function show_servers($error=false,$msg=""){
	global $request;
	global $lstr;
	global $db_tables;
	global $sqlquery;
	
	$sortby="";
	$sortorder="";
	
	$server_id=grab_request_var("server_id",array());
	
	// get servers list
	$servers=get_servers();
		
	//print_r($servers);
		
	// generage messages...
	if($msg==""){
		if(isset($request["useradded"]))
			$msg=$lstr['ServerAddedText'];
		if(isset($request["userupdated"]))
			$msg=$lstr['ServerUpdatedText'];
		}
	

	do_page_start(array("page_title"=>$lstr['ManageServersPageTitle']),true);

?>
	<h1><?php echo $lstr['ManageServersPageHeader'];?></h1>
	

<?php
	display_message($error,false,$msg);
?>

	<form action="" method="post" id="serverList">
	<input type="hidden" name="sortby" value="<?php echo encode_form_val($sortby);?>">
	<input type="hidden" name="sortorder" value="<?php echo encode_form_val($sortorder);?>">
	<?php echo get_nagios_session_protector();?>
	
	<div id="usersTableContainer" class="tableContainer">

	<div class="tableHeader">

	<div class="tableTopButtons">
	<a href="?servers&amp;edit=1"><img class="tableTopButton" src="<?php echo theme_image("b_addserver.png");?>" border="0" alt="<?php echo $lstr['AddNewServerText'];?>" title="<?php echo $lstr['AddNewServerText'];?>"><?php echo $lstr['AddNewServerText'];?></a>
	<div class="tableListSearch">
	</div><!--table list search -->
	</div><!-- table top buttons -->
	

	<br />
	
	</div><!-- tableHeader -->

	<table id="usersTable" class="tablesorter hovercells" style="width: 100%;">
	<thead> 
	<tr>
	<th><input type='checkbox' name='serverList_checkAll' id='checkall' value='0'></th>
	<th><?php echo gettext("Server"); ?></th>
	<th><?php echo gettext("Type"); ?></th>
	<th><?php echo gettext("Address"); ?></th>
	<th><?php echo gettext("Authentication"); ?></th>
	<th><?php echo gettext("Location"); ?></th>
	<th>URL</th>
	<th><?php echo $lstr['ActionsTableHeader'];?></th>
	</tr>
	
	</thead> 
	<tbody>
<?php

	$x=0;
	
	if(count($servers)==0){
		echo "<tr><td colspan='8'>No servers defined.</td></tr>";
		}

	else foreach($servers as $id => $server){
	
		$x++;
	
		$checked="";
		$classes="";
		
		if(($x%2)==0)
			$classes.=" even";
		else
			$classes.=" odd";
		
		$oid=$id;
		
		if(is_array($server_id)){
			if(in_array($oid,$server_id)){
				$checked="CHECKED";
				$classes.=" selected";
				}
			}
		else if($oid==$server_id){
			$checked="CHECKED";
			$classes.=" selected";
			}
		
		echo "<tr";
		if(have_value($classes))
			echo " class='".$classes."'";
		echo ">";
		echo "<td><input type='checkbox' name='server_id[]' value='".htmlentities($oid)."' id='checkbox_".htmlentities($oid)."' ".$checked."></td>"; 
		echo "<td class='clickable'>".htmlentities($server["name"])."</td>";
		$server_type=get_server_type_from_short_name($server["type"]);
		echo "<td class='clickable'>".htmlentities($server_type)."</td>";
		echo "<td class='clickable'>".htmlentities($server["address"])."</td>";
		echo "<td class='clickable'>".htmlentities($server["auth"])."</td>";
		echo "<td class='clickable'>".htmlentities($server["location"])."</td>";
		echo "<td class='clickable'><a href='".htmlentities($server["url"])."' target='_blank'>".htmlentities($server["url"])."</a></td>";
		echo "<td>";
		echo "<a href='?edit=1&amp;server_id[]=".urlencode($oid)."'><img class='tableItemButton' src='".theme_image("b_edituser.png")."' border='0' alt='".$lstr['EditAlt']."' title='".$lstr['EditAlt']."'></a> ";
		echo "<a href='?delete=1&amp;server_id[]=".urlencode($oid)."&nsp=".get_nagios_session_protector_id()."'><img class='tableItemButton' src='".theme_image("b_deleteuser.png")."' border='0' alt='".$lstr['DeleteAlt']."' title='".$lstr['DeleteAlt']."'></a>";
		echo "</td>";
		echo "</tr>\n";
		
		}
?>
	</tbody>
	</table>
	
	<div class="tableFooter">
	
	<div class="tableListMultiOptions">
	<?php echo $lstr['WithSelectedText'];?> 
	<button class="tableMultiItemButton" title="<?php echo $lstr['DeleteAlt'];?>" value="delete" name="multiButton" type="submit">
	<img class="tableMultiButton" src="<?php echo theme_image("b_delete.png");?>" border="0" alt="<?php echo $lstr['DeleteAlt'];?>" title="<?php echo $lstr['DeleteAlt'];?>">
	</button>
	</div>
	
	<br />
	
	</div><!-- tableFooter -->
	
	</div><!-- tableContainer -->
	
	</form>

<?php

	do_page_end(true);
	exit();
	}


function show_edit_server($error=false,$msg=""){
	global $request;
	global $lstr;
	
	// by default we add a new server
	$add=true;
	$server_id=0;
	
	// get servers list
	$servers=get_servers();
	
	//print_r($servers);
	
	// get options
	$server_id="";
	if(isset($request["server_id"])){
		$server_id=current($request["server_id"]);
		if($server_id!="")
			$add=false;
		}

	// find the server
	$sid=$server_id;
	if($sid=="")
		$sid="-1";
	//echo "SERVER_ID='".$sid."'<BR>";
	$server=grab_array_var($servers,$sid,array(
		"name" => "",
		"address" => "",
		"type" => "",
		"auth" => "",
		"url" => "",
		"internal_url" => "",
		"location" => "",
		"notes" => "",
		));
		
	
	//print_r($server);
		
	
	$name=grab_request_var("name",grab_array_var($server,"name"));
	$address=grab_request_var("address",grab_array_var($server,"address"));
	$type=grab_request_var("type",grab_array_var($server,"type"));
	$auth=grab_request_var("auth",grab_array_var($server,"auth"));
	$url=grab_request_var("url",grab_array_var($server,"url"));
	$internal_url=grab_request_var("internal_url",grab_array_var($server,"internal_url"));
	$location=grab_request_var("location",grab_array_var($server,"location"));
	$notes=grab_request_var("notes",grab_array_var($server,"notes"));
	
	
		
	if($error==false){
		if(isset($request["updated"]))
			$msg=$lstr['ServerUpdatedText'];
		else if(isset($request["added"]))
			$msg=$lstr['ServerAddedText'];
		}
		
		
	// load currentserver info
	if($add==false){
	
		// make sure user exists first
	
		//$username=grab_request_var("username",get_user_attr($user_id,"username"));

		$page_title=$lstr['EditServerPageTitle'];
		$page_header=$lstr['EditServerPageHeader'];
		$button_title=$lstr['UpdateServerButton'];
		}
	else{
		// get defaults to use for new user (or use submitted data)
		//$username=grab_request_var("username","");

		$page_title=$lstr['AddServerPageTitle'];
		$page_header=$lstr['AddServerPageHeader'];
		$button_title=$lstr['AddServerButton'];
		}
		

	do_page_start(array("page_title"=>$page_title),true);

?>
	<h1><?php echo $page_header;?></h1>
	

<?php
	display_message($error,false,$msg);
?>

	<script type='text/javascript'>

		function update_auth() {

			var val = $('#type').val()
			if (val == 'nagiosxi') {
				$('#auth').val('session');
				$('#auth option[value="session"]').attr('disabled', false);
				$('.product').html('<?php echo gettext("Nagios XI"); ?>');
				$('.example').html("http://somewhere/nagiosxi/");
			}

			if (val == 'nagioscore') {
				$('#auth').val('basic');
				$('#auth option[value="session"]').attr('disabled', true);
				$('.product').html('<?php echo gettext("Nagios Core"); ?>');
				$('.example').html("http://somewhere/nagios/");
			}
		}

		$(document).ready(function() {
			update_auth(); 
			$('#type').change(function() {
				update_auth();
			}); 
		}); 
	</script>


	<form id="updateForm" method="post" action="">
	<input type="hidden" name="update" value="1">
	<input type="hidden" name="servers" value="1">
	<input type="hidden" name="server_id[]" value="<?php echo encode_form_val($server_id);?>">
	<?php echo get_nagios_session_protector();?>
	
	<div class="sectionTitle"><?php echo $lstr['UserAccountGeneralSettingsSectionTitle'];?></div>

	<table class="editDataSourceTable">


	<tr>
	<td valign="top">
	<label><?php echo $lstr['ServerNameBoxTitle'];?>:</label><br class="nobr" />
	</td>
	<td>
	<input type="text" size="15" name="name" id="nameBox" value="<?php echo encode_form_val($name);?>" class="textfield" /><br class="nobr" />
	<?php echo gettext("A friendly name associated with this server"); ?>.<br><br>
	</td>
	</tr>

	<tr>
	<td valign="top">
	<label><?php echo $lstr['ServerAddressBoxTitle'];?>:</label><br class="nobr" />
	</td>
	<td>
	<input type="text" size="15" name="address" id="nameBox" value="<?php echo encode_form_val($address);?>" class="textfield" /><br class="nobr" />
	<?php echo gettext("The IP address or FQDNS of the server"); ?>.<br><br>
	</td>
	</tr>

	<tr>
	<td valign="top">
	<label><?php echo $lstr['ServerTypeBoxTitle'];?>:</label><br class="nobr" />
	</td>
	<td>
	<select name="type" class=" dropdown" id="type">
<?php
	$servertypes=get_server_types();
		
	foreach($servertypes as $id => $name){

	echo "<option value='{$id}' ".is_selected($type,$id).">{$name}</option>\n";

		}
?>
	</select><br class="nobr" />
	<?php echo gettext("The type of monitoring server this is"); ?>.<br><br>
	</td>
	</tr>
	
	<tr>
	<td valign="top">
	<label><?php echo $lstr['ServerAuthenticationMethodBoxTitle'];?>:</label><br class="nobr" />
	</td>
	<td>
	<select name="auth" class=" dropdown" id="auth">
<?php
	$servermethods=get_server_authentication_methods();
		
	foreach($servermethods as $id => $name){
?>
	<option value="<?php echo $id;?>" <?php echo is_selected($auth,$id);?>><?php echo $name."</option>\n";?>
<?php
		}
?>
	</select><br class="nobr" />
	<?php echo gettext("The authentication method used to access the server"); ?>.<br><br>
	</td>
	</tr>
	
	<tr>
	<td valign="top">
	<label><?php echo $lstr['PublicURLBoxTitle'];?>:</label><br class="nobr" />
	</td>
	<td>
	<input type="text" size="40" name="url" id="urlBox" value="<?php echo encode_form_val($url);?>" class="textfield" /><br class="nobr" />
	<?php echo gettext("The full URL used to access this"); ?> <span class="product">Nagios XI</span> <?php echo gettext("instance by a client's browser"); ?>.
	<br><?php echo gettext("Example"); ?>: <i class="example">http://somewhere/nagiosxi/</i><br><br>
	</td>
	</tr>

	<tr>
	<td valign="top">
	<label><?php echo $lstr['InternalURLBoxTitle'];?>:</label><br class="nobr" />
	</td>
	<td>
	<input type="text" size="40" name="internal_url" id="internalUrlBox" value="<?php echo encode_form_val($internal_url);?>" class="textfield" /><br class="nobr" />
	<?php echo gettext("The full URL used to access this"); ?> <span class="product">Nagios XI</span> <?php echo gettext("instance from the Nagios Fusion server (if different from the public URL)"); ?>.
	<br><?php echo gettext("Example"); ?>: <i class="example">http://somewhere/nagiosxi/</i><br><br>
	</td>
	</tr>

	<tr>
	<td valign="top">
	<label><?php echo $lstr['ServerLocationBoxTitle'];?>:</label><br class="nobr" />
	</td>
	<td>
	<input type="text" size="25" name="location" value="<?php echo encode_form_val($location);?>" class="textfield" /><br class="nobr" />
	<?php echo gettext("An optional text description of the location of this server"); ?>.<br><br>
	</td>
	</tr>

<tr>
<td valign="top">
<label><?php echo $lstr['ServerNotesBoxTitle'];?>:</label><br class="nobr" />
</td>
<td>
<textarea name="notes" rows="5" cols="40">
<?php echo encode_form_val($notes);?>
</textarea><br>
<?php echo gettext("Optional notes about this server"); ?>.<br><br>
</td></tr>



	</table>
	
	<div id="formButtons">
	<input type="submit" class="submitbutton" name="updateButton" value="<?php echo $button_title;?>" id="updateButton">
	<input type="submit" class="submitbutton" name="cancelButton" value="<?php echo $lstr['CancelButton'];?>" id="cancelButton">
	</div>
	
	<!--</fieldset>-->
	
	</form>

<?php

	do_page_end(true);
	exit();
	}


function do_update_server(){
	global $request;
	global $lstr;
	
	// check session
	check_nagios_session_protector();

	// user pressed the cancel button
	if(isset($request["cancelButton"])){
		show_servers(false,"");
		exit();
		}
	
	$errmsg=array();
	$errors=0;

	$server_id=0;
	$add=true;
	
	// get values
	$server_id=grab_request_var("server_id","");
	$name=grab_request_var("name","");
	$type=grab_request_var("type","");
	$auth=grab_request_var("auth","");
	$address=grab_request_var("address","address");
	$url=grab_request_var("url","");
	$internal_url=grab_request_var("internal_url","");
	$location=grab_request_var("location","");
	$notes=grab_request_var("notes","");
	
	$return=grab_request_var("return","");
	
	if(is_array($server_id))
		$server_id=current($server_id);
	if($server_id!="")
		$add=false;
	
	// check for errors
	if(in_demo_mode()==true)
		$errmsg[$errors++]=$lstr['DemoModeChangeError'];
	if(have_value($name)==false)
		$errmsg[$errors++]="No server name specified.";
	if(have_value($address)==false)
		$errmsg[$errors++]="No server address specified.";
	if(!have_value($url))
		$errmsg[$errors++]="No URL specified.";
	else if(!valid_url($url))
		$errmsg[$errors++]="Invalid URL specified.";
	if(have_value($internal_url) && !valid_url($internal_url))
		$errmsg[$errors++]="Invalid internal URL specified.";
	if(!have_value($type))
		$errmsg[$errors++]="Invalid server type.";
	if(!have_value($auth))
		$errmsg[$errors++]="Invalid authentication method.";
		

	// handle errors
	if($errors>0)
		show_edit_server(true,$errmsg);
		
	//force server address to use trailing backslash 
	if(substr($url,-1)!='/')
		$url=$url.'/'; 	
		
	// add/update server
	$newserver=array(
		"name" => $name,
		"address" => $address,
		"type" => $type,
		"auth" => $auth,
		"url" => $url,
		"internal_url" => $internal_url,
		"location" => $location,
		"notes" => $notes,
		);
		
	//echo "NEW SERVER<BR>";
	//print_r($newserver);
	
	// add/update server
	update_server($newserver,$server_id);
		

	// add server
	if($add==true){
		// success!
		header("Location: ?serveradded");
		}
		
	else{
		// success!
		header("Location: ?serverupdated");
		}
	}


function do_delete_server(){
	global $request;
	global $lstr;
	
	// check session
	check_nagios_session_protector();

	$errmsg=array();
	$errors=0;
	
	// check for errors
	if(in_demo_mode()==true)
		$errmsg[$errors++]=$lstr['DemoModeChangeError'];
	if(!isset($request["server_id"])){
		$errmsg[$errors++]="No server selected.";
		}
	else{
		foreach($request["server_id"] as $server_id){
			}
		}
		
	// handle errors
	if($errors>0)
		show_servers(true,$errmsg);
		
	// delete the servers
	delete_server($request["server_id"]);

	// success!
	$servers=count($request["server_id"]);
	if($servers>1)
		show_servers(false,$servers." ".$lstr['ServersDeletedText']);
	else
		show_servers(false,$lstr['ServerDeletedText']);
	}

	

	
?>