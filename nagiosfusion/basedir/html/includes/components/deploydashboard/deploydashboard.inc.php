<?php
// Dashboard Deployment Component
//
// Copyright (c) 2010-2011 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: deploydashboard.inc.php 197 2010-12-01 16:34:55Z tyarusso $

require_once(dirname(__FILE__).'/../componenthelper.inc.php');


// respect the name
$deploydashboard_component_name="deploydashboard";

// run the initialization function
deploydashboard_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function deploydashboard_component_init(){
	global $deploydashboard_component_name;
	
	$versionok=deploydashboard_component_checkversion();
	
	$desc="";
	if(!$versionok)
		$desc="<br><b>".gettext("Error: This component requires Nagios Fusion 2012R1.2 or later.")."</b>";

		
	$args=array(

		// need a name
		COMPONENT_NAME => $deploydashboard_component_name,
		COMPONENT_VERSION => '1.0',
		// informative information
		COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
		COMPONENT_DESCRIPTION => gettext("Allows admins to deploy dashboards to other users. ").$desc,
		COMPONENT_TITLE => "Dashboard Deployment Tool",
		COMPONENT_PROTECTED => true,
		COMPONENT_TYPE => COMPONENT_TYPE_CORE,		
		// configuration function (optional)
		//COMPONENT_CONFIGFUNCTION => "deploydashboard_component_config_func",
		);
		
	register_component($deploydashboard_component_name,$args);
	
	if($versionok){
		// add a menu link
		register_callback(CALLBACK_MENUS_INITIALIZED,'deploydashboard_component_addmenu');
		}
	}
	

///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

function deploydashboard_component_checkversion(){

	if(!function_exists('get_product_release'))
		return false;
	//requires greater than 2009R1.4B
	if(get_product_release()<202)
		return false;

	return true;
	}
	
	
function deploydashboard_component_addmenu($arg=null){
	global $deploydashboard_component_name;
	
	$url=get_component_url_base($deploydashboard_component_name);

	// do we need to add a new menu section?
	
	//$mi=find_menu_item(MENU_DASHBOARDS,"menu-dashboards-adddashboard","id");

	
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "link",
		"title" => gettext("Deploy Dashboards"),
		"id" => "menu-dashboards-deploydashboards",
		"order" => 109,
		"opts" => array(
			"href" => $url,
			),
		"function" => "is_admin"			
		));

	}

?>