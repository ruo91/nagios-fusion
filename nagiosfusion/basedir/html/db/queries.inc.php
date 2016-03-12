<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: queries.inc.php 96 2010-05-12 17:01:05Z egalstad $

require_once(dirname(__FILE__).'/../includes/db.inc.php');
require_once(dirname(__FILE__).'/../includes/constants.inc.php');


//**********************************************************************************
//**
//** FRONTEND
//**
//**********************************************************************************

// USERS
$sqlquery['GetUsers']="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]['users']." WHERE TRUE";

//
$sqlquery['CheckFusionInstall']="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]['options'];


// COMMANDS
$sqlquery['GetCommands']="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]['commands']." WHERE TRUE";




?>
