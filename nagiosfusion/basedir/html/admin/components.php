<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: components.php 172 2010-06-19 11:17:30Z egalstad $

//define("SKIPCOMPONENTS",1);  // skips auto-inclusion of components

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
	global $lstr;
	
	if(in_demo_mode()==true)
		header("Location: main.php");
		
	if(isset($request["download"]))
		do_download();
	else if (isset($request["checkupdates"])){
        do_checkupdates();
        }
    else if (isset($request["upload"]))
		do_upload();
	else if (isset($request["delete"]))
		do_delete();
	else if (isset($request["config"])){
		if(isset($request["cancelButton"]))
			show_components();
		else if(isset($request["update"]))
			do_configure();
		else
			show_configure();
		}
	else if (isset($request["installedok"]))
		show_components(false,$lstr['ComponentInstalledText']);
	else
		show_components();
	
	exit;
	}
	
	
function show_components($error=false,$msg=""){
	global $request;
	global $lstr;
	global $components;
    global $components_api_versions;
	global $cfg;

    $base = grab_array_var($cfg,'root_dir','/usr/local/nagiosfusion');
    $tmp=$base.'/tmp/'; 
    $xmlcache = $tmp.'fusion_components_api_versions.xml'; 
    if(file_exists($xmlcache))
        $components_api_versions = simplexml_load_file($xmlcache);
	
	do_page_start(array("page_title"=>$lstr['ManageComponentsPageTitle']),true);

?>

	
	<h1><?php echo $lstr['ManageComponentsPageHeader'];?></h1>
	

<?php
	display_message($error,false,$msg);
?>

	<?php echo $lstr['ManageComponentsPageNotes'];?>
	
	<br><br>

	
	<?php 
		//echo "INITIAL COMPONENTS:<BR>";
		//print_r($components);
	?>
	
	<form enctype="multipart/form-data" action="components.php" method="post">
    
    <div id='rightContainer' style="width: 150px; float: right;">
		<!-- 
        <div class="bluebutton" style="margin-bottom:3px;">
			<a href="http://exchange.nagios.org/directory/Addons/Components" target="_blank"><?php echo gettext("Get Components"); ?></a>
		</div>
        -->
		<div class="bluebutton">
			<a href="?checkupdates=true"><?php echo gettext("Check for Updates"); ?></a>
		</div>
	</div>
    
	<?php echo get_nagios_session_protector();?>
	<input type="hidden" name="upload" value="1">
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
	<label><?php echo $lstr["UploadNewComponentBoxText"];?>:</label><br>
	<input name="uploadedfile" class="textfield"  type="file" />	<input type="submit" class="submitbutton" value="<?php echo $lstr['UploadComponentButton'];?>" />
	</form>
	
	<br>

	<table class="standardtable">
	<thead> 
	<tr><th><?php echo $lstr['ComponentNameTableHeader'];?></th><th><?php echo $lstr['ComponentTypeTableHeader'];?></th><th><?php echo $lstr['ComponentSettingsTableHeader'];?></th><th><?php echo $lstr['ActionsTableHeader'];?></th><th><?php echo $lstr['VersionTableHeader'];?></th><th><?php echo $lstr['StatusTableHeader'];?></th></tr>
	</thead>
	<tbody>
	
<?php

	$x=0;

	// reset the array
	/*
	$components=array();
	reset($components);	
	
	$p=dirname(__FILE__)."/../includes/components/";
	$subdirs=scandir($p);
	foreach($subdirs as $sd){
	
		if($sd=="." || $sd=="..")
			continue;
			
		$d=$p.$sd;
		
		if(is_dir($d)){
		
			$cf=$d."/$sd.inc.php";
			if(file_exists($cf)){
			
				include_once($cf);
				
				//echo "INCLUDED: $sd<BR>";
				//echo "COMPONENTS:<BR>";
				//print_r($components);
				
				$component_dir=basename($d);
				
				// display the component
				foreach($components as $name => $carray){
					show_component($component_dir,$name,$carray,$x);
					}
				
				// reset the array
				$components=array();
				reset($components);
				
				$x++;
				}
			}
		}
	*/
	
	foreach($components as $name => $carray){
	
		// component may have just been deleted
		if(!file_exists(dirname(__FILE__)."/../includes/components/".$carray[COMPONENT_DIRECTORY]))
			continue;
			
		show_component($carray[COMPONENT_DIRECTORY],$name,$carray[COMPONENT_ARGS],$x);
		
		$x++;
		}
	
?>
	
	</tbody>
	</table>

<?php

	do_page_end(true);
	exit();
	}
	
	
function show_component($component_dir,$component_name,$carray,$x){
	global $lstr;
    global $components_api_versions;

	$rowclass="";
	
	if(($x%2)!=0)
		$rowclass.=" odd";
	else
		$rowclass.=" even";

	// grab variables
	$type=grab_array_var($carray,COMPONENT_TYPE,"");
	$title=grab_array_var($carray,COMPONENT_TITLE,"");
	$desc=grab_array_var($carray,COMPONENT_DESCRIPTION,"");
	$version=grab_array_var($carray,COMPONENT_VERSION,"");
	$date=grab_array_var($carray,COMPONENT_DATE,"");
	$author=grab_array_var($carray,COMPONENT_AUTHOR,"");
	$license=grab_array_var($carray,COMPONENT_LICENSE,"");
	$copyright=grab_array_var($carray,COMPONENT_COPYRIGHT,"");
	$homepage=grab_array_var($carray,COMPONENT_HOMEPAGE,"");

	$configfunc=grab_array_var($carray,COMPONENT_CONFIGFUNCTION,"");
	$protected=grab_array_var($carray,COMPONENT_PROTECTED,false);

	echo "<tr class=".$rowclass.">";
	
	$displaytitle=$component_name;
	if($title!="")
		$displaytitle=$title;
	
	echo "<td>";
	echo "<b>".$displaytitle."</b><br>";
	
	if($desc!="")
		echo $desc."<br>";
	
	if($version!="")
		echo gettext("Version").": $version ";
	if($date!="")
		echo gettext("Date").": $date ";
	if($author!="")
		echo gettext("Author").": $author ";
	if($homepage!="")
		echo gettext("Website").": <a href='$homepage' target='_blank'>$homepage<a/>";
	
	echo "</td>";
	
	echo "<td>";
	switch($type){
		case "core":
			echo gettext("Core");
			break;
		default:
			echo gettext("User");
			break;
		}
	echo "</td>";
	
	// nagios session protector
	$nspid=get_nagios_session_protector_id();
	
	echo "<td>";
	if($configfunc!=""){
		echo "<a href='?config=".$component_dir."&nsp=".$nspid."'><img src='".theme_image("editsettings.png")."' alt=".$lstr['EditSettingsAlt']."' title='".$lstr['EditSettingsAlt']."'></a>";
		}
	else
		echo "-";
	echo "</td>";
	
	echo "<td>";
	if($protected==false){
		echo "<a href='?download=".$component_dir."&nsp=".$nspid."'><img src='".theme_image("download.png")."' alt='".$lstr['DownloadAlt']."' title='".$lstr['DownloadAlt']."'></a> ";
		echo "<a href='?delete=".$component_dir."&nsp=".$nspid."'><img src='".theme_image("delete.png")."' alt='".$lstr['DeleteAlt']."' title='".$lstr['DeleteAlt']."'></a>";
		}
	else
		echo "-";
	echo "</td>";
    echo "<td>";
	if($version!="")
		echo "$version";
	echo "</td>";
    
    if($version!="" && isset($components_api_versions->$component_dir->version)){
        
        if($version<$components_api_versions->$component_dir->version){
            echo "<td style='background-color:#B2FF5F'>";
            echo $components_api_versions->$component_dir->version." Available<br/>";
            if ($components_api_versions->$component_dir->download!="")
                echo"<a href='".$components_api_versions->$component_dir->download."'>Download</a>";
            }
        else{
            echo "<td>";
            echo gettext("Up to date");
            }
        }
    else
        echo "<td>";
	echo "</td>";
	echo "</tr>\n";

	}

	
function do_download(){
	global $cfg;
	global $lstr;

	// check session
	check_nagios_session_protector();

	$component_dir=grab_request_var("download");
	if(have_value($component_dir)==false)
		show_components();
	
	// clean the name
	$component_dir=str_replace("..","",$component_dir);
	$component_dir=str_replace("/","",$component_dir);
	$component_dir=str_replace("\\","",$component_dir);
	
	$id=submit_command(COMMAND_PACKAGE_COMPONENT,$component_dir);
	if($id<=0)
		show_components(true,$lstr['ErrorSubmittingCommandText']);
	else{
		for($x=0;$x<40;$x++){
			$status_code=-1;
			$result_code=-1;
			$args=array(
				"cmd" => "getcommands",
				"command_id" => $id,
				);
			$xml=get_backend_xml_data($args);
			if($xml){
				if($xml->command[0]){
					$status_code=intval($xml->command[0]->status_code);
					$result_code=intval($xml->command[0]->result_code);
					}
				}
			if($status_code==2){
				if($result_code==0){
				
					// wizard was packaged, send it to user
					$dir="/usr/local/nagiosfusion/tmp";
					$thefile=$dir."/component-".$component_dir.".zip";
					
					//chdir($dir);
					
					$mime_type="";
					header('Content-type: '.$mime_type);
					header("Content-length: " . filesize($thefile)); 
					header('Content-Disposition: attachment; filename="'.basename($thefile).'"');
					readfile($thefile); 					
					}
				else
					show_components(true,$lstr['ComponentPackagingTimedOutText']);
				exit();
				}
			usleep(500000);
			}
		}

	exit();
	}
	
	
function do_upload(){
	global $cfg;
	global $lstr;
	global $request;
	
	//print_r($request);
	//exit();
	
	// check session
	check_nagios_session_protector();

	$uploaded_file=grab_request_var("uploadedfile");

	
	$target_path="/usr/local/nagiosfusion/tmp";
	$target_path.="/";
	$component_file=basename($_FILES['uploadedfile']['name']);
	$target_path.="component-".$component_file; 
	
	//echo "TEMP NAME: ".$_FILES['uploadedfile']['tmp_name']."<BR>\n";
	//echo "TARGET: ".$target_path."<BR>\n";

	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)){

		// fix perms
		chmod($target_path,0550);
		chgrp($target_path,"nagios");

		$id=submit_command(COMMAND_INSTALL_COMPONENT,$component_file);
		if($id<=0)
			show_components(true,$lstr['ErrorSubmittingCommandText']);
		else{
			for($x=0;$x<20;$x++){
				$status_code=-1;
				$result_code=-1;
				$args=array(
					"cmd" => "getcommands",
					"command_id" => $id,
					);
				$xml=get_backend_xml_data($args);
				if($xml){
					if($xml->command[0]){
						$status_code=intval($xml->command[0]->status_code);
						$result_code=intval($xml->command[0]->result_code);
						}
					}
				if($status_code==2){
					if($result_code==0){
						// redirect to show install message (so the list will include the new component)
						header("Location: ?installedok");
						exit();
						}
					else
						show_components(true,$lstr['ComponentInstallFailedText']);
					exit();
					}
				usleep(500000);
				}
			}
		show_components(false,$lstr['ComponentScheduledForInstallationText']);
		}
	else{
		// error
		show_components(true,$lstr['ComponentUploadFailedText']);
		}

	exit();
	}

function do_delete(){
	global $cfg;
	global $lstr;
	global $request;
	
	// check session
	check_nagios_session_protector();

	$dir=grab_request_var("delete","");
		
	// clean the filename
	$dir=str_replace("..","",$dir);
	$dir=str_replace("/","",$dir);
	$dir=str_replace("\\","",$dir);
	
	if($dir=="")
		show_components();
		
	$id=submit_command(COMMAND_DELETE_COMPONENT,$dir);
	if($id<=0)
		show_components(true,$lstr['ErrorSubmittingCommandText']);
	else{
		for($x=0;$x<14;$x++){
			$status_code=-1;
			$result_code=-1;
			$args=array(
				"cmd" => "getcommands",
				"command_id" => $id,
				);
			$xml=get_backend_xml_data($args);
			if($xml){
				if($xml->command[0]){
					$status_code=intval($xml->command[0]->status_code);
					$result_code=intval($xml->command[0]->result_code);
					}
				}
			if($status_code==2){
				show_components(false,$lstr['ComponentDeletedText']);
				exit();
				}
			usleep(500000);
			}
		}
	show_components(false,$lstr['ComponentScheduledForDeletionText']);
	exit();
	}
	
	
function show_configure($error=false,$msg=""){
	global $request;
	global $lstr;
	global $components;
	
	$dir=grab_request_var("config","");
		
	// clean the filename
	$dir=str_replace("..","",$dir);
	$dir=str_replace("/","",$dir);
	$dir=str_replace("\\","",$dir);
	
	$component_name=$dir;
	
	if($component_name=="")
		show_components();
		
	$component=$components[$component_name];

	
	$title=grab_array_var($component[COMPONENT_ARGS],COMPONENT_TITLE,"");
	
	do_page_start(array("page_title"=>$lstr['ConfigureComponentPageTitle']." - ".$title),true);

?>

	
	<h1><?php echo $title;?></h1>
	

<?php
	display_message($error,false,$msg);
?>

	<form method="post" action="">
	<?php echo get_nagios_session_protector();?>
	<input type="hidden" name="config" value="<?php echo encode_form_val($component_name);?>">
	<input type="hidden" name="update" value="1">
	
<?php
	// get component output
	$configfunc=grab_array_var($component[COMPONENT_ARGS],COMPONENT_CONFIGFUNCTION,"");
	if($configfunc!=""){
		$inargs=$request;
		$outargs=array();
		$output=$configfunc(COMPONENT_CONFIGMODE_GETSETTINGSHTML,$inargs,$outargs,$result);
		echo $output;
		}
	else
		echo "Component function does not exist.";
	
?>
	
	<div id="formButtons">
	<input type="submit" class="submitbutton" name="submitButton" value="<?php echo $lstr['ApplySettingsButton'];?>"/>
	<input type="submit" class="submitbutton" name="cancelButton" value="<?php echo $lstr['CancelButton'];?>"/>

	<form>

<?php
	}

function do_checkupdates(){
    global $cfg;
    
    $base = grab_array_var($cfg,'root_dir','/usr/local/nagiosfusion');
    $tmp=$base.'/tmp/'; 
    $xmlcache = $tmp.'fusion_components_api_versions.xml'; 
    $url="http://api.nagios.com/product_versions/nagiosfusion/fusion_components_api_versions.xml";
    //use proxy component?
	$proxy=false; 
	if(have_value(get_option('use_proxy')) )
		$proxy = true; 
	
	$options = array(
		'return_info'	=> true,
		'method'	=> 'get',
		'timeout'	=> 10
		);

	// fetch the url
	$result=load_url($url,$options,$proxy);
	$getfile=trim($result["body"]);
    
    
    $error=false;
    $msg="";
    // make sure we succeeded and the file is an appropriate length
    if ($getfile && strlen($getfile)>300){
        file_put_contents($xmlcache,$getfile);
        $msg="Component Versions Updated";
    }
    else{
        $error=true;
        $msg=gettext("Could not download component version list from Nagios Server, check Internet Connnectivity");
    }
    show_components($error,$msg);
}

function do_configure($error=false,$msg=""){
	global $request;
	global $lstr;
	global $components;
	
	// check session
	check_nagios_session_protector();

	$dir=grab_request_var("config","");
		
	// clean the filename
	$dir=str_replace("..","",$dir);
	$dir=str_replace("/","",$dir);
	$dir=str_replace("\\","",$dir);
	
	$component_name=$dir;
	
	if($component_name=="")
		show_components();
		
	$component=$components[$component_name];
		

	// save component settings
	$configfunc=grab_array_var($component[COMPONENT_ARGS],COMPONENT_CONFIGFUNCTION,"");
	if($configfunc!=""){
	
		// pass request vars to component
		$inargs=$request;
		
		// initialize return values
		$outargs=array("test"=> "test2");
		$result=0;
		
		// tell component to save settings
		$output=$configfunc(COMPONENT_CONFIGMODE_SAVESETTINGS,$inargs,$outargs,$result);
		
		// handle errors thrown by component
		if($result!=0)
			show_configure(true,$outargs[COMPONENT_ERROR_MESSAGES]);
			
		// handle success
		else{
			$msg=$lstr['ComponentSettingsUpdatedText'];
			if(array_key_exists(COMPONENT_INFO_MESSAGES,$outargs))
				$msg=$outargs[COMPONENT_INFO_MESSAGES];
			show_configure(false,$msg);
			}
		}
	else
		echo "Component function does not exist.";
	
	exit();
	}

?>