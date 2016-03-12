<?php 
// Visializations Component
//
// Copyright (c) 2014 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: visualizations.inc.php 155 2010-11-06 02:36:00Z egalstad $

require_once(dirname(__FILE__).'/../componenthelper.inc.php');
//include_once(dirname(__FILE__).'/../../dashlethelper.inc.php');
include_once('/usr/local/nagiosfusion/html/includes/dashlets/dashlethelper.inc.php'); 
include_once(dirname(__FILE__).'/dashlets-visualizations.inc.php');
include_once(dirname(__FILE__).'/column-grouped-stacked.inc.php');
include_once(dirname(__FILE__).'/line-basic.inc.php');

// respect the name
$visualizations_component_name="visualizations";

// run the initialization function
visualizations_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function visualizations_component_init(){
	global $visualizations_component_name;
	
	$versionok=visualizations_component_checkversion();
	
	$desc=gettext("Fusion Visualizations is an interactive graphing tool for your Nagios data.
			For most reliable graphs and dashlets, use Mozilla Firefox.");
	if(!$versionok)
		$desc="<b>Error: ".gettext("This component requires Nagios Fusion 2012r1.0 or later").".</b>";
	
	$args=array(

		// need a name
		COMPONENT_NAME => $visualizations_component_name,
		
		// informative information
		COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
		COMPONENT_TYPE => COMPONENT_TYPE_CORE,
		COMPONENT_DESCRIPTION => $desc,
		COMPONENT_TITLE => "Nagios Visualizations",
		COMPONENT_DATE => '05/15/2012',
		COMPONENT_VERSION => '1.1',
		// configuration function (optional)
		//COMPONENT_CONFIGFUNCTION => "visualizations_component_config_func",
		);
		
	register_component($visualizations_component_name,$args);
	
	// add a menu link
//	if($versionok)
	register_callback(CALLBACK_MENUS_INITIALIZED,'visualizations_component_addmenu');
		
	// register dashlets
	register_callback(CALLBACK_MENUS_INITIALIZED,'register_visualization_dashlets'); 

}
	



///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

function visualizations_component_checkversion(){

	if(!function_exists('get_product_release'))
		return false;
	//requires greater than 2009R1.4B
	if(get_product_release()<200)
		return false;

	return true;
	}
	
function visualizations_component_addmenu($arg=null){
	global $visualizations_component_name;
	
		
	add_menu_item(MENU_HOME,array(
		"type" => "menusection",
		"title" => gettext("Visualizations"),
		"id" => "menu-status-section-visualizations",
		"order" => 500,
		"opts" => array(
			"id" => "alerts",
			"expanded" => true,
			"url" => get_base_url().'includes/components/visualizations/index.php?&graphtype=bar&objecttype=host',
			)
		));
	
	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => gettext("Host Health"),
		"id" => "menu-home-hosthealth",
		"order" => 501,
		"opts" => array(
			"href" => get_base_url().'includes/components/visualizations/index.php?&graphtype=bar&objecttype=host',
			//"target" => "_blank",  
			)
		));

	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => gettext("Service Health"),
		"id" => "menu-home-servicehealth",
		"order" => 502,
		"opts" => array(
			"href" => get_base_url().'includes/components/visualizations/index.php?&graphtype=bar&objecttype=service',
			//"target" => "_blank",  
			)
		));

	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => gettext("Alert Histogram"),
		"id" => "menu-home-histogram",
		"order" => 503,
		"opts" => array(
			"href" => get_base_url().'includes/components/visualizations/index.php?graphtype=line',
			//"target" => "_blank",  
			)
		));

	add_menu_item(MENU_HOME,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-home-sectionend-alerts",
		"order" => 599,
		"opts" => ""
		));
	
	}


function visualizations_component_get_baseurl(){
	return	get_base_url().'includes/components/visualizations/';
}


function register_visualization_dashlets ($cbtype='',$args=null) {

	//host health
	$args=array();
	$args[DASHLET_NAME]="visualization_hosthealth";
	$args[DASHLET_TITLE]="Host Health";
	$args[DASHLET_FUNCTION]="host_health_dashlet_func";
	$args[DASHLET_DESCRIPTION]=gettext("Displays a column graph of host health across all fused servers");
	$args[DASHLET_WIDTH]="700";
	$args[DASHLET_HEIGHT]="300";
	$args[DASHLET_INBOARD_CLASS]="visualizations_map_inboard";
	$args[DASHLET_OUTBOARD_CLASS]="visualizations_map_outboard";
	$args[DASHLET_CLASS]="hosthealth";
	$args[DASHLET_AUTHOR]="Mike Guthrie. Nagios Enterprises, LLC";
	$args[DASHLET_COPYRIGHT]=gettext("Dashlet Copyright &copy; 2012 Nagios Enterprises. All rights reserved.");
	$args[DASHLET_HOMEPAGE]="http://www.nagios.com";
	$args[DASHLET_SHOWASAVAILABLE]=true;
	register_dashlet($args[DASHLET_NAME],$args);

	//service health
	$args[DASHLET_NAME]="visualization_servicehealth";
	$args[DASHLET_TITLE]="Service Health";
	$args[DASHLET_FUNCTION]="service_health_dashlet_func";
	$args[DASHLET_DESCRIPTION]=gettext("Displays a column graph of service health across all fused servers");
	$args[DASHLET_CLASS]="servicehealth";
	register_dashlet($args[DASHLET_NAME],$args);


	//alert histogram
	$args[DASHLET_NAME]="visualization_alert_histogram";
	$args[DASHLET_TITLE]="Alert Histogram";
	$args[DASHLET_FUNCTION]="alert_histogram_dashlet_func";
	$args[DASHLET_DESCRIPTION]=gettext("Displays a histogram of alerts across all fused servers");
	$args[DASHLET_CLASS]="alerthistogram";
	register_dashlet($args[DASHLET_NAME],$args);

	//top alert producers 

}

	
?>