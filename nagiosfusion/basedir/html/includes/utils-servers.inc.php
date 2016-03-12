<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// Development Started 03/22/2008
// $Id: utils-systat.inc.php 18 2010-07-08 20:21:34Z egalstad $

//require_once(dirname(__FILE__).'/common.inc.php');



////////////////////////////////////////////////////////////////////////
// MISC SERVER FUNCTIONS
////////////////////////////////////////////////////////////////////////

function get_server_types(){
	$types=array(
		"nagiosxi" => "Nagios XI",
		"nagioscore" => "Nagios Core",
		//"nagiosvshell" => "Nagios Core (V-Shell)", //added V-Shell Support -MG 4/07/11 
		);
	return $types;
	}
	
function get_server_type_from_short_name($name){

	$type=get_server_types();
	foreach($type as $sname => $lname){
		if($sname==$name)
			return $lname;
		}
	return "Unknown";
	}

function get_server_authentication_methods(){
	$types=array(
		"session" => "Session",
		"basic" => "HTTP Basic",
		);
	return $types;
	}

	
function get_server_root_url($sid,$frontend=true){

	// get server info
	$sinfo=get_server_info($sid);
	
	// get url
	if($frontend==true)
		$url=grab_array_var($sinfo,"url");
	else{
		$url=grab_array_var($sinfo,"internal_url");
		if($url=="")
			$url=grab_array_var($sinfo,"url");
		}
	
	// get server type
	$type=grab_array_var($sinfo,"type");
	
	// adjust url based on server type
	// TODO...
	
	return $url;
	}
	

////////////////////////////////////////////////////////////////////////
// SERVER STATE FUNCTIONS
////////////////////////////////////////////////////////////////////////

function get_server_state($sid,&$res,&$output){

	$result=false;
	$output="";

	$sinfo=get_server_info($sid);
	
	//print_r($sinfo);
	//echo "<BR><BR>";
	
	// check server
	$args=array(
		"timeout" => 10,
		"return_info" => true,
		);
	$url=get_server_root_url($sid,false);
	//echo "FETCHING $url<BR><BR>";
	$urlres=load_url($url,$args);
	
	$http_code=$urlres['info']['http_code'];

	if($http_code==0){
		$output="Unresponsive";
		$result=false;
		}
	else{
		$result=true;
		$output.="OK";   //removed http_code, no auth here -MG 5/3/11
		}
	
	//print_r($urlres);
	//echo "<BR><BR>";
	
	
	$res=$result;
	return $result;
	}
	
	
function check_server_auth($sid,$username,$password,&$res,&$output){

	$result=false;
	$output="";

	$sinfo=get_server_info($sid);
	
	//print_r($sinfo);
	//echo "<BR><BR>";
	
	// check server
	$url=get_server_root_url($sid,false);
	
	// parse url
	$urlparts=parse_url($url);
	
	// authentication
	$basicauth="";
	$urlauth="";
	if($sinfo["auth"]=="basic")
		$basicauth=$username.":".$password."@";
	if($sinfo["type"]=="nagiosxi")
		$urlauth="&username=".$username."&password=".md5($password);
	
	// construct new url
	$newurl=$urlparts["scheme"]."://".$basicauth.$urlparts["host"];
	if(array_key_exists("port",$urlparts))
		$newurl.=":".$urlparts["port"];
	$newurl.=$urlparts["path"]."/";

	if($sinfo["type"]=="nagiosxi"){
		$newurl.="backend/";
		$newurl.="?cmd=getticket";
		$newurl.=$urlauth;
		}

	
	//echo "FETCHING $newurl<BR><BR>";
	
	$args=array(
		"timeout" => 10,
		"return_info" => true,
		"method" => "post",
		);
	$urlres=load_url($newurl,$args);
	
	$http_code=$urlres['info']['http_code'];

	// first determine result by http code
	switch($http_code){
		case 0:
			$output="Server Timeout";
			$result=false;
			break;
		case 200:
			$output="OK ($http_code)";  //added http_code for clarity -MG 
			$result=true;
			break;
		case 401:
			$output="AUTH ERROR ($http_code)";
			$result=false;
			break;
		default:
			$result=false;
			$output.="AUTH UNKNOWN ($http_code)";
			break;
		}
		
	// check nagiosxi backend result
	if($sinfo["type"]=="nagiosxi" && $result==true){
		
		$output="AUTH BAD";
		$result=false;
		
		$body=$urlres['body'];
		//$output=$body;
		$xml=@simplexml_load_string($body);
		if($xml){
			$ticket=strval($xml);
			//$ticket=intval($xml[1]);
			//$output="TICKET=$ticket";
			// we got a ticket!
			if(strlen($ticket)>0){		//added strlen function.  ($ticket>0)  always returns false -MG 5/3/11
				$output="OK";
				$result=true;
				}
			}
		//print_r($xml);
		}
	
	//print_r($urlres);
	//echo "<BR><BR>";
	
	
	$res=$result;
	return $result;
	}
	
	
function get_server_data($sid,$username,$password,$type,&$arr){

	global $cfg;

	$arr=array(
		"timeout" => false,
		"http_code" => 0,
		"url" => "",
		"data" => "",
		);

	$sinfo=get_server_info($sid);
	
	//print_r($sinfo);
	//echo "<BR><BR>";
	
	// check server
	$url=get_server_root_url($sid,false);
	
	// parse url
	$urlparts=parse_url($url);
	
	// authentication
	$basicauth="";
	$urlauth="";
	if($sinfo["auth"]=="basic")
		$basicauth=$username.":".$password."@";
	else{
		if($sinfo["type"]=="nagiosxi")
			$urlauth="&username=".$username."&password=".md5($password);
		}
	
	// construct new url
	$newurl=$urlparts["scheme"]."://".$basicauth.$urlparts["host"];
	if(array_key_exists("port",$urlparts))
		$newurl.=":".$urlparts["port"];
	$newurl.=$urlparts["path"]."/";

	if($sinfo["type"]=="nagiosxi"){
		$newurl.="backend/";
		switch($type){
			case "hoststatus":
				$newurl.="?cmd=gethoststatus&brevity=1";
				break;
			case "servicestatus":
				$newurl.="?cmd=getservicestatus&combinedhost=true&brevity=1";
				break;
			case "programstatus":
				$newurl.="?cmd=getprogramstatus";
				break;
			case 'recentalerts':
				
				break; 
			
			default:
				$newurl.="?cmd=getticket";
				break;
			}
		$newurl.=$urlauth;
		}
	else if($sinfo["type"]=="nagioscore"){
		$newurl.="cgi-bin/";
		switch($type){
			case "tac":
				//$newurl.="tac-xml.cgi?fusion=1";
				$newurl.='tac.cgi?';
				break;
			default:
				$newurl.="?";
				break;
			}
		}
	/////////////////////////////////////////////////////////////////////////	
	//testing for V-Shell Integration	
	else if($sinfo['type']=='nagiosvshell') {
		$newurl.='?type=backend&mode=xml';
	}	
		
	// save url
	$arr["url"]=$newurl;
	
	//echo "FETCHING $newurl<BR><BR>";
	
	$args=array(
		"timeout" => grab_array_var($cfg,'default_timeout',30),		//increasing timeout for servers with heavy load -MG 
		"return_info" => true,
		"method" => "post",
		);
	
	if($sinfo['type']=='nagiosvshell') $args['method'] = 'get';	
		
	$urlres=load_url($newurl,$args);
	
	$http_code=$urlres['info']['http_code'];
	
	// save httpd code
	$arr["http_code"]=$http_code;
	
	// did a timeout occur
	if($http_code==0)
		$arr["timeout"]=true;
	
	// save body
	$arr["body"]=$urlres['body'];
	
	return;
	}
	
	
function embed_nagiosxi_magic_login($sid)
{
	$sinfo=get_server_info($sid);
	
	// Get saved credentials
	$sc = get_option("server_credentials");
	if ($sc == null) {
		$sc = array();
	} else {
		$sc = unserialize($sc);
	}

	$username = $sc[$sid]["username"];
	$password = $sc[$sid]["password"];
		
	$url=$sinfo["url"]."/backend/?cmd=getmagicpixel&username=".urlencode($username)."&password=".md5($password);
	$id="magiclogin_".random_string(6);

	$started = grab_array_var($_SESSION, 'server_sessions_started', false); 
	$age = grab_array_var($_SESSION, 'server_sessions_age', 0);
    
    if ($started == false || (time() - $age) > 1500) {
        $output = "<div id='$id'><img src='".$url."&r=".rand()."'></div>";
    } else {
        $output = "<div id='$id'></div>";
    }

    $output .= '<script type="text/javascript">
        setInterval(function()
        {
            rand=Math.random();
            $("#'.$id.'").html("").html(\'<img src="'.$url.'&r=\'+rand+\'">\');
        }, 900000);

			</script>';
	return $output;
}

function embed_nagios_core_magic_login($sid)
{
	$sinfo = get_server_info($sid);

	$creds = get_option("server_credentials");
	if ($creds == null) {
		$creds = array();
	} else {
		$creds = unserialize($creds);
	}

	$username = $creds[$sid]["username"];
	$password = $creds[$sid]["password"];

	$url = $sinfo["url"];
	if (substr($url, -1) != "/") { $url .= "/"; }
	$id = "magiclogin_".random_string(6);

    $output='<script type="text/javascript">

        setInterval(auth_'.$id.', 900000);

		function auth_'.$id.'() {
			$.ajax({
					url: "'.$url.'",
					crossDomain: true,
					headers: {
						Authorization: "Basic '.base64_encode($username.':'.$password).'"
					},
					sucess: function(result) {
						alert("done");
					}
				});
		}

	</script>';

	$started = grab_array_var($_SESSION, 'server_sessions_started', false); 
	$age = grab_array_var($_SESSION, 'server_sessions_age', 0);

	if ($started == false || (time() - $age) > 1500) {
        $output .= "<script>auth_".$id."();</script>";
    }

	return $output;
}
	
////////////////////////////////////////////////////////////////////////
// SERVER FUNCTIONS
////////////////////////////////////////////////////////////////////////

// returns an array of information about all servers
function get_servers(){

	// get servers list
	$servers_serial=get_option("servers");
	if($servers_serial==null)
		$servers=array();
	else
		$servers=unserialize($servers_serial);
		
	return $servers;
	}
	
// returns an array with information about a specific server
function get_server_info($sid){

	$servers=get_servers();
	
	$sinfo=array();
	
	if(array_key_exists($sid,$servers))
		$sinfo=$servers[$sid];

	return $sinfo;
	}
	
// adds/updates a server
function update_server($sinfo,$sid=""){

	// get servers list
	$servers=get_servers();
	
	// add
	if($sid==""){
		$sid=random_string(6);
		$servers[$sid]=$sinfo;
		}
	// update
	else{
		$servers[$sid]=$sinfo;
		}
		
	// save new servers list
	set_option("servers",serialize($servers));
	
	// return new servers list
	return $servers;
	}
	


// deletes a server
function delete_server($sid){

	// get servers list
	$servers=get_servers();
	
	// array of servers
	if(is_array($sid)){
		foreach($sid as $server_id){
			unset($servers[$server_id]);
			//remove data for any other subcomponents
			$args=array('server_id' => $server_id); 
			do_callbacks(CALLBACK_DELETE_SERVER,$args);
			//$query = "DELETE FROM fusion_tac_data WHERE server_sid='{$server_id}'"; 
			//exec_sql_query(DB_NAGIOSFUSION,$query,true);
		}		
	}	
	else {// single server
		unset($servers[$sid]);
		//remove data for any other subcomponents
		$args=array('server_id' => $server_id); 
		do_callbacks(CALLBACK_DELETE_SERVER,$args);
		//$query = "DELETE FROM fusion_tac_data WHERE server_sid='{$sid}'"; 
		//exec_sql_query(DB_NAGIOSFUSION,$query,true);
	}
	//echo "SID: {$sid}"; 	
			
	// save new servers list
	set_option("servers",serialize($servers));
	
	// return new servers list
	return $servers;
	}





/**
*	fetches assembled nagios XI backend url with '?' and authentication creds being passed 
*
*/ 
function get_nagiosxi_backend_url($sid) {

	global $cfg;

	$sinfo=get_server_info($sid);

	// check server
	$url=get_server_root_url($sid,false);
	
	// parse url
	$urlparts=parse_url($url);

	// get saved credentials
	$sc = get_option("server_credentials");
	if($sc==null)
		$sc=array();
	else
		$sc=unserialize($sc);

	// get credentials
	$username = grab_array_var($sc[$sid],"username");
	$password = md5(grab_array_var($sc[$sid],"password"));
	
	//$fa_data = array("username" => $username, "password" => $password);
	//$fa = base64_encode(serialize($fa_data));

	// authentication
	//$urlauth = "&fa=".$fa;
	$urlauth = "&username=".$username."&password=".$password;
	
	// construct new url
	$newurl=$urlparts["scheme"]."://".$urlparts["host"];
	if(array_key_exists("port",$urlparts))
		$newurl.=":".$urlparts["port"];

	$newurl.=$urlparts["path"]."/";
	$newurl.="backend/?";
	$newurl.=$urlauth;

	return $newurl; 		
}


/**
*	retuns an assembled core CGI URL for the core server with basic authentication, scheme, port number figured out 
*
*/ 
function get_nagioscore_backend_url($sid) {

	// get saved credentials
	$sc=(is_null(get_option("server_credentials"))) ? array() : unserialize(get_option("server_credentials"));
			
	// get credentials
	$username=grab_array_var($sc[$sid],"username");
	$password=grab_array_var($sc[$sid],"password");
	
	// we dont' have credentials
	if(!have_value($username) || !have_value($password))
		return false;

	//info array 	
	$sinfo = get_server_info($sid); 
	// check server
	$url=get_server_root_url($sid,false);
	
	// parse url
	$urlparts=parse_url($url);
	
	// authentication
	$basicauth="";
	$urlauth="";
	if($sinfo["auth"]=="basic")
		$basicauth=$username.":".$password."@";
	
	// construct new url
	$newurl=$urlparts["scheme"]."://".$basicauth.$urlparts["host"];
	if(array_key_exists("port",$urlparts))
		$newurl.=":".$urlparts["port"];
	$newurl.=$urlparts["path"]."/";
	$url = $newurl.'cgi-bin/';

	return $url; 
}


/**
*	loads http body from a requested URL 
*
*/

function get_server_data_from_url($url,$timeout=15,$return_info=true,$method='post') {

	$arr = array(); //return array 
	$args = array('timeout' =>$timeout,'return_info' =>$return_info, 'method' => $method); 
	//curl fetch for data  
	$urlres=load_url($url,$args);
	
	// did a timeout occur
	if($urlres['info']['http_code']==0)
		$arr["timeout"]=true;

	// save httpd code
	$arr["http_code"] = $urlres['info']['http_code'];
	$arr['body'] = $urlres['body']; 
	
	return $arr; 
}


/**
*	retrieves an array of hostnames from a selected XI server
*	@param string $sid: server ID 
*	@return mixed $hosts: viable hosts $XML object for selected server 
*/ 
function get_xiserver_host_list($sid) {
	global $cfg; 
	$timeout = grab_array_var($cfg,'default_timeout',15);  

	$url = get_nagiosxi_backend_url($sid); 
	$url.='&cmd=gethosts&orderby=host_name%3Aa&brevity=3'; 
	
	$raw_xml = get_server_data_from_url($url,$timeout); 

	return simplexml_load_string($raw_xml['body']); 
}

/**
*	retrieves an array of hostnames from a selected XI server
*	@param string $sid: server ID 
*	@return mixed $hosts: viable hosts $XML object for selected server 
*/ 
function get_xiserver_service_list($sid,$host) {
	global $cfg; 
	$timeout = grab_array_var($cfg,'default_timeout',15);  

	$url = get_nagiosxi_backend_url($sid); 
	$url.='&cmd=getservices&brevity=3&orderby=service_description%3Aa&host_name='.$host; 
	
	$raw_xml = get_server_data_from_url($url,$timeout); 

	return simplexml_load_string($raw_xml['body']); 

}


/**
*	retrieves an array of hostgroup names from a selected XI server
*	@param string $sid: server ID 
*	@return mixed $hostgroups: viable hosts $XML object for selected server, false on failure  
*/ 
function get_xiserver_hostgroup_list($sid) {
	global $cfg; 
	$timeout = grab_array_var($cfg,'default_timeout',15);  

	$url = get_nagiosxi_backend_url($sid); 
	$url.='&cmd=gethostgroups&orderby=hostgroup_name%3Aa&brevity=3'; 
	
	$raw_xml = get_server_data_from_url($url,$timeout); 

	return simplexml_load_string($raw_xml['body']); 
}


/**
*	retrieves an array of hostgroup names from a selected XI server
*	@param string $sid: server ID 
*	@return mixed $hostgroups: viable hosts $XML object for selected server, false on failure  
*/ 
function get_xiserver_servicegroup_list($sid) {
	global $cfg; 
	$timeout = grab_array_var($cfg,'default_timeout',15);  

	$url = get_nagiosxi_backend_url($sid); 
	$url.='&cmd=getservicegroups&orderby=servicegroup_name%3Aa&brevity=3'; 
	
	$raw_xml = get_server_data_from_url($url,$timeout); 

	return simplexml_load_string($raw_xml['body']); 
}


/**
*	Does the server session authentications and handles all logic around that
*
*/ 
function handle_server_authorizations()
{
	global $cfg;

	// Make sure auto login is enabled for Fusion
	$auto_login_enabled = grab_array_var($cfg, 'enable_auto_login', true);

	// Do magix pixel login if we're authenticated, but only do this if we need to
	// doing this for every page footer makes page loads VERY slow if one XI server has
	// a slow connection of if the connection is bad 
    if (is_authenticated() && $auto_login_enabled) {

		$servers = get_servers(); 
		foreach ($servers as $sid => $sinfo) {
			if ($sinfo['type'] == 'nagiosxi') {
				echo embed_nagiosxi_magic_login($sid); 	
			} else if ($sinfo['type'] == 'nagioscore') {
				//echo embed_nagios_core_magic_login($sid); 
			}
		}

		// Save the last time we've run this and that it worked	
        $_SESSION['server_sessions_started'] = true;
        $_SESSION['server_sessions_age'] = time(); 	
	}
}

?>