<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: db.inc.php 75 2010-04-01 19:40:08Z egalstad $

require_once(dirname(__FILE__).'/../config.inc.php');
require_once(dirname(__FILE__).'/../db/adodb/adodb.inc.php');
require_once(dirname(__FILE__).'/dbl.inc.php');
require_once(dirname(__FILE__).'/dbauth.inc.php');

$DB=array();

// initialize table names
init_db_table_names();


////////////////////////////////////////////////////////////////////////
// TABLE NAME FUNCTIONS
////////////////////////////////////////////////////////////////////////

function init_db_table_names(){
	global $db_tables;
		
	$db_tables=array();

	// fusion table names
	generate_table_name(DB_NAGIOSFUSION,"commands");
	generate_table_name(DB_NAGIOSFUSION,"events");
	generate_table_name(DB_NAGIOSFUSION,"meta");
	generate_table_name(DB_NAGIOSFUSION,"options");
	generate_table_name(DB_NAGIOSFUSION,"sysstat");
	generate_table_name(DB_NAGIOSFUSION,"usermeta");
	generate_table_name(DB_NAGIOSFUSION,"users");
	
	//added for 2012
	generate_table_name(DB_NAGIOSFUSION,"tac_data");
	generate_table_name(DB_NAGIOSFUSION,"sessions"); 
	
	//print_r($db_tables);
	}

	
function generate_table_name($package="unknown",$tablename="unknown"){
	global $cfg;
	global $db_tables;
	
	$db_tables[$package][$tablename]=$cfg['db_prefix'][$package].$tablename;
	}

	

////////////////////////////////////////////////////////////////////////
// CONNECTION FUNCTIONS
////////////////////////////////////////////////////////////////////////


function db_connect_all(){

	// connect to fusion db
	$result=db_connect(DB_NAGIOSFUSION);
	if($result==false){
		handle_db_connect_error(DB_NAGIOSFUSION);
		return false;
		}
		
	return true;
	}
	
	
	
function db_connect($db,$opts=null){
	global $cfg;
	global $DB;
	
	// global defaults
	$dbtype=$cfg['dbtype'];
	$dbserver=$cfg['dbserver'];

	if(array_key_exists("dbtype",$cfg['db_info'][$db]))
		$dbtype=$cfg['db_info'][$db]['dbtype'];
	if(array_key_exists("dbserver",$cfg['db_info'][$db]))
		$dbserver=$cfg['db_info'][$db]['dbserver'];

	if($opts==null){
		$opts=array(
			"user" => $cfg['db_info'][$db]['user'],
			"pwd" => $cfg['db_info'][$db]['pwd'],
			"db" => $cfg['db_info'][$db]['db'],
			);
		}

	$username=$opts["user"];
	$password=$opts["pwd"];
	$dbname=$opts["db"];
	
	// make a database connection
	$DB[$db]=NewADOConnection($dbtype);
	if(!$DB[$db]->Connect($dbserver,$username,$password,$dbname))
		return false;
		
	return true;
	}	




//**********************************************************************************
//**
//** DBMS-SPECIFIC FUNCTIONS
//**
//**********************************************************************************

function escape_sql_param($in,$dbtype,$quote=false){
	global $cfg;
	
	$escaped="";

	if($in===null){
		$escaped="NULL";
		$quote=false;
		}
    
	else if(is_bool($in)){
		//$out=$in ? 1 : 0;
		$out=$in ? 'TRUE' : 'FALSE';
		$quote=false;
		}
		
	else{
		//$dbtype=$cfg['dbtype']
		switch($dbtype){
			case 'mysql':
				$escaped=mysql_escape_string($in);
				break;
			case 'pgsql':
				$escaped=pg_escape_string($in);
				break;
			default:
				$escaped=addslashes($in);
				break;
			}
		}

	if($quote==true){
		$out="'".$escaped."'";
		}
	else
		$out=$escaped;

	return $out;
	}
	
function sql_time_from_timestamp($t=0,$dbh){
	global $cfg;
	
	$dbtype='';
	
	if(array_key_exists("dbtype",$cfg['db_info'][$dbh]))
		$dbtype=$cfg['db_info'][$dbh]['dbtype'];

	$timestring="";
	
	if($t==0){
		$timestring="NOW()";
		}
    
	else{
		switch($dbtype){
			case 'pgsql':
				//$timestring="TIMESTAMP 'epoch' + $t";
				$timestring="$t::abstime::timestamp without time zone";
				break;
			// assume mysql syntax
			default:
				$timestring="FROM_UNIXTIME($t)";
				break;
			}
		}

	return $timestring;
	}
	
	
////////////////////////////////////////////////////////////////////////
// SQL QUERY FUNCTIONS
////////////////////////////////////////////////////////////////////////

function exec_named_sql_query($dbh,$name,$handle_error=true){
	global $sqlquery;
	
	if(!have_value($name))
		return null;
	return exec_sql_query($dbh,$sqlquery[$name],$handle_error);
	}

	
function exec_sql_query($dbh,$sql,$handle_error=true){
	global $DB;
	
	if(!have_value($sql))
		return null;
		
	if(!$dbh)
		return null;
	if(!isset($DB[$dbh]))
		return null;
	$rs=$DB[$dbh]->Execute($sql);
	if(!$rs && $handle_error==true)
		handle_sql_error($dbh,$sql);
	else
		return $rs;
	}


function get_sql_error($dbh){
	global $DB;
	$d=$DB[$dbh];
	return $d->ErrorMsg();
	return $DB[$dbh]->ErrorMsg();
	}
	

function get_sql_insert_id($dbh,$seqname=''){
	global $cfg;
	global $DB;

	$dbtype='';
	
	if(array_key_exists("dbtype",$cfg['db_info'][$dbh]))
		$dbtype=$cfg['db_info'][$dbh]['dbtype'];
		
	// for postgresql we must get current value of sequence
	if($dbtype=='pgsql'){
		$id=-1;
		if($seqname!=''){
			$sql="SELECT currval('".$seqname."') AS newid;";
			if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql,false))){
				if($rs->MoveFirst()){
					$id=intval($rs->fields['newid']);
					}
				}
			}
		}
		
	// else use adodb's function
	else
		$id=$DB[$dbh]->Insert_ID();
		
	if($id=='')
		$id=-1;
		
	//echo "INSERT ID='$id'<BR>\n";
	
	return $id;
	}


?>