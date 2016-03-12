<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: handler-users.inc.php 75 2010-04-01 19:40:08Z egalstad $

require_once(dirname(__FILE__).'/common.inc.php');



// USERS (FRONTEND)  *************************************************************************
function fetch_users(){
	global $DB;
	global $cfg;
	global $sqlquery;
	global $db_tables;
	global $request;
	
	// only let admins see this
	if(is_admin()==false){
		exit;
		}

	// generate query
	$fieldmap=array(
		"user_id" => $db_tables[DB_NAGIOSFUSION]["users"].".user_id",
		"username" => $db_tables[DB_NAGIOSFUSION]["users"].".username",
		"name" => $db_tables[DB_NAGIOSFUSION]["users"].".name",
		"email" => $db_tables[DB_NAGIOSFUSION]["users"].".email",
		"enabled" => $db_tables[DB_NAGIOSFUSION]["users"].".enabled",
		);
	$args=array(
		"sql" => $sqlquery['GetUsers'],
		"fieldmap" => $fieldmap
		);
	$sql=generate_sql_query(DB_NAGIOSFUSION,$args);
	
	if(!($rs=$DB[DB_NAGIOSFUSION]->Execute($sql)))
		handle_backend_db_error(DB_NAGIOSFUSION);
	else{
		output_backend_header();
		echo "<userlist>\n";
		echo "  <recordcount>".$rs->RecordCount()."</recordcount>\n";
		
		if(!isset($request["totals"])){
			while(!$rs->EOF){

				echo "  <user id='".db_field($rs,'user_id')."'>\n";
				//xml_db_field(2,$rs,'user_id','id');
				xml_db_field(2,$rs,'username');
				xml_db_field(2,$rs,'name');
				xml_db_field(2,$rs,'email');
				xml_db_field(2,$rs,'enabled');
				echo "  </user>\n";

				$rs->MoveNext();
				}
			}
		echo "</userlist>\n";
		}
	}



?>