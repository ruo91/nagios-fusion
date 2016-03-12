<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: auth.inc.php 75 2010-04-01 19:40:08Z egalstad $

include_once('utils.inc.php');

// redirect to login screen if user is not authenticated
function check_authentication($redirect=true){
	global $request;
	global $lstr;
	
	// some pages are used by both frontend and backend, so check for backend...
	if(defined("BACKEND")){
		echo "BACKEND DEFINED";
		check_backend_authentication();
		return;
		}
	
	if(is_authenticated()==false){
	
		// check backent ticket
		if(is_backend_authenticated()==true)
			return;

		// don't redirect user
		if($redirect==false){
			echo "Your session has timed out.";
			}
		
		// redirect user to login screen
		else{
			$redirecturl=$_SERVER['PHP_SELF'];
			$redirecturl.="%3f"; // question mark
		
			// add any variables present in original query string
			$request=array();
			grab_request_vars(false,"get");
			foreach($request as $var => $val){
				$redirecturl.="%26".$var."=".$val;
				}
		
			$theurl=get_base_url().PAGEFILE_LOGIN."?redirect=$redirecturl";
			//echo "THEURL: $theurl<BR>\n";
			header("Location: ".$theurl);
			}
		
		exit();
		}

	// do callbacks
	$args=array();
	do_callbacks(CALLBACK_AUTHENTICATION_PASSED,$args);

	// save_session to db 
	save_session_info(); 

	}

// checks if user is authenticated
function is_authenticated(){

	// some pages are used by both frontend and backend, so check for backend...
	if(defined("BACKEND")){
		return is_backend_authenticated();
		}

	if(isset($_SESSION["user_id"]))
		return true;

	return false;
	}
	
// determines if auto-login is enabled
function is_autologin_enabled(){

	$opt_s=get_option("autologin_options");
	if($opt_s=="")
		return false;
	else
		$opts=unserialize($opt_s);	
		
	$enabled=grab_array_var($opts,"autologin_enabled");
	$username=grab_array_var($opts,"autologin_username");
	
	if($enabled==1 && $username!="" && is_valid_user($username))
		return true;
	
	return false;
	}	
	
?>