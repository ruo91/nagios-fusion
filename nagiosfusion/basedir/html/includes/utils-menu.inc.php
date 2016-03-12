<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// Development Started 03/22/2008
// $Id: utils-menu.inc.php 161 2010-06-13 10:25:50Z egalstad $

//require_once(dirname(__FILE__).'/common.inc.php');

$menus=array();

////////////////////////////////////////////////////////////////////////
// MENU FUNCTIONS
////////////////////////////////////////////////////////////////////////

// deprecated
function draw_menu_items($items){
	$html=get_menu_items_html($items);
	echo $html;
	}
	

function print_menu($menu_name=""){
	global $menus;
	
	// bad menu name
	if(!menu_exists($menu_name))
		return;
		
	// sort menu items
	sort_menu($menu_name);
		
	$html=get_menu_items_html($menus[$menu_name][MENUITEMS]);
	echo $html;
	}

function set_user_menu_preferences($id,$expanded){
	
	$settings_raw=get_user_meta(0,"menu_collapse_options");
	if($settings_raw!="")
		$settings=unserialize(stripslashes($settings_raw));
		
	$settings[$id]=$expanded;
	set_user_meta(0,"menu_collapse_options",serialize($settings),false);

	}	

function menu_exists($menu_name=""){
	global $menus;

	// bad menu name
	if($menu_name=="")
		return false;
	if(!array_key_exists($menu_name,$menus))
		return false;

	return true;
	}
	
	

function add_menu($menu_name){
	global $menus;
	
	// already exists
	if(menu_exists($menu_name))
		return;
		
	// create new menu
	$menus[$menu_name]=array(
		MENUITEMS => array(),
		);
	}
	

function add_menu_item($menu_name,$menu_item){
	global $menus;

	// menu doesn't exist
	if(!menu_exists($menu_name))
		return;
		
	$menus[$menu_name][MENUITEMS][]=$menu_item;
	}
	

function find_menu_item($menu_name,$match,$field="id"){
	global $menus;
	
	// menu doesn't exist
	if(!menu_exists($menu_name))
		return null;
		
	foreach($menus[$menu_name][MENUITEMS] as $index => $arr){
		if(!array_key_exists($field,$arr))
			continue;
		if($arr[$field]==$match)
			return $arr;
		}
		
	return null;
	}
	

function delete_menu_item($menu_name,$match,$field="id"){
	global $menus;
	
	// menu doesn't exist
	if(!menu_exists($menu_name))
		return false;
		
	foreach($menus[$menu_name][MENUITEMS] as $index => $arr){
		if(!array_key_exists($field,$arr))
			continue;
		if($arr[$field]==$match){
			unset($menus[$menu_name][MENUITEMS][$index]);
			return true;
			}
		}
		
	return false;
	}

	

function sort_menu($menu_name){
	global $menus;
	
	// menu doesn't exist
	if(!menu_exists($menu_name))
		return;

	$items=$menus[$menu_name][MENUITEMS];

	//print_r($items);
	
	// obtain a list of sort orders
	$sortorders=array();
	foreach($items as $index => $item){
		// get the items sort order (default to zero if non-existent)
		$sortorder=grab_array_var($item,"order",0);
		$sortorders[$index]=$sortorder;
		}
	
	// sort the items by their sortorder
	array_multisort($sortorders,SORT_ASC,$items);
	
	//print_r($items);
	
	$menus[$menu_name][MENUITEMS]=$items;
	}
	
	
function get_menu_items_html($items){
	$html="";
	foreach($items as $item){

		// some items should only be displayed if a function evaluates to true
		if(array_key_exists("function",$item)){
			$f=$item["function"];
			if($f()!=true)
				continue;
			}

		$menu_id="";
		if(array_key_exists("id",$item))
			$menu_id=$item["id"];
		$title="";
		if(array_key_exists("title",$item))
			$title=$item["title"];
		$opts="";
		if(array_key_exists("opts",$item))
			$opts=$item["opts"];
		
		// use user-defined collapse settings
		$useropts=get_user_meta(0,"menu_collapse_options");

		if($useropts!=""){
			$useropts=unserialize(stripslashes($useropts));

			if(array_key_exists($menu_id,$useropts))
				$opts["expanded"]=$useropts[$menu_id];
		}
			
		$html.=get_menu_item_html($item["type"],$title,$opts,$menu_id);
		}
	return $html;
	}
	

function get_menu_item_html($type,$title,$opts,$menu_id=""){

	$html="";

	switch($type){
	
	// html
	case "html":
		$html.=$opts["html"];
		break;
	
	// menu links
	case "menusection":
		$secondclass="";
		if($opts["expanded"]==false)
			$secondclass="menusection-collapsed";
		if(isset($menu_id))
			$add_menu_id="id='".$menu_id."'";
		$linkopts="";
		if(array_key_exists("linkopts",$opts))
			$linkopts=$opts["linkopts"];
		$target="";
		if(array_key_exists("target",$opts)){
			$t=$opts["target"];
			if(have_value($target))
				$target="target='".$t."'";
			}
		else
			$target="target='maincontentframe'";

		$urla="";
		$urlb="";
		$url="";
		if(array_key_exists("url",$opts))
			$url=$opts["url"];
		if(array_key_exists("href",$opts))
			$url=$opts["href"];
		if($url!=""){
			$urla="<a href='".$opts["url"]."' ".$target." ".$linkopts.">";
			$urlb="</a>";
			}
		$html.="<div class='menusection ".$secondclass."'>";
		$html.="<div class='menusectionbutton' ".$add_menu_id."></div>";
		$html.="<div class='menusectiontitle'>".$urla.$title.$urlb."</div>";
		$ulopts="";
		if(array_key_exists("ulopts",$opts))
			$ulopts=$opts["ulopts"];
		$html.="<ul class='menusection' ".$ulopts.">";
		break;
	case "menusectionend":
		$html.="</ul>";
		$html.="</div>";
		break;
	case "linkspacer":
		$html.="<li class='menulinkspacer'></li>";
		break;
	case "link":
		$xopts="";
		foreach($opts as $var => $val){
			$xopts.=" ".$var."=\"".$val."\"";
			}
		// no target was specified - default it
		if(array_key_exists("target",$opts)==false){
			$xopts.=" target=\"maincontentframe\"";
			}
		// link image
		$img="";
		if(array_key_exists("img",$opts)){
			$img.="<img src='".$opts["img"]."'>";
			}
		$html.="<li class='menulink'><a ".$xopts.">".$img.$title."</a></li>";
		break;
	default:
		break;
		}
		
	return $html;
	}

////////////////////////////////////////////////////////////////////////
// MENU INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function init_menus(){
	global $menus;
	
	// add the menus
	add_menu(MENU_HOME);
	add_menu(MENU_VIEWS);
	add_menu(MENU_DASHBOARDS);
	add_menu(MENU_REPORTS);
	add_menu(MENU_CONFIGURE);
	add_menu(MENU_HELP);
	add_menu(MENU_ADMIN);
	add_menu(MENU_TOOLS);
	add_menu(MENU_ACCOUNT);
	
	// do callbacks
	// components can add top-level menus here
	do_callbacks(CALLBACK_MENUS_DEFINED,$menus);
	
	// initialize menus with menu items
	init_help_menu();
	init_account_menu();
	init_admin_menu();
	init_home_menu();
	init_views_menu();
	init_dashboards_menu();
	init_config_menu();
	
	// do callbacks
	// components can add menu items here
	do_callbacks(CALLBACK_MENUS_INITIALIZED,$menus);

	// do callbacks
	// here we might do final sorting of menus after components modified items in the callback above
	do_callbacks(CALLBACK_MENUS_INITIALIZED_FINAL,$menus);

	}
	

function init_help_menu(){

	add_menu_item(MENU_HELP,array(
		"type" => "menusection",
		"title" => gettext("Help Resources"),
		"id" => "menu-help-section-resources",
		"order" => 100,
		"opts" => array(
			"id" => "help",
			"expanded" => true,
			"url" => "main.php",
			)
		));
		
	add_menu_item(MENU_HELP,array(
		"type" => "link",
		"title" => gettext("FAQs"),
		"id" => "menu-help-faqs",
		"order" => 101,
		"opts" => array(
			"href" => "http://support.nagios.com/wiki/index.php/Nagios_Fusion:FAQs",
			)
		));
	add_menu_item(MENU_HELP,array(
		"type" => "link",
		"title" => gettext("Support Forum"),
		"id" => "menu-help-supportforum",
		"order" => 102,
		"opts" => array(
			"href" => "http://support.nagios.com/forum",
			)
		));
	add_menu_item(MENU_HELP,array(
		"type" => "link",
		"title" => gettext("Nagios Library"),
		"id" => "menu-help-library",
		"order" => 105,
		"opts" => array(
			"href" => "http://library.nagios.com/",
			)
		));
	add_menu_item(MENU_HELP,array(
		"type" => "link",
		"title" => gettext("Support Wiki"),
		"id" => "menu-help-wiki",
		"order" => 101,
		"opts" => array(
			"href" => "http://support.nagios.com/wiki/",
			)
		));
	add_menu_item(MENU_HELP,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-help-sectionend-resources",
		"order" => 110,
		"opts" => ""
		));
		

	
	add_menu_item(MENU_HELP,array(
		"type" => "menusection",
		"title" => gettext("Documentation Guides"),
		"id" => "menu-help-guides",
		"order" => 120,
		"opts" => array(
			"id" => "guides",
			"expanded" => true,
			)
		));
	add_menu_item(MENU_HELP,array(
		"type" => "link",
		"title" => gettext("Administrator Guide"),
		"id" => "menu-help-adminguide",
		"order" => 121,
		"opts" => array(
			"href" => "https://assets.nagios.com/downloads/nagiosfusion/guides/administrator",
			),
		"function" => "is_admin",
		));

	/* No user guide exists -JO
	add_menu_item(MENU_HELP,array(
		"type" => "link",
		"title" => gettext("User Guide"),
		"id" => "menu-help-userguide",
		"order" => 122,
		"opts" => array(
			"href" => "https://assets.nagios.com/downloads/nagiosfusion/guides/user",
			)
		));
	*/

	add_menu_item(MENU_HELP,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-help-sectionend-resources",
		"order" => 125,
		"opts" => ""
		));

	}
	
function init_config_menu() {

	// Servers
	add_menu_item(MENU_CONFIGURE,array(
		"type" => "menusection",
		"title" => gettext("Servers"),
		"id" => "menu-config-section-servers",
		"order" => 100,
		"opts" => array(
			"id" => "servers",
			"expanded" => true,
			"url" => "servers.php",
			)
		));
	add_menu_item(MENU_CONFIGURE,array(
		"type" => "link",
		"title" => gettext("Manage Fused Servers"),
		"id" => "menu-config-manageservers",
		"order" => 101,
		"opts" => array(
			"href" => "servers.php",
			)
		));
	add_menu_item(MENU_CONFIGURE,array(
		"type" => "link",
		"title" => gettext("Fused Server Credentials"),
		"id" => "menu-config-auth",
		"order" => 102,
		"opts" => array(
			"href" => "main.php",
			)
		));
	add_menu_item(MENU_CONFIGURE,array(
		"type" => "menusectionend",
		"id" => "menu-config-sectionend-servers",
		"order" => 150,
		"title" => "",
		"opts" => ""
		));

	// Configuration
	add_menu_item(MENU_CONFIGURE,array(
		"type" => "menusection",
		"title" => gettext("Configuration"),
		"id" => "menu-config-section-resources",
		"order" => 200,
		"opts" => array(
			"id" => "config",
			"expanded" => true,
			"url" => "main.php",
			)
		));

	add_menu_item(MENU_CONFIGURE,array(
		"type" => "link",
		"title" => gettext("System Configuration"),
		"id" => "menu-config-systemconfig",
		"order" => 201,
		"opts" => array(
			"id" => "systemconfig",
			"href" => "system_config.php",
			)
		));
		
	add_menu_item(MENU_CONFIGURE,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-config-sectionend-resources",
		"order" => 250,
		"opts" => ""
		));
	}
	

	
	

function init_account_menu(){

	add_menu_item(MENU_ACCOUNT,array(
		"type" => "menusection",
		"title" => gettext("My Account"),
		"id" => "menu-account-section-myaccount",
		"order" => 100,
		"opts" => array(
			"id" => "myaccountquickview",
			"expanded" => true,
			"url" => "main.php",
			)
		));
		
	add_menu_item(MENU_ACCOUNT,array(
		"type" => "link",
		"title" => gettext("Account Information"),
		"id" => "menu-account-accountinfo",
		"order" => 101,
		"opts" => array(
			"href" => "main.php",
			)
		));
		
	add_menu_item(MENU_ACCOUNT,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-account-sectionend-myaccount",
		"order" => 102,
		"opts" => ""
		));
		
		
	}
	

function init_admin_menu(){

	$base_url=get_base_url();

	// Quick
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusection",
		"title" => gettext("Quick Tools"),
		"id" => "menu-admin-section-quicktools",
		"order" => 100,
		"opts" => array(
			"id" => "quickview",
			"expanded" => true,
			"url" => "main.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Admin Home"),
		"id" => "menu-admin-home",
		"order" => 101,
		"opts" => array(
			"href" => "main.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusectionend",
		"id" => "menu-admin-sectionend-quicktools",
		"order" => 102,
		"title" => "",
		"opts" => ""
		));
				
	// System
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusection",
		"title" => gettext("System Status"),
		"id" => "menu-admin-section-systemstatus",
		"order" => 200,
		"opts" => array(
			"id" => "updates",
			"expanded" => true,
			"url" => "updates.php",
			)
		));
	
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("System Status"),
		"id" => "menu-admin-systemstatus",
		"order" => 202,
		"opts" => array(
			"href" => "sysstat.php",
			)
		));
	
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Check For Updates"),
		"id" => "menu-admin-checkforupdates",
		"order" => 203,
		"opts" => array(
			"href" => "updates.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusectionend",
		"id" => "menu-admin-sectionend-systemstatus",
		"order" => 204,
		"title" => "",
		"opts" => ""
		));

	// Users
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusection",
		"title" => gettext("Users"),
		"id" => "menu-admin-section-users",
		"order" => 300,
		"opts" => array(
			"id" => "users",
			"expanded" => true,
			"url" => "users.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Manage Users"),
		"id" => "menu-admin-manageusers",
		"order" => 301,
		"opts" => array(
			"href" => "users.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Automatic Login"),
		"id" => "menu-admin-autologin",
		"order" => 302,
		"opts" => array(
			"href" => "autologin.php",
			)
		));		
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusectionend",
		"id" => "menu-admin-sectionend-users",
		"order" => 303,
		"title" => "",
		"opts" => ""
		));


	// System Configuration
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusection",
		"title" => gettext("System Config"),
		"id" => "menu-admin-section-systemconfig",
		"order" => 400,
		"opts" => array(
			"id" => "systemconfig",
			"expanded" => true,
			"url" => "globalconfig.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Manage System Config"),
		"id" => "menu-admin-managesystemconfig",
		"order" => 401,
		"opts" => array(
			"href" => "globalconfig.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Manage Email Settings"),
		"id" => "menu-admin-manageemailsettings",
		"order" => 402,
		"opts" => array(
			"href" => "mailsettings.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Reset Security Credentials"),
		"id" => "menu-admin-resetsecuritycredentials",
		"order" => 403,
		"opts" => array(
			"href" => "credentials.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("License Information"),
		"id" => "menu-admin-licenseinformation",
		"order" => 404,
		"opts" => array(
			"href" => "license.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-admin-sectionend-systemconfig",
		"order" => 405,
		"opts" => ""
		));

	// System Extensions
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusection",
		"title" => gettext("System Extensions"),
		"id" => "menu-admin-section-systemextensions",
		"order" => 600,
		"opts" => array(
			"id" => "systemextensions",
			"expanded" => true,
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Manage Components"),
		"id" => "menu-admin-managecomponents",
		"order" => 601,
		"opts" => array(
			"href" => "components.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "link",
		"title" => gettext("Manage Dashlets"),
		"id" => "menu-admin-managedashlets",
		"order" => 602,
		"opts" => array(
			"href" => "dashlets.php",
			)
		));
	add_menu_item(MENU_ADMIN,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-admin-sectionend-systemextensions",
		"order" => 610,
		"opts" => ""
		));

		

	}

function init_home_menu(){
	global $lstr;

	$base_path=get_base_url();
	$includes_path=$base_path."includes/";
	$components_path=$includes_path."components/";

	$fusionstatus_path=$components_path."fusioncore/";

/*		
	// Quick View
	add_menu_item(MENU_HOME,array(
		"type" => "menusection",
		"title" => "Quick View",
		"id" => "menu-home-section-quickview",
		"order" => 100,
		"opts" => array(
			"id" => "quickview",
			"expanded" => true,
			"url" => $base_path."includes/page-home-main.php"
			)
		));
	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => "Home Dashboard",
		"id" => "menu-home-homedashboard",
		"order" => 101,
		"opts" => array(
			"href" => $base_path."includes/page-home-main.php"
			)
		));

	add_menu_item(MENU_HOME,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-home-sectionend-quickview",
		"order" => 181,
		"opts" => ""
		));
	//end quickview 
*/
		
	// My Dashboards
	add_menu_item(MENU_HOME,array(
		"type" => "menusection",
		"title" => gettext("My Dashboards"),
		"id" => "menu-home-section-mydashboards",
		"order" => 200,
		"opts" => array(
			"id" => "homemydashboards",
			"expanded" => true,
			"ulopts" => "id='mydashboardsmenu'",
			"url" => "#",
			"target" => "",
			)
		));


	add_menu_item(MENU_HOME,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-home-sectionend-mydashboards",
		"order" => 299,
		"opts" => ""
		));


///////////////////

		
	// Status
	add_menu_item(MENU_HOME,array(
		"type" => "menusection",
		"title" => gettext("Server Status"),
		"id" => "menu-status-section-start",
		"order" => 300,
		"opts" => array(
			"id" => "servers",
			"expanded" => true,
			"url" => $fusionstatus_path."tac.php"
			)
		));

	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => gettext("Tactical Overview"),
		"id" => "menu-home-tac",
		"order" => 301,
		"opts" => array(
			"href" => $fusionstatus_path."tac.php"
			)
		));

	//tac summary view 
	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => gettext("Tactical Summary"),
		"id" => "menu-home-summary",
		"order" => 305,
		"opts" => array(
			"href" => $fusionstatus_path."summary.php"
			)
		));


	add_menu_item(MENU_HOME,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-home-sectionend-status",
		"order" => 399,
		"opts" => ""
		));

	////////////////////////////////////////////////////////////////////
	// Alerts 
	add_menu_item(MENU_HOME,array(
		"type" => "menusection",
		"title" => gettext("Alerts"),
		"id" => "menu-status-section-start",
		"order" => 400,
		"opts" => array(
			"id" => "alerts",
			"expanded" => true,
			"url" => $fusionstatus_path."recentalerts.php"
			)
		));
	//recent alerts 
	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => gettext("Recent Alerts"),
		"id" => "menu-home-recentalerts",
		"order" => 405,
		"opts" => array(
			"href" => $fusionstatus_path."recentalerts.php"
			)
		));	
	///topalert producers 
	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => gettext("Top Alert Producers"),
		"id" => "menu-home-topalertproducers",
		"order" => 410,
		"opts" => array(
			"href" => $fusionstatus_path."topalertproducers.php"
			)
		));	



	add_menu_item(MENU_HOME,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-home-sectionend-alerts",
		"order" => 499,
		"opts" => ""
		));
	//////////////////////////////////////////////////////////////////
	// Servers
	add_menu_item(MENU_HOME,array(
		"type" => "menusection",
		"title" => "Servers",
		"id" => "menu-servers-section-start",
		"order" => 900,
		"opts" => array(
			"id" => "servers",
			"expanded" => true,
			//"url" => $base_path."includes/page-home-main.php"
			)
		));
	
	register_callback(CALLBACK_AUTHENTICATION_PASSED,'init_servers_menu_list');
	
	/*
	if(is_authenticated()==true){
		$servers=get_servers();
		$x=0;
		foreach($servers as $sid => $sinfo){
		
			$x++;

			add_menu_item(MENU_HOME,array(
				"type" => "link",
				"title" => $sinfo["name"],
				"id" => "menu-server-".$sid,
				"order" => 200+$x,
				"opts" => array(
					"href" => $sinfo["url"],
					)
				));
			}
		}
	*/
		
	add_menu_item(MENU_HOME,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-servers-sectionend",
		"order" => 999,
		"opts" => ""
		));
		
	
	}
	

function init_servers_menu_list($arg=null)
{
	// Check authentication... again?
	if (is_authenticated() == false) { return; }

	// Get saved credentials
	$sc = get_option("server_credentials");
	if ($sc == null) {
		$sc = array();
	} else {
		$sc = unserialize(stripslashes($sc));
	}

	$servers = get_servers();
	$x = 0;
	foreach($servers as $sid => $sinfo){

		//$data = array("username" => $sc[$sid]['username'], "password" => md5($sc[$sid]['password']));
		//$fa_data = base64_encode(serialize($data));
		//$url = $sinfo['url']."?username=".$sc[$sid]['username']."&password=".md5($sc[$sid]['password']);
		
		$url = $sinfo['url'];
	
		// should server be displayed?
		$display_server=grab_array_var($sc[$sid],"display",1);
		if($display_server==0)
			continue;
	
		$x++;

		add_menu_item(MENU_HOME,array(
			"type" => "link",
			"title" => htmlentities($sinfo["name"]),
			"id" => "menu-server-".htmlentities($sid),
			"order" => 900+$x,
			"opts" => array(
				"img" => theme_image("menuredirect.png"),
				"href" => $url,
				"target" => "_blank",
				)
			));
		}
		
	}

	
function init_views_menu(){

	// Quick View
	add_menu_item(MENU_VIEWS,array(
		"type" => "menusection",
		"title" => gettext("View Tools"),
		"id" => "menu-views-section-viewtools",
		"order" => 100,
		"opts" => array(
			"id" => "myviewsquickview",
			"expanded" => true,
			"url" => "#",
			"target" => ""
			)
		));
	add_menu_item(MENU_VIEWS,array(
		"type" => "link",
		"title" => gettext("Rotate Views"),
		"id" => "menu-views-rotateviews",
		"order" => 101,
		"opts" => array(
			"href" => "#",
			"class" => "rotatemyviewslink",
			"target" => ""
			)
		));
	add_menu_item(MENU_VIEWS,array(
		"type" => "linkspacer",
		"order" => 102,
		));
	add_menu_item(MENU_VIEWS,array(
		"type" => "link",
		"title" => gettext("Add New View"),
		"id" => "menu-views-addnewview",
		"order" => 103,
		"opts" => array(
			"href" => "#",
			"class" => "addnewviewlink",
			"target" => ""
			)
		));
	add_menu_item(MENU_VIEWS,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-views-sectionend-viewtools",
		"order" => 104,
		"opts" => ""
		));
		
	// My Views
	add_menu_item(MENU_VIEWS,array(
		"type" => "menusection",
		"title" => gettext("My Views"),
		"id" => "menu-views-section-myviews",
		"order" => 200,
		"opts" => array(
			"id" => "myviews",
			"expanded" => true,
			"ulopts" => "id='myviewsmenu'",
			)
		));
		
	add_menu_item(MENU_VIEWS,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-views-sectionend",
		"order" => 299,
		"opts" => ""
		));
		
	}
	
function init_dashboards_menu(){

	// Quick View
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "menusection",
		"title" => gettext("Dashboard Tools"),
		"id" => "menu-dashboards-section-tools",
		"order" => 100,
		"opts" => array(
			"id" => "mydashboardsquickview",
			"expanded" => true,
			"url" => "#",
			"target" => ""
			)
		));
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "linkspacer",
		"order" => 101,
		));
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "link",
		"title" => gettext("Add New Dashboard"),
		"id" => "menu-dashboards-adddashboard",
		"order" => 110,
		"opts" => array(
			"href" => "#",
			"class" => "addnewdashboardlink",
			"target" => ""
			)
		));
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-dashboards-sectionend-tools",
		"order" => 111,
		"opts" => ""
		));
		
	// My Dashboards
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "menusection",
		"title" => gettext("My Dashboards"),
		"id" => "menu-dashboards-section-mydashboards",
		"order" => 200,
		"opts" => array(
			"id" => "mydashboards",
			"expanded" => true,
			"ulopts" => "id='mydashboardsmenu'",
			"url" => "#",
			"target" => "",
			)
		));
		

	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-dashboards-sectionend-mydashboards",
		"order" => 299,
		"opts" => ""
		));
		
		
	//Dashlets
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "menusection",
		"title" => gettext("Add Dashlets"),
		"id" => "menu-dashboards-section-dashlets",
		"order" => 300,
		"opts" => array(
			"id" => "dashlets",
			"expanded" => false,
			"ulopts" => "id='dashletsmenu'",
			"url" => "dashlets.php",
			)
		));
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "link",
		"title" => gettext("Available Dashlets"),
		"id" => "menu-dashboards-availabledashlets",
		"order" => 301,
		"opts" => array(
			"href" => "dashlets.php",
			)
		));
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "linkspacer",
		"order" => 302,
		"function" => "is_admin",
		));	
	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "link",
		"title" => gettext("Manage Dashlets"),
		"id" => "menu-dashboards-managedashlets",
		"order" => 303,
		"opts" => array(
			"img" => theme_image("menuredirect.png"),
			"href" => get_base_url()."admin/?fusionwindow=dashlets.php",
			"target" => "_top",
			),
		"function" => "is_admin",
		));

	add_menu_item(MENU_DASHBOARDS,array(
		"type" => "menusectionend",
		"title" => "",
		"id" => "menu-dashboards-sectionend-dashlets",
		"order" => 304,
		"opts" => ""
		));

	}
	
?>