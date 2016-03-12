<?php
// Fusion Core Component Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: fusioncore.inc.php 218 2012-12-17 20:42:02Z mguthrie $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');

// core helpers
include_once(dirname(__FILE__).'/ajaxhelpers.inc.php');

// core dashlets
include_once(dirname(__FILE__).'/dashlets.inc.php');

// status
//include_once(dirname(__FILE__).'/status-utils.inc.php');

// run the initialization function
fusioncore_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function fusioncore_component_init(){

	$name="fusioncore";
	
	$args=array(

		// need a name
		COMPONENT_NAME => $name,
		
		// informative information
		//COMPONENT_VERSION => "1.1",
		//COMPONENT_DATE => "11-27-2009",
		COMPONENT_TITLE => "Nagios Fusion Core Functions",
		COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
		COMPONENT_DESCRIPTION => gettext("Provides core functions and interface functionality for Nagios Fusion."),
		COMPONENT_COPYRIGHT => "Copyright (c) 2010 Nagios Enterprises",
		//COMPONENT_HOMEPAGE => "http://www.nagios.com",
		
		// do not delete
		COMPONENT_PROTECTED => true,
		COMPONENT_TYPE => COMPONENT_TYPE_CORE,

		// configuration function (optional)
		//COMPONENT_CONFIGFUNCTION => "xicore_component_config_func",
		);
		
	register_component($name,$args);
	}

	


?>