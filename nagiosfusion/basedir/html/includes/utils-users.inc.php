<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// Development Started 03/22/2008
// $Id: utils-users.inc.php 79 2010-04-02 16:49:54Z egalstad $

//require_once(dirname(__FILE__).'/common.inc.php');


////////////////////////////////////////////////////////////////////////////////
// XML DATA
////////////////////////////////////////////////////////////////////////////////

function get_xml_users($args=array()){
	$x=simplexml_load_string(get_users_xml_output($args));
	//print_r($x);
	return $x;
	}

////////////////////////////////////////////////////////////////////////
// USER ACCOUNT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function add_user_account($username,$password,$name,$email,$level,$forcepasschange,$addcontact,&$errmsg){
	global $db_tables;
	global $lstr;
	
	$error=false;
	$errors=0;
	
	$user_id=-1;

	// make sure we have required variables
	if(!have_value($username)){
		$error=true;
		$errmsg[$errors++]=$lstr['BlankUsernameError'];
		}
	if(!have_value($email)){
		$error=true;
		$errmsg[$errors++]=$lstr['BlankEmailError'];
		}
	else if(!valid_email($email)){
		$error=true;
		$errmsg[$errors++]=$lstr['InvalidEmailError'];
		}
	if(!have_value($name)){
		$error=true;
		$errmsg[$errors++]=$lstr['BlankNameError'];
		}
	if(!have_value($password)){
		$error=true;
		$errmsg[$errors++]=$lstr['BlankPasswordError'];
		}
	if(!have_value($level)){
		$error=true;
		$errmsg[$errors++]=$lstr['BlankSecurityLevelError'];
		}
		
	// does user account already exist?
	if(is_valid_user($username)==true){
		$error=true;
		$errmsg[$errors++]=$lstr['AccountNameCollisionError'];
		}

	// generate random backend ticket string
	$backend_ticket=random_string(64);
	
	// add account
	if($error==false){
		$sql="INSERT INTO ".$db_tables[DB_NAGIOSFUSION]["users"]." (username,email,name,password,backend_ticket) VALUES ('".escape_sql_param($username,DB_NAGIOSFUSION)."','".escape_sql_param($email,DB_NAGIOSFUSION)."','".escape_sql_param($name,DB_NAGIOSFUSION)."','".md5($password)."','".$backend_ticket."')";
		if(!exec_sql_query(DB_NAGIOSFUSION,$sql)){
			$error=true;
			$errmsg[$errors++]=$lstr['AddAccountFailedError'].": ".get_sql_error(DB_NAGIOSFUSION);
			}
		else
			$user_id=get_sql_insert_id(DB_NAGIOSFUSION,"fusion_users_user_id_seq");
		}
	if($user_id<1){
		$errmsg[$errors++]="Unable to get insert id for new user account";
		$error=true;
		}
	if($error==false){
		// assign privs
		if(!set_user_meta($user_id,'userlevel',$level)){
			$error=true;
			$errmsg[$errors++]=$lstr['AddAccountPrivilegesFailedError'];
			}
		// force password change at next login
		if($forcepasschange==true)
			set_user_meta($user_id,'forcepasswordchange','1');

		}
	
	if($error==false)
		return $user_id;
	else
		return null;
	}


function get_user_attr($user_id,$attr){
	global $db_tables;
	
	// use logged in user's id
	if($user_id==0 && isset($_SESSION["user_id"]))
		$user_id=$_SESSION["user_id"];
	
	// make sure we have required variables
	if(!have_value($user_id))
		return null;
	if(!have_value($attr))
		return null;

	// get attribute
	$sql="SELECT ".escape_sql_param($attr,DB_NAGIOSFUSION)." FROM ".$db_tables[DB_NAGIOSFUSION]["users"]." WHERE user_id='".escape_sql_param($user_id,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql,false))){
		if($rs->MoveFirst()){
			return $rs->fields[$attr];
			}
		}
	return null;
	}


function change_user_attr($user_id,$attr,$value){
	global $db_tables;
	
	// use logged in user's id
	if($user_id==0)
		$user_id=$_SESSION["user_id"];
	
	// make sure we have required variables
	if(!have_value($user_id))
		return error;
	if(!have_value($attr))
		return error;

	// update attribute
	$sql="UPDATE ".$db_tables[DB_NAGIOSFUSION]["users"]." SET ".escape_sql_param($attr,DB_NAGIOSFUSION)."='".escape_sql_param($value,DB_NAGIOSFUSION)."' WHERE user_id='".escape_sql_param($user_id,DB_NAGIOSFUSION)."'";
	if(!exec_sql_query(DB_NAGIOSFUSION,$sql))
		return false;
	return true;
	}


// checks if a user account exists
function is_valid_user($username){
	$id=get_user_id($username);
	if(!have_value($id))
		return false;
	return true;
	}

	
// checks if a user account exists (using id)
function is_valid_user_id($userid){
	global $db_tables;
	
	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["users"]." WHERE user_id='".escape_sql_param($userid,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql))){
		if($rs->RecordCount()>0)
			return $rs->fields["user_id"];
		}
	return false;
	}

	
function get_user_id($username){
	global $db_tables;
	
	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["users"]." WHERE username='".escape_sql_param($username,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql))){
		if($rs->RecordCount()>0)
			return $rs->fields["user_id"];
		}
	return null;
	}


// get all users in the database
function get_user_list(){
	global $db_tables;
	
	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["users"]." ORDER BY username ASC";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql)))
		return $rs;
	return null;
	}

	
function delete_user_id($userid,$deletecontact=true){
	global $db_tables;

	// delete user account
	$sql="DELETE FROM ".$db_tables[DB_NAGIOSFUSION]["users"]." WHERE user_id='".escape_sql_param($userid,DB_NAGIOSFUSION)."'";
	if(!($rs=exec_sql_query(DB_NAGIOSFUSION,$sql)))
		return false;
		
	// delete user meta
	$sql="DELETE FROM ".$db_tables[DB_NAGIOSFUSION]["usermeta"]." WHERE user_id='".escape_sql_param($userid,DB_NAGIOSFUSION)."'";
	if(!($rs=exec_sql_query(DB_NAGIOSFUSION,$sql)))
		return false;
	

	return true;
	}


////////////////////////////////////////////////////////////////////////
// USER AUTHORIZATION FUNCTION
////////////////////////////////////////////////////////////////////////

function is_admin($user_id=0){

	// subsystem cron jobs run with admin privileges
	if(defined("SUBSYSTEM"))
		return true;

	// use logged in user's id
	if($user_id==0)
		$user_id=$_SESSION["user_id"];
		
		
	$level=get_user_meta($user_id,'userlevel');
	//return "[".$user_id."=".$level."]";
	if(intval($level)==L_GLOBALADMIN)
		return true;
	else
		return false;
	}


function get_authlevels(){
	global $lstr;

	$levels=array(
		L_USER => $lstr['UserLevelText'],
		L_GLOBALADMIN => $lstr['AdminLevelText']
		);

	return $levels;
	}


function is_valid_authlevel($level){

	$levels=get_authlevels();

	return array_key_exists($level,$levels);
	}


////////////////////////////////////////////////////////////////////////
// MISC USER FUNCTION
////////////////////////////////////////////////////////////////////////

function is_advanced_user($userid=0){

	if($userid==0)
		$userid=$_SESSION["user_id"];

	// admins are experts
	if(is_admin($userid)==true)
		return true;
		
	// certain users are experts
	$advanceduser=get_user_meta($userid,"advanced_user");
	if($advanceduser==1)
		return true;
	else
		return false;
	
	return false;
	}
	
function is_readonly_user($userid=0){

	if($userid==0)
		$userid=$_SESSION["user_id"];

	// admins are always read/write
	if(is_admin($userid)==true)
		return false;
		
	// certain users are experts
	$readonlyuser=get_user_meta($userid,"readonly_user");
	if($readonlyuser==1)
		return true;
	else
		return false;
	
	return false;
	}
	
	
////////////////////////////////////////////////////////////////////////
// USER META DATA FUNCTIONS
////////////////////////////////////////////////////////////////////////

function get_user_meta($user_id,$key){
	global $db_tables;

	// use logged in user's id
	if($user_id==0){
		if(!isset($_SESSION["user_id"]))
			return null;
		else
			$user_id=$_SESSION["user_id"];
		}
	
	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["usermeta"]." WHERE user_id='".escape_sql_param($user_id,DB_NAGIOSFUSION)."' AND keyname='".escape_sql_param($key,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql))){
		if($rs->MoveFirst()){
			return $rs->fields["keyvalue"];
			}
		}
	return null;
	}


function get_all_user_meta($user_id){
	global $db_tables;
	
	$meta=array();

	// use logged in user's id
	if($user_id==0){
		if(!isset($_SESSION["user_id"]))
			return null;
		else
			$user_id=$_SESSION["user_id"];
		}
	
	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["usermeta"]." WHERE user_id='".escape_sql_param($user_id,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql))){
		while(!$rs->EOF){
			$meta[$rs->fields["keyname"]]=$rs->fields["keyvalue"];
			$rs->MoveNext();
			}
		}
	return $meta;
	}

	
function get_user_meta_session_vars($overwrite=false){
	global $db_tables;
	
	if(!isset($_SESSION["user_id"]))
		return null;

	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["usermeta"]." WHERE user_id='".escape_sql_param($_SESSION["user_id"],DB_NAGIOSFUSION)."' AND autoload='1'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql,false))){
		while(!$rs->EOF){
			// set session variable - skip some
			switch($rs->fields["keyname"]){
				case "user_id";  // security risk
					break;
				default:
					if(!($overwrite==false && isset($_SESSION[$rs->fields["keyname"]])))
						$_SESSION[$rs->fields["keyname"]]=$rs->fields["keyvalue"];
					break;
				}
			$rs->MoveNext();
			}
		}
	return null;
	}


function set_user_meta($user_id,$key,$value,$sessionload=true){
	global $db_tables;
	
	// use logged in user's id
	if($user_id==0)
		$user_id=$_SESSION["user_id"];
	
	$autoload=0;
	if($sessionload==true)
		$autoload=1;

	// see if data exists already
	$key_exists=false;
	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["usermeta"]." WHERE user_id='".escape_sql_param($user_id,DB_NAGIOSFUSION)."' AND keyname='".escape_sql_param($key,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql))){
		if($rs->RecordCount()>0)
			$key_exists=true;
		}

	// insert new key
	if($key_exists==false){
		$sql="INSERT INTO ".$db_tables[DB_NAGIOSFUSION]["usermeta"]." (user_id,keyname,keyvalue,autoload) VALUES ('".escape_sql_param($user_id,DB_NAGIOSFUSION)."','".escape_sql_param($key,DB_NAGIOSFUSION)."','".escape_sql_param($value,DB_NAGIOSFUSION)."','".$autoload."')";
		return exec_sql_query(DB_NAGIOSFUSION,$sql);
		}

	// update existing key
	else{
		$sql="UPDATE ".$db_tables[DB_NAGIOSFUSION]["usermeta"]." SET keyvalue='".escape_sql_param($value,DB_NAGIOSFUSION)."', autoload='".$autoload."' WHERE user_id='".escape_sql_param($user_id,DB_NAGIOSFUSION)."' AND keyname='".escape_sql_param($key,DB_NAGIOSFUSION)."'";
		return exec_sql_query(DB_NAGIOSFUSION,$sql);
		}
		
	}

	
function delete_user_meta($user_id,$key){
	global $db_tables;

	// use logged in user's id
	if($user_id==0)
		$user_id=$_SESSION["user_id"];
	
	$sql="DELETE FROM ".$db_tables[DB_NAGIOSFUSION]["usermeta"]." WHERE user_id='".escape_sql_param($user_id,DB_NAGIOSFUSION)."' AND keyname='".escape_sql_param($key,DB_NAGIOSFUSION)."'";
	return exec_sql_query(DB_NAGIOSFUSION,$sql);
	}



////////////////////////////////////////////////////////////////////////
// USER MASQUERADE FUNCTIONS
////////////////////////////////////////////////////////////////////////


function masquerade_as_user_id($user_id=-1){

	// only admins can masquerade
	if(is_admin()==false)
		return;
	
	if(!is_valid_user_id($user_id)){
		return;
		}
		
	$username=get_user_attr($user_id,"username");
	
	//echo "GOOD TO GO";

	///////////////////////////////////////////////////////////////
	// DESTROY CURRENT USER SESSION
	///////////////////////////////////////////////////////////////
	//  destroy the session.
	deinit_session();
	init_session();
	
	// reinitialize theme
	//init_theme();
	
	// reinitialize the menu
	//init_menus();
	
	///////////////////////////////////////////////////////////////
	// SETUP NEW USER SESSION
	///////////////////////////////////////////////////////////////

	// set session variables
	$_SESSION["user_id"]=$user_id;
	$_SESSION["username"]=$username;
				
	// load user session variables (e.g. preferences)
	get_user_meta_session_vars(true);
	
	}

	
////////////////////////////////////////////////////////////////////////
// DEFAULT VIEWS/DASHBOARDS FUNCTIONS
////////////////////////////////////////////////////////////////////////

	
function add_default_views($userid=0){

	// add some views for the user if they don't have any
	$views=get_user_meta($userid,"views");
	if($views==null || $views==""){
//		add_view($userid,"/nagiosxi/includes/components/nagioscore/ui/tac.php","Tactical Overview");
		}
	}	
	
function add_default_dashboards($userid=0){

	// add some dashboards for the user if they don't have any
	$add=false;
	$dashboards=get_user_meta($userid,"dashboards");
	if($dashboards==null || $dashboards=="")
		$add=true;
	if($add==true){

		// home page dashboard
		$db=add_dashboard($userid,"Home Page",array(),HOMEPAGE_DASHBOARD_ID);
		// add some dashlets to the home dashboard (done later...)
		
		// empty dashboard
		$db=add_dashboard($userid,"Empty Dashboard",array(),null);
		}
		
	// fix blank homepage dashboard
	init_home_dashboard_dashlets($userid);
	}
	
// add default dashlets to a blank home dashboard
function init_home_dashboard_dashlets($userid=0){

	$homedash=get_dashboard_by_id($userid,HOMEPAGE_DASHBOARD_ID);
	if($homedash==null)
		return false;
		
	$dashcount=count($homedash["dashlets"]);
	if($dashcount==0){
		// getting started
		add_dashlet_to_dashboard($userid,HOMEPAGE_DASHBOARD_ID,"fusioncore_getting_started","Getting Started",array("height"=>365,"width"=>415,"top"=>60,"left"=>10,"pinned"=>0,"zindex"=>"1"),array());
		return true;
	}
	
	return false;
}
?>