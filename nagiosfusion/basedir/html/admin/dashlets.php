<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: dashlets.php 78 2010-04-01 21:57:43Z egalstad $

define("SKIPDASHLETS",1);  // skips auto-inclusion of dashlets

require_once(dirname(__FILE__).'/../includes/common.inc.php');

//require_once(dirname(__FILE__).'/../includes/configwizards.inc.php');

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
		
	if(isset($request["download"]))
		do_download();
	else if (isset($request["checkupdates"])){
        do_checkupdates();
        }
    else if (isset($request["upload"]))
		do_upload();
	else if (isset($request["delete"]))
		do_delete();
	else
		show_dashlets();
	
	exit;
	}
	
	
function show_dashlets($error=false,$msg=""){
	global $request;
	global $lstr;
	global $dashlets;
    global $dashlets_api_versions;
	global $cfg;

    $base = grab_array_var($cfg,'root_dir','/usr/local/nagiosfusion');
    $tmp=$base.'/tmp/'; 
    $xmlcache = $tmp.'fusion_dashlets_api_versions.xml'; 
    if(file_exists($xmlcache))
        $dashlets_api_versions = simplexml_load_file($xmlcache);


	
	do_page_start(array("page_title"=>$lstr['ManageDashletsPageTitle']),true);

?>

	
	<h1><?php echo $lstr['ManageDashletsPageHeader'];?></h1>
	

<?php
	display_message($error,false,$msg);
?>

	<?php echo $lstr['ManageDashletsPageNotes'];?>
	
	<br><br>

	
	<?php 
		//echo "INITIAL DASHLETS:<BR>";
		//print_r($dashlets);
	?>
	
	<form enctype="multipart/form-data" action="dashlets.php" method="post">
	
    <div id='rightContainer' style="width: 150px; float: right;">
		<!-- 
        <div class="bluebutton" style="margin-bottom:3px;">
			<a href="http://exchange.nagios.org/directory/Addons/Components" target="_blank"><?php echo gettext("Get Dashlets"); ?></a>
		</div>
        -->
		<div class="bluebutton">
			<a href="?checkupdates=true"><?php echo gettext("Check for Updates"); ?></a>
		</div>
	</div>
    
    <?php echo get_nagios_session_protector();?>
	<input type="hidden" name="upload" value="1">
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
	<label><?php echo $lstr["UploadNewDashletBoxText"];?>:</label><br>
	<input name="uploadedfile" class="textfield"  type="file" />	<input type="submit" class="submitbutton" value="<?php echo $lstr['UploadDashletButton'];?>" />
	</form>
	
	<br>

	<table class="standardtable">
	<thead> 
	<tr><th><?php echo $lstr['DashletNameTableHeader'];?></th><th><?php echo $lstr['ActionsTableHeader'];?></th><th><?php echo $lstr['VersionTableHeader'];?></th><th><?php echo $lstr['StatusTableHeader'];?></th></tr>
	</thead>
	<tbody>
	
<?php

	$x=0;

	// reset the array - only system dashlets should have been in the array at this point
	$dashlets=array();
	reset($dashlets);	
	//echo "<BR>NEW DASHLETS<BR>";
	//print_r($dashlets);
	
	$p=dirname(__FILE__)."/../includes/dashlets/";
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
				//echo "DASHLETS:<BR>";
				//print_r($dashlets);
				
				$dashlet_dir=basename($d);
				
				// display thedashlet
				foreach($dashlets as $name => $darray){
					show_dashlet($dashlet_dir,$name,$darray,$x);
					}
				
				// reset the array
				$dashlets=array();
				reset($dashlets);
				
				$x++;
				}
			}
		}
	
?>
	
	</tbody>
	</table>

<?php

	do_page_end(true);
	exit();
	}
	
	
function show_dashlet($dashlet_dir,$dashlet_name,$darray,$x){
	global $lstr;
    global $dashlets;
    global $dashlets_api_versions;

	$rowclass="";
	
	if(($x%2)!=0)
		$rowclass.=" odd";
	else
		$rowclass.=" even";
    
    // grab variables
	$version=grab_array_var($darray,DASHLET_VERSION,"");
    
	echo "<tr class=".$rowclass.">";
	
	echo "<td>";
	display_dashlet_preview($dashlet_name,$darray);
	//echo $dashlet_name;
	echo "</td>";
	
	
	// nagios session protector
	$nspid=get_nagios_session_protector_id();

	echo "<td>";
	echo "<a href='?download=".$dashlet_dir."&nsp=".$nspid."'><img src='".theme_image("download.png")."' alt='".$lstr['DownloadAlt']."' title='".$lstr['DownloadAlt']."'></a> ";
	echo "<a href='?delete=".$dashlet_dir."&nsp=".$nspid."'><img src='".theme_image("delete.png")."' alt='".$lstr['DeleteAlt']."' title='".$lstr['DeleteAlt']."'></a>";
	echo "</td>";
    echo "<td>";
	if($version!="")
		echo "$version";
	echo "</td>";
    
    if($version!="" && isset($dashlets_api_versions->$dashlet_dir->version)){
        
        if($version<$dashlets_api_versions->$dashlet_dir->version){
            echo "<td style='background-color:#B2FF5F'>";
            echo $dashlets_api_versions->$dashlet_dir->version." Available<br/>";
            if ($dashlets_api_versions->$dashlet_dir->download!="")
                echo"<a href='".$dashlets_api_versions->$dashlet_dir->download."'>Download</a>";
            }
        else{
            echo "<td>";
            echo "Up to date";
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

	$dashlet_dir=grab_request_var("download");
	if(have_value($dashlet_dir)==false)
		show_dashlets();
	
	// clean the name
	$dashlet_dir=str_replace("..","",$dashlet_dir);
	$dashlet_dir=str_replace("/","",$dashlet_dir);
	$dashlet_dir=str_replace("\\","",$dashlet_dir);
	
	$id=submit_command(COMMAND_PACKAGE_DASHLET,$dashlet_dir);
	if($id<=0)
		show_dashlets(true,$lstr['ErrorSubmittingCommandText']);
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
				
					// dashlet was packaged, send it to user
					$dir="/usr/local/nagiosfusion/tmp";
					$thefile=$dir."/dashlet-".$dashlet_dir.".zip";
					
					//chdir($dir);
					
					$mime_type="";
					header('Content-type: '.$mime_type);
					header("Content-length: " . filesize($thefile)); 
					header('Content-Disposition: attachment; filename="'.basename($thefile).'"');
					readfile($thefile); 					
					}
				else
					show_dashlets(true,$lstr['DashletPackagingTimedOutText']);
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
	$dashlet_file=basename($_FILES['uploadedfile']['name']);
	$target_path.="dashlet-".$dashlet_file; 
	
	//echo "TEMP NAME: ".$_FILES['uploadedfile']['tmp_name']."<BR>\n";
	//echo "TARGET: ".$target_path."<BR>\n";

	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)){

		// fix perms
		chmod($target_path,0550);
		chgrp($target_path,"nagios");

		$id=submit_command(COMMAND_INSTALL_DASHLET,$dashlet_file);
		if($id<=0)
			show_dashlets(true,$lstr['ErrorSubmittingCommandText']);
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
					if($result_code==0)
						show_dashlets(false,$lstr['DashletInstalledText']);
					else
						show_dashlets(true,$lstr['DashletInstallFailedText']);
					exit();
					}
				usleep(500000);
				}
			}
		show_dashlets(false,$lstr['DashletScheduledForInstallationText']);
		}
	else{
		// error
		show_dashlets(true,$lstr['DashletUploadFailedText']);
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
		show_dashlets();
		
	$id=submit_command(COMMAND_DELETE_DASHLET,$dir);
	if($id<=0)
		show_dashlets(true,$lstr['ErrorSubmittingCommandText']);
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
				show_dashlets(false,$lstr['DashletDeletedText']);
				exit();
				}
			usleep(500000);
			}
		}
	show_dashlets(false,$lstr['DashletScheduledForDeletionText']);
	exit();
	}

function do_checkupdates(){
    global $cfg;
    
    $base = grab_array_var($cfg,'root_dir','/usr/local/nagiosfusion');
    $tmp=$base.'/tmp/'; 
    $xmlcache = $tmp.'fusion_dashlets_api_versions.xml'; 
    $url="http://api.nagios.com/product_versions/nagiosfusion/fusion_dashlets_api_versions.xml";
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
    if ($getfile && strlen($getfile)>100){
        file_put_contents($xmlcache,$getfile);
        $msg=gettext("Dashlet Versions Updated");
    }
    else{
        $error=true;
        $msg=gettext("Could not download dashlet version list from Nagios Server, check Internet Connnectivity");
    }
    show_dashlets($error,$msg);
}
?>