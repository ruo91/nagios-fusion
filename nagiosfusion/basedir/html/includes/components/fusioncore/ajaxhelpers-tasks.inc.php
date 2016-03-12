<?php
// Fusion Core Ajax Helper Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: ajaxhelpers-tasks.inc.php 79 2010-04-02 16:49:54Z egalstad $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
	

////////////////////////////////////////////////////////////////////////
// TASK AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////
	
function fusioncore_ajax_get_admin_tasks_html($args=null){
	global $lstr;
	
	$output='';

	if(is_admin()==false)
		return $lstr['NotAuthorizedErrorText'];

	else{
		$output.='<div class="infotable_title">'.gettext('Administrative Tasks').'</div>';

		$output.='
		<table class="infotable">
		<thead>
		<tr><th>'.gettext('Task').'</th></tr>
		</thead>
		<tbody>
		';
		
		$base_url=get_base_url();
		$admin_base=$base_url."admin/";
		$config_base=$base_url."config/";
		
		// check for setup tasks that need to be done
		$setupoutput="";
		
		$opt=get_option("system_settings_configured");
		if($opt!=1)
			$setupoutput.="<li><a href='".$admin_base."?fusionwindow=globalconfig.php' target='_top'>
				<b>".gettext("Configure system settings")."</b></a><br>".gettext("Configure basic settings for your Fusion system").".</li>";
		
		$opt=get_option("security_credentials_updated");
		if($opt!=1)
			$setupoutput.="<li><a href='".$admin_base."?fusionwindow=credentials.php' target='_top'><b>".gettext("Reset security credentials")."</b></a>
							<br>".gettext("Change the default credentials used by the Fusion system").".</li>";
			
		$opt=get_option("mail_settings_configured");
		if($opt!=1)
			$setupoutput.="<li><a href='".$admin_base."?fusionwindow=mailsettings.php' target='_top'>
				<b>".gettext("Configure mail settings")."</b></a><br>".gettext("Configure email settings for your Fusion system").".</li>";
			
		$servers=get_servers();
		if(count($servers)==0)
			$setupoutput.="<li><a href='".$config_base."?fusionwindow=servers.php' target='_top'><b>".gettext("Define fused servers")."</b></a>
							<br>".gettext("Configure servers that should be integrated into Fusion").".</li>";
			
		// get saved credentials
		$sc=get_option("server_credentials");
		if($sc==null)
			$sc=array();
		else
			$sc=unserialize($sc);
		if(count($sc)==0)
			$setupoutput.="<li><a href='".$config_base."?fusionwindow=main.php' target='_top'><b>".gettext("Define server credentials")."</b></a>
					<br>".gettext("Define credentials used to authenticated to fused servers").".</li>";
		
		
		if($setupoutput!=""){
			$output.="<tr><td><span class='infotable_subtitle'>".gettext("Initial Setup Tasks").":</span></td></tr>";
			$output.="<tr><td>";
			$output.="<ul>";
			$output.=$setupoutput;
			$output.="</ul>";
			$output.="</td></tr>";
		}

		// check for important tasks that need to be done
		$alertoutput="";

		$update_info=array(
			"last_update_check_succeeded" => get_option("last_update_check_succeeded"),
			"update_available" => get_option("update_available"),
			);
		$updateurl=get_base_url()."admin/?fusionwindow=updates.php";
		if($update_info["last_update_check_succeeded"]==0){
			$alertoutput.="<li><div style='float: left; margin-right: 5px;'><img src='".theme_image("unknown_small.png")."'></div>
							".gettext("The last")." <a href='".$updateurl."' target='_top'>".gettext("update check failed")."</a>.</li>";
			}
		else if($update_info["update_available"]==1){
			$alertoutput.="<li><div style='float: left; margin-right: 5px;'><img src='".theme_image("critical_small.png")."'></div>
						".gettext("A new Nagios Fusion")." <a href='".$updateurl."' target='_top'>".gettext("update is available")."</a>.</li>";
		
			}
		
			

		if($alertoutput!=""){
			$output.="<tr><td><span class='infotable_subtitle'>".gettext("Important Tasks").":</span></td></tr>";
			$output.="<tr><td>";
			$output.="<ul>";
			$output.=$alertoutput;
			$output.="</ul>";
			$output.="</td></tr>";
		}

		$output.="<tr><td><span class='infotable_subtitle'>".gettext("Ongoing Tasks").":</span></td></tr>";
		$output.="<tr><td>";
		$output.="<ul>";
		$output.="<li><a href='".$config_base."?fusionwindow=servers.php' target='_top'>".gettext("Configure your fused servers")."</a><br>".gettext("Add or modify servers to be fused").".</li>";
		$output.="<li><a href='".$admin_base."?fusionwindow=users.php' target='_top'>".gettext("Add new user accounts")."</a><br>".gettext("Setup new users with access to Nagios Fusion").".</li>";
		$output.="</ul>";
		$output.="</td></tr>";
		
		$output.='
		</tbody>
		</table>
		';
		}
		
	$output.='
	<div class="ajax_date">Last Updated: '.get_datetime_string(time()).'</div>
	';
	
	return $output;
	}
	
	
	
function fusioncore_ajax_get_getting_started_html($args=null){
	global $lstr;
	
	$output='';

	$output.='<div class="infotable_title">'.gettext('Getting Started Guide').'</div>';

	$output.='
	<table class="infotable">
	<thead>
	<tr><th>&nbsp;</th></tr>
	</thead>
	<tbody>
	';
		
	$base_url=get_base_url();
	$account_base=$base_url."account/";
	$config_base=$base_url."config/";
	
	$backend_url=get_product_portal_backend_url();
	
	$setupoutput="";

	// check admin stuff
	if(is_admin()){
		$admin_base=$base_url."admin/";
		$opt=get_option("system_settings_configured");
		if($opt!=1)
			$setupoutput.="<li><a href='".$admin_base."' target='_top'><b>".gettext("Tend to pending administrative tasks")."</b>
					</a><br>".gettext("Configure initial Fusion settings").".</li>";
		}
	
	// get saved credentials
	$sc=get_option("server_credentials");
	if($sc==null)
		$sc=array();
	else
		$sc=unserialize($sc);
	if(count($sc)==0)
		$setupoutput.="<li><a href='".$config_base."?fusionwindow=main.php' target='_top'><b>".gettext("Define server credentials")."</b>
					</a><br>".gettext("Define credentials used to authenticated to fused servers").".</li>";
		
	if($setupoutput!=""){
		$output.="<tr><td><span class='infotable_subtitle'>".gettext("Initial Setup Tasks").":</span></td></tr>";
		$output.="<tr><td>";
		$output.="<ul>";
		$output.=$setupoutput;
		$output.="</ul>";
		$output.="</td></tr>";
	}
		
	$output.="<tr><td><span class='infotable_subtitle'>".gettext("Common Tasks").":</span></td></tr>";
	$output.="<tr><td>";
	$output.="<ul>";
	$output.="<li><a href='".$account_base."' target='_top'>".gettext("Change your account settings")."</a><br>".gettext("Change your account password and general preferences").".</li>";
	$output.="</ul>";
	$output.="</td></tr>";
	
	$output.="<tr><td><span class='infotable_subtitle'>".gettext("Getting Started").":</span></td></tr>";
	$output.="<tr><td>";
	$output.="<ul>";
	$output.="<li><a href='".$backend_url."&opt=learn' target='_blank'><b>".gettext("Learn about Fusion")."</b></a><br>".gettext("Learn more about Fusion and its capabilities").".</li>";
	$output.="<li><a href='".$backend_url."&opt=newsletter' target='_blank'><b>".gettext("Signup for Fusion news")."</b></a><br>".gettext("Stay informed of the latest updates and happenings for Fusion").".</li>";
	$output.="</ul>";
	$output.="</td></tr>";

	
	$output.='
	</tbody>
	</table>
	';
		
	$output.='
	<div class="ajax_date">'.gettext('Last Updated').': '.get_datetime_string(time()).'</div>
	';
	
	return $output;
	}
	
	
	
?>