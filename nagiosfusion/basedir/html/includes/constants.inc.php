<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: constants.inc.php 175 2010-06-20 16:30:45Z egalstad $



// CONSTANTS

//used for development 
define('SQLDEBUG',true); 

// DATABASES
define("DB_NAGIOSFUSION","nagiosfusion");


// PAGES
define("PAGE_LOGIN","auth");
define("PAGE_HOME","home");
define("PAGE_MYVIEWS","myviews");
define("PAGE_MYDASHBOARDS","mydashboards");
define("PAGE_CONFIG","config");
define("PAGE_SYSCONFIG","system_config.php");
define("PAGE_ADMIN","admin");
define("PAGE_ACCTINFO","acctinfo");

// PAGE FILES
define("PAGEFILE_AJAXHELPER","ajaxhelper.php");
define("PAGEFILE_AJAXPROXY","ajaxproxy.php");
define("PAGEFILE_SUGGEST","suggest.php");
define("PAGEFILE_LOGIN","login.php");
define("PAGEFILE_INSTALL","install.php");
define("PAGEFILE_UPGRADE","upgrade.php");

// PAGE OPTIONS
define("PAGEOPT_LOGIN","login");
define("PAGEOPT_LOGOUT","logout");

// COMMON COLORS
define("COMMONCOLOR_GREEN","#5CDF45");
define("COMMONCOLOR_YELLOW","#FEFF6F");
define("COMMONCOLOR_ORANGE","#FFBD6F");
define("COMMONCOLOR_RED","#FF795F");
define("COMMONCOLOR_BLUE","#5FB7FF");


// COMPONENT DEFINITIONS
define("COMPONENT_API_VERSION",2); // API version
define("COMPONENT_NAME","name");
define("COMPONENT_DIRECTORY","directory");
define("COMPONENT_ARGS","args");

define("COMPONENT_PROTECTED","protected");
define("COMPONENT_TYPE","type");
define("COMPONENT_TITLE","title");
define("COMPONENT_VERSION","version");
define("COMPONENT_AUTHOR","author");
define("COMPONENT_DESCRIPTION","description");
define("COMPONENT_DATE","date");
define("COMPONENT_COPYRIGHT","copyright");
define("COMPONENT_LICENSE","license");
define("COMPONENT_HOMEPAGE","homepage");
define("COMPONENT_CONFIGFUNCTION","configfunc");

define("COMPONENT_TYPE_CORE","core");
define("COMPONENT_TYPE_USER","user");

//define("COMPONENT_PASSBACK_DATA","passbackdata");
define("COMPONENT_CONFIGMODE_GETSETTINGSHTML","getsettingshtml");
define("COMPONENT_CONFIGMODE_SAVESETTINGS","savesettings");
define("COMPONENT_ERROR_MESSAGES","errormessages");
define("COMPONENT_INFO_MESSAGES","infomessages");


// DASHBOARD DEFINITIONS
define("HOMEPAGE_DASHBOARD_ID","home");
define("HOMEPAGE_DASHBOARD_TITLE","Home Page");

// DASHLET DEFINITIONS
define("DASHLET_API_VERSION",1); // API version
define("DASHLET_ID","id");
define("DASHLET_NAME","name");
define("DASHLET_ARGS","args");
define("DASHLET_VERSION","version");
define("DASHLET_AUTHOR","author");
define("DASHLET_DESCRIPTION","description");
define("DASHLET_DATE","date");
define("DASHLET_COPYRIGHT","copyright");
define("DASHLET_LICENSE","license");
define("DASHLET_HOMEPAGE","homepage");
define("DASHLET_PREVIEW_IMAGE","preview_image");
define("DASHLET_FUNCTION","func");
define("DASHLET_URL","url");
define("DASHLET_HTML","html");
define("DASHLET_TITLE","title");
define("DASHLET_ADDTODASHBOARDTITLE","addtitle");
define("DASHLET_CSS_FILE","css_file");
define("DASHLET_JS_FILE","js_file");
define("DASHLET_CONFIGHTML","confightml");
//define("DASHLET_DISPLAYARGS","displayargs");
define("DASHLET_CLASS","class");
define("DASHLET_OUTBOARD_CLASS","outboard_class");
define("DASHLET_INBOARD_CLASS","inboard_class");
define("DASHLET_PREVIEW_CLASS","preview_class");
define("DASHLET_BOARD_STATUS","board_status");
define("DASHLET_BOARD_STATUS_OUTBOARD","outboard");
define("DASHLET_BOARD_STATUS_INBOARD","inboard");
define("DASHLET_BOARD_STATUS_PREVIEW","outboard");
define("DASHLET_MODE","mode");
define("DASHLET_MODE_PREVIEW","preview");
define("DASHLET_MODE_INBOARD","inboard");
define("DASHLET_MODE_OUTBOARD","outboard");
define("DASHLET_MODE_GETCONFIGHTML","getconfightml");
define("DASHLET_MODE_GETRECONFIGHTML","getreconfightml");
define("DASHLET_OPTS","opts");
define("DASHLET_WIDTH","width");
define("DASHLET_HEIGHT","height");
define("DASHLET_OPACITY","opacity");
define("DASHLET_BACKGROUND","background");
define("DASHLET_REFRESHRATE","refresh_rate");
define("DASHLET_SHOWASAVAILABLE","show_as_available");
define("DASHLET_PERMS","perms");
define("DASHLET_REQUIREDCONPONENTS","required_components");

// PERFORMANCE GRAPH
define("PERFGRAPH_MODE_HOSTSOVERVIEW",0);
define("PERFGRAPH_MODE_HOSTOVERVIEW",1);
define("PERFGRAPH_MODE_SERVICEDETAIL",2);
define("PERFGRAPH_MODE_GOTOSERVICEDETAIL",3);

	
	
// DATE FORMATS
define("DF_AUTO",0);
define("DF_ISO8601",1);	// 2008-01-31 23:30:01
define("DF_US",2);	// 01/31/2008 23:30:01
define("DF_EURO",3);	// 31/01/2008 23:30:01

// DATE/TIME TYPES
define("DT_AUTO",0);
define("DT_LONG_DATE_TIME",1);
define("DT_SHORT_DATE_TIME",2);
define("DT_SHORT_DATE",3);
define("DT_SHORT_TIME",4);
define("DT_HTTP_DATE_TIME",5);
define("DT_SQL_DATE_TIME",6);
define("DT_SQL_DATE",7);
define("DT_SQL_TIME",8);
define("DT_UNIX",9);

// NUMBER FORMATS
define("NF_AUTO",0);
define("NF_1",1);	// 1000000.00  - English w/o commas
define("NF_2",2);	// 1,000,000.00 - English
define("NF_3",3);	// 1.000.000,00  - German
define("NF_4",4);	// 1 000 000,00 - French
define("NF_5",5);	// 1'000'000,00 - Swiss

// USER LEVELS
define("L_USER",1);
define("L_GLOBALADMIN",255);


// INSTANCE/OBJECT PERMISSIONS
define("P_NONE",0);
define("P_READ",1);
define("P_WRITE",2);
define("P_EXECUTE",4);
define("P_LIST",8);
define("P_ADMIN",16);
define("P_RESERVED1",32);
define("P_RESERVED2",64);
define("P_RESERVED3",128);
define("P_ALL",255);

// INSTANCE OBJECT PERMISSION CALCULATION METHODS
define("PM_NONE",0);
define("PM_NAGIOS",1);
define("PM_CUSTOM",2);
define("PM_RXL",4);
define("PM_ADMIN",128);
define("PM_ALL",255);

// OBJECT TYPES
define("OBJECTTYPE_HOST",1);
define("OBJECTTYPE_SERVICE",2);
define("OBJECTTYPE_HOSTGROUP",3);
define("OBJECTTYPE_SERVICEGROUP",4);
define("OBJECTTYPE_HOSTESCALATION",5);
define("OBJECTTYPE_SERVICEESCALATION",6);
define("OBJECTTYPE_HOSTDEPENDENCY",7);
define("OBJECTTYPE_SERVICEDEPENDENCY",8);
define("OBJECTTYPE_TIMEPERIOD",9);
define("OBJECTTYPE_CONTACT",10);
define("OBJECTTYPE_CONTACTGROUP",11);
define("OBJECTTYPE_COMMAND",12);

// COMMENT TYPES
define("COMMENTTYPE_ACKNOWLEDGEMENT",4);
define("COMMENTTYPE_FLAPDETECTION",3);
define("COMMENTTYPE_DOWNTIME",2);
define("COMMENTTYPE_USER",1);

// TIMED EVENT TYPES
define("TIMEDEVENTTYPE_HOSTCHECK",12);
define("TIMEDEVENTTYPE_SERVICECHECK",0);

// CHECK TYPES
define("ACTIVE_CHECK",0);
define("PASSIVE_CHECK",1);



// SUBSYSTEM COMPONENT STATUS
define("SUBSYS_COMPONENT_RUNNING",1);
define("SUBSYS_COMPONENT_STOPPED",2);
define("SUBSYS_COMPONENT_CRASHED",3);
define("SUBSYS_COMPONENT_STOPPING",4);
define("SUBSYS_COMPONENT_RESTARTING",5);
define("SUBSYS_COMPONENT_RELOADING",6);

define("SUBSYS_COMPONENT_STATUS_OK",0);
define("SUBSYS_COMPONENT_STATUS_ERROR",1);
define("SUBSYS_COMPONENT_STATUS_UNKNOWN",2);


// MESSAGE STRING
define("MSG_OK","OK");
define("MSG_ERROR","ERROR");


// RESULT CODES
define("RESULT_ERROR",-1);
define("RESULT_OK",0);

// COMMAND STATUS CODES
define("COMMAND_STATUS_QUEUED",0);
define("COMMAND_STATUS_PROCESSING",1);
define("COMMAND_STATUS_COMPLETED",2);

// COMMAND RESULT CODES
define("COMMAND_RESULT_OK",0);
define("COMMAND_RESULT_ERROR",1);


// COMMANDS
define("COMMAND_NONE",0);

define("COMMAND_NAGIOSCORE_GETSTATUS",10);
define("COMMAND_NAGIOSCORE_START",11);
define("COMMAND_NAGIOSCORE_STOP",12);
define("COMMAND_NAGIOSCORE_RESTART",13);
define("COMMAND_NAGIOSCORE_RELOAD",14);
define("COMMAND_NAGIOSCORE_CHECKCONFIG",15);
define("COMMAND_NAGIOSCORE_SUBMITCOMMAND",16);
define("COMMAND_NAGIOSCORE_APPLYCONFIG",17);
define("COMMAND_NAGIOSCORE_RECONFIGURE",18);

define("COMMAND_NDO2DB_GETSTATUS",100);
define("COMMAND_NDO2DB_START",101);
define("COMMAND_NDO2DB_STOP",102);
define("COMMAND_NDO2DB_RESTART",103);
define("COMMAND_NDO2DB_RELOAD",104);

define("COMMAND_NPCD_GETSTATUS",110);
define("COMMAND_NPCD_START",111);
define("COMMAND_NPCD_STOP",112);
define("COMMAND_NPCD_RESTART",113);
define("COMMAND_NPCD_RELOAD",114);

define("COMMAND_NAGIOSQL_DELETECONTACT",201);
define("COMMAND_NAGIOSQL_DELETETIMEPERIOD",202);
define("COMMAND_NAGIOSQL_DELETESERVICE",203);
define("COMMAND_NAGIOSQL_DELETEHOST",204);

define("COMMAND_NAGIOSXI_SET_HTACCESS",1100);
define("COMMAND_DELETE_CONFIGWIZARD",1101);
define("COMMAND_INSTALL_CONFIGWIZARD",1102);
define("COMMAND_PACKAGE_CONFIGWIZARD",1103);
define("COMMAND_DELETE_DASHLET",1104);
define("COMMAND_INSTALL_DASHLET",1105);
define("COMMAND_PACKAGE_DASHLET",1106);
define("COMMAND_DELETE_COMPONENT",1107);
define("COMMAND_INSTALL_COMPONENT",1108);
define("COMMAND_PACKAGE_COMPONENT",1109);

// Special commands
define("COMMAND_CHANGE_TIMEZONE", 1110);
define("COMMAND_UPDATE_FUSION_TO_LATEST", 1111);
define("COMMAND_DELETE_UPGRADE_LOG", 1112);


// META TYPES
define("METATYPE_NONE",0);
define("METATYPE_EVENT",1);
define("METATYPE_COMMAND",2);
define("METATYPE_NOTIFICATION",3);
define("METATYPE_CONFIGWIZARD",4);
define("METATYPE_OTHER",999);


// HOST STATUS TYPES
define("HOSTSTATUSATTR_INDOWNTIME",1);
define("HOSTSTATUSATTR_NOTINDOWNTIME",2);
define("HOSTSTATUSATTR_ACKNOWLEDGED",4);
define("HOSTSTATUSATTR_NOTACKNOWLEDGED",8);
define("HOSTSTATUSATTR_CHECKSDISABLED",16);
define("HOSTSTATUSATTR_CHECKSENABLED",32);
// missing some...
define("HOSTSTATUSATTR_ISFLAPPING",1024);
define("HOSTSTATUSATTR_ISNOTFLAPPING",2048);
define("HOSTSTATUSATTR_NOTIFICATIONSDISABLED",4096);
define("HOSTSTATUSATTR_NOTIFICATIONSENABLED",8192);

// SERVICE STATUS TYPES
define("SERVICESTATUSATTR_INDOWNTIME",1);
define("SERVICESTATUSATTR_NOTINDOWNTIME",2);
define("SERVICESTATUSATTR_ACKNOWLEDGED",4);
define("SERVICESTATUSATTR_NOTACKNOWLEDGED",8);
define("SERVICESTATUSATTR_CHECKSDISABLED",16);
define("SERVICESTATUSATTR_CHECKSENABLED",32);
// missing some...
define("SERVICESTATUSATTR_ISFLAPPING",1024);
define("SERVICESTATUSATTR_ISNOTFLAPPING",2048);
define("SERVICESTATUSATTR_NOTIFICATIONSDISABLED",4096);
define("SERVICESTATUSATTR_NOTIFICATIONSENABLED",8192);

// STATES
define("STATE_OK",0);
define("STATE_WARNING",1);
define("STATE_CRITICAL",2);
define("STATE_UNKNOWN",3);
define("STATE_UP",0);
define("STATE_DOWN",1);
define("STATE_UNREACHABLE",2);

// PSEUDO-STATES (USED IN LINKS, ETC)
define("SERVICESTATE_PENDING",1);
define("SERVICESTATE_OK",2);
define("SERVICESTATE_WARNING",4);
define("SERVICESTATE_UNKNOWN",8);
define("SERVICESTATE_CRITICAL",16);
define("SERVICESTATE_ANY",31);

define("HOSTSTATE_PENDING",1);
define("HOSTSTATE_UP",2);
define("HOSTSTATE_DOWN",4);
define("HOSTSTATE_UNREACHABLE",8);
define("HOSTSTATE_ANY",15);


// STATE TYPES
define("STATETYPE_SOFT",0);
define("STATETYPE_HARD",1);


// CALLBACKS
define("CALLBACK_SESSION_STARTED","session_started");
define("CALLBACK_PREREQS_PASSED","prereqs_passed");
define("CALLBACK_AUTHENTICATION_PASSED","authentication_passed");

define("CALLBACK_PAGE_HEAD","page_head");
define("CALLBACK_BODY_START","body_start");
define("CALLBACK_BODY_END","body_end");
define("CALLBACK_FOOTER_START","footer_start");
define("CALLBACK_FOOTER_END","footer_end");
define("CALLBACK_HEADER_START","header_start");
define("CALLBACK_HEADER_END","header_end");
define("CALLBACK_CONTENT_START","content_start");
define("CALLBACK_CONTENT_END","content_end");

define("CALLBACK_EVENT_ADDED","event_added");
define("CALLBACK_EVENT_PROCESSED","event_processed");

define("CALLBACK_MENUS_DEFINED","menus_defined");
define("CALLBACK_MENUS_INITIALIZED","menus_initialized");
define("CALLBACK_MENUS_INITIALIZED_FINAL","menus_initialized_final");

define("CALLBACK_SERVICE_TABS_INIT","service_tabs_init");
define("CALLBACK_HOST_TABS_INIT","host_tabs_init");

define("CALLBACK_USER_NOTIFICATION_METHODS_TABS_INIT","user_notification_methods_tabs_init");
define("CALLBACK_USER_NOTIFICATION_MESSAGES_TABS_INIT","user_notification_messages_tabs_init");

define("CALLBACK_POLLING_FUNCTIONS","polling_functions"); 

define("CALLBACK_PROCESS_AUTH_INFO","process_auth_info");
define("CALLBACK_DELETE_SERVER", "delete_server"); 


// EVENT SOURCES
define("EVENTSOURCE_OTHER",0);
define("EVENTSOURCE_NAGIOSXI",1);
define("EVENTSOURCE_NAGIOSCORE",2);

// EVENT TYPES
define("EVENTTYPE_OTHER",0);
define("EVENTTYPE_STATECHANGE",1);
define("EVENTTYPE_NOTIFICATION",2);

// EVENT STATUS CODES
define("EVENTSTATUS_QUEUED",0);
define("EVENTSTATUS_PROCESSING",1);
define("EVENTSTATUS_COMPLETED",2);

// CONFIG WIZARDS
define("CONFIGWIZARD_API_VERSION",2); // API version
define("CONFIGWIZARD_TYPE","type");
define("CONFIGWIZARD_NAME","name");	
define("CONFIGWIZARD_VERSION","version");
define("CONFIGWIZARD_DATE","date");
define("CONFIGWIZARD_COPYRIGHT","copyright");
define("CONFIGWIZARD_AUTHOR","author");
define("CONFIGWIZARD_DESCRIPTION","description");
define("CONFIGWIZARD_DISPLAYTITLE","display_title");
define("CONFIGWIZARD_PREVIEWIMAGE","preview_image");	
define("CONFIGWIZARD_FUNCTION","callback_function");

define("CONFIGWIZARD_ERROR_MESSAGES","error_messages");
define("CONFIGWIZARD_PASSBACK_DATA","passback_data");
define("CONFIGWIZARD_NAGIOS_OBJECTS","nagios_objects");

define("CONFIGWIZARD_TYPE_MONITORING","monitoring");

define("CONFIGWIZARD_MODE_GETSTAGE1HTML","get_stage1_html");
define("CONFIGWIZARD_MODE_VALIDATESTAGE1DATA","validate_stage1_data");
define("CONFIGWIZARD_MODE_GETSTAGE2HTML","get_stage2_html");
define("CONFIGWIZARD_MODE_VALIDATESTAGE2DATA","validate_stage2_data");
define("CONFIGWIZARD_MODE_GETSTAGE3HTML","get_stage3_html");
define("CONFIGWIZARD_MODE_GETSTAGE3OPTS","get_stage3_opts");
define("CONFIGWIZARD_MODE_VALIDATESTAGE3DATA","validate_stage3_data");
define("CONFIGWIZARD_MODE_GETSTAGE4OPTS","get_stage4_opts");
define("CONFIGWIZARD_MODE_COMMITOK","commit_ok");
define("CONFIGWIZARD_MODE_COMMITERROR","commit_error");
define("CONFIGWIZARD_MODE_COMMITPERMSERROR","commit_perms_error");
define("CONFIGWIZARD_MODE_COMMITCONFIGERROR","commit_config_error");

define("CONFIGWIZARD_MODE_GETFINALSTAGEHTML","get_final_stage_html");
define("CONFIGWIZARD_MODE_GETOBJECTS","get_objects");

define("CONFIGWIZARD_HIDE_OPTIONS",99);  // hiden all/some options
define("CONFIGWIZARD_HIDDEN_OPTIONS","hidden_options"); // hidden options array
define("CONFIGWIZARD_HIDE_NOTIFICATION_OPTIONS","hide_notification_options");
define("CONFIGWIZARD_HIDE_NOTIFICATION_DELAY","hide_notification_delay");
define("CONFIGWIZARD_HIDE_NOTIFICATION_INTERVAL","hide_notification_interval");
define("CONFIGWIZARD_HIDE_NOTIFICATION_TARGETS","hide_notification_targets");

define("CONFIGWIZARD_OVERRIDE_OPTIONS","override_options"); // options to override



// NOTIFICATION METHODS
define("NOTIFICATIONMETHOD_FUNCTION","callback_function");

define("NOTIFICATIONMETHOD_ERROR_MESSAGES","error_messages");

define("NOTIFICATIONMETHOD_MODE_GETCONFIGOPTIONS","get_config_options");
define("NOTIFICATIONMETHOD_MODE_SETCONFIGOPTIONS","set_config_options");
define("NOTIFICATIONMETHOD_MODE_GETMESSAGEFORMAT","get_message_format");
define("NOTIFICATIONMETHOD_MODE_SETMESSAGEFORMAT","set_message_format");


// NOTIFICATION REASONS
define("NOTIFICATIONREASON_NORMAL",0);
define("NOTIFICATIONREASON_ACKNOWLEDGEMENT",1);
define("NOTIFICATIONREASON_FLAPPINGSTART",2);
define("NOTIFICATIONREASON_FLAPPINGSTOP",3);
define("NOTIFICATIONREASON_FLAPPINGDISABLED",4);
define("NOTIFICATIONREASON_DOWNTIMESTART",5);
define("NOTIFICATIONREASON_DOWNTIMEEND",6);
define("NOTIFICATIONREASON_DOWNTIMECANCELLED",7);
define("NOTIFICATIONREASON_CUSTOM",99);
define("NOTIFICATIONREASON_Custom",NOTIFICATIONREASON_CUSTOM); 

// MENUS
define("MENU_HOME","home"); // DONE!
define("MENU_VIEWS","views"); // DONE!
define("MENU_DASHBOARDS","dashboards"); // DONE!
define("MENU_REPORTS","reports"); // DONE!
define("MENU_CONFIGURE","configure"); // DONE!
define("MENU_CORECONFIGMANAGER","coreconfigmanager"); // DONE!
define("MENU_HELP","help"); // DONE!
define("MENU_ADMIN","admin"); // DONE!
define("MENU_ACCOUNT","account"); // DONE!
define("MENU_TOOLS","tools");

define("MENUITEMS","menuitems");
define("MENUSECTION","menusection");
define("MENUSECTIONEND","menusectionend");
//define("MENULINK","menulink");
define("MENULINK","link");
define("MENULINKSPACER","linkspacer");

?>