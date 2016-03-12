<?php //utils-session.inc.php  
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// Development Started 04/06/2012
// $Id: utils-email.inc.php 75 2010-04-01 19:40:08Z 

require_once(dirname(__FILE__).'/common.inc.php');

////////////////////////////////////////////////////////////////////////
// SESSION FUNCTIONS
////////////////////////////////////////////////////////////////////////


// start session - require cookies
//use $lock_session=true for ajax loads, should increase ajax load time and prevent blocking access
function init_session($lock_session=false){

	// we are running as a subsystem cron job
	if(defined("SUBSYSTEM")){
		$_SESSION["user_id"]=0;
		return;
		}
	
	session_name("nagiosfusion");

	// require cookies
	ini_set("session.use_cookies","1"); 
	ini_set("session.use_only_cookies","1"); 
	ini_set("session.cookie_lifetime","0"); 
	$cookie_timeout=60*30; // in seconds
	$cookie_path="/";
	$garbage_timeout=$cookie_timeout+600; //in seconds
	session_set_cookie_params($cookie_timeout,$cookie_path);
	ini_set("session.gc_maxlifetime",$garbage_timeout);
		
	// start session
	session_start();

	
	// adust cookie timeout to reset after page refresh
	if(isset($_COOKIE[session_name()]))
		setcookie(session_name(),$_COOKIE[session_name()],time()+$cookie_timeout,$cookie_path);

	// do callbacks
	$args=array();
	do_callbacks(CALLBACK_SESSION_STARTED,$args);
	
	//set a one-time flag so that we only log into XI once per session
	if(!isset($_SESSION['xi_sessions_started'])) {
		$_SESSION['xi_sessions_started'] = false; 	
	}

	//use this for ajax loaded content
	if($lock_session)
		session_write_close(); 
	
		
}

// clear session
function deinit_session(){

	// clear session variables
	$_SESSION=array();

	// delete the session cookie.
	if(isset($_COOKIE[session_name()]))
		setcookie(session_name(),'',time()-42000,'/');

	//remove session info from db
	delete_session_info(); 

	//  destroy the session.
	if(session_id()!='')
		session_destroy();
	}

	


/**
*	saves new session ID to DB, or updates if it already exists 
*	session id tracking is used by subsystem poller to detect active users 
*/ 	
function save_session_info(){
	
	$sid = session_id(); 
	$now = time(); 

	//check if we're updating or inserting
	$query="SELECT COUNT(*) FROM fusion_sessions WHERE session_id='$sid'"; 
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true);
	
	foreach($rs as $r) $count = $r['count'];	

	//insert or update??	
	if($count==0)	
		$query = "INSERT INTO fusion_sessions (session_id,last_update_time) VALUES ('$sid',NOW())";
	else
		$query = "UPDATE fusion_sessions SET last_update_time=NOW() WHERE session_id='$sid'"; 
	//save to DB 
	exec_sql_query(DB_NAGIOSFUSION,$query,true); 

}	


/**
*	clears session info from DB 
*/ 
function delete_session_info() {

	$sid = session_id(); 
	$query = "DELETE FROM fusion_sessions WHERE session_id='$sid'";  
	exec_sql_query(DB_NAGIOSFUSION,$query,true);

}


/**
*	db maintenance function that cleans out old session information that's not needed anymore 
*/ 
function clear_stale_sessions() {
	
	$stale=time()-3600; //60 minutes 
	$query = "DELETE FROM fusion_sessions WHERE last_update_time < ".sql_time_from_timestamp($stale,DB_NAGIOSFUSION);
	exec_sql_query(DB_NAGIOSFUSION,$query,true);

}


/**
*	checks to see if there's a current active web session 
*/ 
function active_session_exists() {

	$stale = time() - 300; 
	$query="SELECT COUNT(*) FROM fusion_sessions WHERE last_update_time > ".sql_time_from_timestamp($stale,DB_NAGIOSFUSION);
	//echo $query;  
	$rs=exec_sql_query(DB_NAGIOSFUSION,$query,true);
	foreach($rs as $r) $count = $r['count'];
	
	if($count > 0)
		return true;
	else
		return false; 

}

	
?>