#!/usr/bin/php -q
<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: dbmaint.php 75 2010-04-01 19:40:08Z egalstad $

define("SUBSYSTEM",1);
//define("BACKEND",1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


init_dbmaint();
do_dbmaint_jobs();



function init_dbmaint(){

	// make database connections
	$dbok=db_connect_all();
	if($dbok==false){
		echo "ERROR CONNECTING TO DATABASES!\n";
		exit();
		}

	return;
	}

function do_dbmaint_jobs(){
	global $db_tables;
	global $cfg;
	global $max_time;
	global $sleep_time;

	$now=time();

	
	
	/////////////////////////////////////////////////////////////
	// OPTIMIZE FUSION TABLES
	/////////////////////////////////////////////////////////////
	$dbminfo=$cfg['db_info']['nagiosfusion']['dbmaint'];
	
	$optimize=false;
	$lastopt=get_meta(METATYPE_NONE,0,"last_db_optimization");
	if($lastopt==null)
		$optimize=true;
	else{
		if($now > ($lastopt + (intval($dbminfo["optimize_interval"])*60)))
			$optimize=true;
		}
	if(intval($dbminfo["optimize_interval"])==0)
		$optimize=false;
	if($optimize==true){
		foreach($db_tables[DB_NAGIOSFUSION] as $table){
			//echo "TABLE: $table\n";
			optimize_table(DB_NAGIOSFUSION,$table);
			}
		set_meta(METATYPE_NONE,0,"last_db_optimization",$now);
		}

	/////////////////////////////////////////////////////////////
	// REPAIR NAGIOSFUSION TABLES
	/////////////////////////////////////////////////////////////
	
	$repair=true;
	$optimize=false;
	$lastopt=get_meta(METATYPE_NONE,0,"last_db_repair");
	if($lastopt==null)
		$repair=true;
	else{
		if($now > ($lastopt + (intval($dbminfo["repair_interval"])*60)))
			$repair=true;
		}
	if(intval($dbminfo["repair_interval"])==0)
		$repair=false;
	if($repair==true){
		foreach($db_tables[DB_NAGIOSFUSION] as $table){
			//echo "TABLE: $table\n";
			repair_table(DB_NAGIOSFUSION,$table);
			}
		set_meta(METATYPE_NONE,0,"last_db_repair",$now);
		}


	/////////////////Clear State Session Data////////////////////////
	clear_stale_sessions(); 
			
	
	update_sysstat();
	}
	
function clean_db_table($db,$table,$field,$ts){
	global $db_tables;
	
	$sql="DELETE FROM ".$db_tables[$db][$table]." WHERE ".$field." < ".sql_time_from_timestamp($ts,$db)."";
	echo "SQL: $sql\n";
	$rs=exec_sql_query($db,$sql);
	}
	
	
function optimize_table($db,$table){
	global $cfg;
	global $db_tables;
	
	$dbtype=$cfg['db_info'][$db]["dbtype"];
	
	// postgres
	if($dbtype=='pgsql'){
		$sql="VACUUM ANALYZE ".$table.";";
		}
	// mysql
	else{
		$sql="OPTIMIZE TABLE ".$table."";
		}
		
	echo "SQL: $sql\n";
	$rs=exec_sql_query($db,$sql);
	}
	
function repair_table($db,$table){
	global $db_tables;
	global $cfg;
	
	$dbtype=$cfg['db_info'][$db]["dbtype"];

	// only works with mysql
	if($dbtype=='mysql'){
		$sql="REPAIR TABLE ".$table."";
		echo "SQL: $sql\n";
		$rs=exec_sql_query($db,$sql);
		}
	}
	
function update_sysstat(){
	// record our run in sysstat table
	$arr=array(
		"last_check" => time(),
		);
	$sdata=serialize($arr);
	update_systat_value("dbmaint",$sdata);
	}
	
	

?>