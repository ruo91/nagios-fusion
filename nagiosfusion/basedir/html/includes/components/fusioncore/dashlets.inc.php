<?php
// XI Core Dashlet Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: dashlets.inc.php 218 2012-12-17 20:42:02Z mguthrie $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
include_once(dirname(__FILE__).'/../../utils-dashlets.inc.php');

//include_once(dirname(__FILE__).'/dashlets-comments.inc.php');
//include_once(dirname(__FILE__).'/dashlets-monitoringengine.inc.php');
//include_once(dirname(__FILE__).'/dashlets-perfdata.inc.php');
include_once(dirname(__FILE__).'/dashlets-status.inc.php');
include_once(dirname(__FILE__).'/dashlets-sysstat.inc.php');
include_once(dirname(__FILE__).'/dashlets-tasks.inc.php');
include_once(dirname(__FILE__).'/dashlets-misc.inc.php');

init_fusioncore_dashlets();

////////////////////////////////////////////////////////////////////////
// CORE DASHLET INITIALIZATION
////////////////////////////////////////////////////////////////////////

// initializes all core dashlets
function init_fusioncore_dashlets(){

	// stuff that's common to all core dashlets
	$args=array(
		DASHLET_AUTHOR => "Nagios Enterprises, LLC",
		DASHLET_COPYRIGHT => "Dashlet Copyright &copy; 2009-2010 Nagios Enterprises. All rights reserved.",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		DASHLET_SHOWASAVAILABLE => true,
		);
	
	// Fusion news
	/*
	$args[DASHLET_NAME]="fusioncore_fusion_news_feed";
	$args[DASHLET_TITLE]="Nagios XI News";
	$args[DASHLET_FUNCTION]="fusioncore_dashlet_fusion_news_feed";
	$args[DASHLET_DESCRIPTION]="Shows the latest tutorials, howtos, videos, and news on Nagios Fusion.";
	$args[DASHLET_WIDTH]="350";
	$args[DASHLET_INBOARD_CLASS]="xicore_fusion_news_feed_inboard";
	$args[DASHLET_OUTBOARD_CLASS]="xicore_fusion_news_feed_outboard";
	$args[DASHLET_CLASS]="xicore_fusion_news_feed";
	$args[DASHLET_SHOWASAVAILABLE]=true;
	register_dashlet($args[DASHLET_NAME],$args);
	*/


	// getting started tasks
	$args[DASHLET_NAME]="fusioncore_getting_started";
	$args[DASHLET_TITLE]="Getting Started Guide";
	$args[DASHLET_FUNCTION]="fusioncore_dashlet_getting_started";
	$args[DASHLET_DESCRIPTION]=gettext("Displays helpful information on getting started with Nagios Fusion.");
	$args[DASHLET_WIDTH]="350";
	$args[DASHLET_INBOARD_CLASS]="fusioncore_getting_started_inboard";
	$args[DASHLET_OUTBOARD_CLASS]="fusioncore_getting_started_outboard";
	$args[DASHLET_CLASS]="fusioncore_getting_started";
	$args[DASHLET_SHOWASAVAILABLE]=true;
	register_dashlet($args[DASHLET_NAME],$args);

	// admin tasks
	$args[DASHLET_NAME]="fusioncore_admin_tasks";
	$args[DASHLET_TITLE]="Administrative Tasks";
	$args[DASHLET_FUNCTION]="fusioncore_dashlet_admin_tasks";
	$args[DASHLET_DESCRIPTION]=gettext("Displays tasks that an administrator should take to setup and maintain the Nagios Fusion installation.");
	$args[DASHLET_WIDTH]="350";
	$args[DASHLET_INBOARD_CLASS]="fusioncore_admin_tasks_inboard";
	$args[DASHLET_OUTBOARD_CLASS]="fusioncore_admin_tasks_outboard";
	$args[DASHLET_CLASS]="fusioncore_admin_tasks";
	$args[DASHLET_SHOWASAVAILABLE]=true;
	register_dashlet($args[DASHLET_NAME],$args);


	
	// component status - admin page
	$args[DASHLET_NAME]="fusioncore_component_status";
	$args[DASHLET_TITLE]="Fusion Subsystem Status";
	$args[DASHLET_FUNCTION]="fusioncore_dashlet_component_status";
	$args[DASHLET_DESCRIPTION]=gettext("Displays realtime status of core Fusion subsystem processes.");
	$args[DASHLET_WIDTH]="300";
	$args[DASHLET_INBOARD_CLASS]="fusioncore_component_status_inboard";
	$args[DASHLET_OUTBOARD_CLASS]="fusioncore_component_status_outboard";
	$args[DASHLET_CLASS]="fusioncore_component_status";
	$args[DASHLET_SHOWASAVAILABLE]=true;
	register_dashlet($args[DASHLET_NAME],$args);
	

	
	// server stats - admin page
	$args[DASHLET_NAME]="fusioncore_server_stats";
	$args[DASHLET_TITLE]="Server Stats";
	$args[DASHLET_FUNCTION]="fusioncore_dashlet_server_stats";
	$args[DASHLET_DESCRIPTION]=gettext("Displays realtime statistics of the Fusion server.");
	$args[DASHLET_WIDTH]="300";
	$args[DASHLET_INBOARD_CLASS]="fusioncore_server_stats_inboard";
	$args[DASHLET_OUTBOARD_CLASS]="fusioncore_server_stats_outboard";
	$args[DASHLET_CLASS]="fusioncore_server_stats";
	$args[DASHLET_SHOWASAVAILABLE]=true;
	register_dashlet($args[DASHLET_NAME],$args);
	
	
	// available updates
	$args[DASHLET_NAME]="fusioncore_available_updates";
	$args[DASHLET_TITLE]="Available Updates";
	$args[DASHLET_FUNCTION]="fusioncore_dashlet_available_updates";
	$args[DASHLET_DESCRIPTION]=gettext("Displays the status of available updates for your Nagios Fusion installation.");
	$args[DASHLET_WIDTH]="350";
	$args[DASHLET_HEIGHT]="250";
	$args[DASHLET_INBOARD_CLASS]="fusioncore_available_updates_inboard";
	$args[DASHLET_OUTBOARD_CLASS]="fusioncore_available_updates_outboard";
	$args[DASHLET_CLASS]="fusioncore_available_updates";
	$args[DASHLET_SHOWASAVAILABLE]=true;
	register_dashlet($args[DASHLET_NAME],$args);
	
	// server tac overview
	$args[DASHLET_NAME]="fusioncore_server_tactical_overview";
	$args[DASHLET_TITLE]="Tactical Overview";
	$args[DASHLET_FUNCTION]="fusioncore_server_tactical_overview";
	$args[DASHLET_DESCRIPTION]="";
	$args[DASHLET_WIDTH]="350";
	$args[DASHLET_INBOARD_CLASS]="fusioncore_server_tactical_overview_inboard";
	$args[DASHLET_OUTBOARD_CLASS]="fusioncore_server_tactical_overview_outboard";
	$args[DASHLET_CLASS]="fusioncore_server_tactical_overview";
	$args[DASHLET_SHOWASAVAILABLE]=false;
	register_dashlet($args[DASHLET_NAME],$args);

	}



?>