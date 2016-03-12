<?php //utils-recentalerts.inc.php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// subsystem polling functions for recent alerts 

//echo "register recent alerts!\n";
require_once(dirname(__FILE__).'/common.inc.php');

register_callback(CALLBACK_POLLING_FUNCTIONS,'poll_recent_alerts'); 


function poll_recent_alerts($type='',$args) {

	$session = grab_array_var($args,'session',false);

	if(!$session) return; //only run if there's an active session 

	echo "Polling Recent Alerts...\n"; 

	//use default active interval if a session is active 
	
	//clear stale data
	exec_sql_query(DB_NAGIOSFUSION,'TRUNCATE TABLE fusion_recent_alerts',true); 
	exec_sql_query(DB_NAGIOSFUSION,"SELECT setval('fusion_recent_alerts_id_seq', 1)",true);

	$servers=get_servers();
	$output=''; 
	foreach($servers as $sid => $sinfo){	
		if($sinfo['type']=='nagiosxi') {
			print "NAGIOSXI ALERT FETCH ".$sinfo['name']."\n"; 	
			list($error,$count) = get_nagiosxi_recent_alerts_data($sid,$sinfo,$output);
			print "COUNT :$count\n";		
		}//end if 
		if($sinfo['type']=='nagioscore') {
			print "NAGIOSCORE ALERT FETCH".$sinfo['name']."\n"; 
			list($error,$count)=get_nagioscore_recent_alerts_data($sid,$sinfo,$output); 
			print "COUNT :".$count."\n"; 			
		}//end if
	}//end foreach 
}//end poll_recent_alerts()


//XXX TODO: the data functions need to flush to DB 

function get_nagioscore_recent_alerts_data($sid,$sinfo,&$output) {
	global $cfg; 

	$error=false;
	$notifications=array(); 

	$sinfo = get_server_info($sid);
	// get saved credentials
	$sc=get_option("server_credentials");
	if($sc==null)
		$sc=array();
	else
		$sc=unserialize($sc);	
	
	// get credentials
	$username=grab_array_var($sc[$sid],"username");
	$password=grab_array_var($sc[$sid],"password");
	
	if(!have_value($username) || !have_value($password)) {
		$output.="<span class='noauthdefined'>No credentials defined for this server yet</span>\n";
		return array(true,array()); 
	} 

/*
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
	$url = $newurl.'cgi-bin/notifications.cgi?contact=all&noheader=true';

*/
	$url = get_nagioscore_backend_url($sid);
	$url.='notifications.cgi?contact=all&noheader=true'; 

	//echo $url."<br />"; 

	$timeout = grab_array_var($cfg, 'default_timeout', 20); 
	$result=load_url($url,array('method'=>'get','return_info'=>true, 'timeout' => $timeout));
	$http_code = $result['info']['http_code'];  
	$body =& $result['body'];  
	$size = $result['info']['size_download'];  //keep an eye on the data size 
			
	// check for auth/404 errors
	if($http_code > 400){
		$output.='<span class="tacservernotfound"><img src="'.theme_image("critical_small.png").'"> Unable to fetch data from server.</span>';
		return array(true,array());
	}
	if($http_code == 401){
		$output.='<span class="tacserverautherror"><img src="'.theme_image("critical_small.png").'"> Failed to authenticate to server.  <a href="'.get_base_url().'config/" target="_top">Change credentials</a>.</span>';
		return array(true,array());
	}

	//we only want the last 2 hours 
	$twohoursago = time()-(120*60); 

	//break data into chunks so we don't crash php 
	$rows = explode('</tr>',$body); 
	unset($body); 

	$count = count($rows); 
	$notifications = 0; 
	$dt_exists = class_exists('DateTime');
	//loop through table rows
	for($i=1; $i < $count; $i++) {

		//break string down by line
		$lines = explode("\n",strip_tags($rows[$i]));
		unset($rows[$i]); //free up some memory
		$date =  strip_tags($lines[5]);

		//there may not be any notifications
		if(empty($date))
			continue;

		if($dt_exists) {
			$d1 = DateTime::createFromFormat('m-d-Y H:i:s',$date);
			//drop everything older than 2 hours
			if((!$d1) || $d1->getTimestamp() < $twohoursago) break;
		}
		else
			$date=date('m-d-Y H:i:s',strtotime($date));

		//else
		//	$date=date('m-d-Y H:i:s',strtotime($date)); 
/*		
		//build new array entry 
		'server_name' => $sinfo['name'],
		'host_name' => $lines[2], 
		'service_description' => $lines[3],
		'notification_type' => $lines[4],  	
		'start_time' => $d1->getTimestamp(),
		'contact_name' => $lines[6],
		'notification_command' => $lines[7],
		'output' => $lines[8]);
*/
		//state value is 4 as a placeholder 
		$query = "INSERT INTO fusion_recent_alerts 
		(server_sid,server_name,host_name,service_description,type,start_time,contact_name,notification_command,state,output)
		VALUES ('$sid','".$sinfo['name']."','".$lines[02]."','".$lines[3]."','".$lines[4]."','".$date."',
		'".$lines[6]."','".$lines[7]."',4,'".substr(strval($lines[8]),0,511)."')";
				
		exec_sql_query(DB_NAGIOSFUSION,$query,true); 

		$notifications++;
	}

	return array($error,$notifications);
}


function get_nagiosxi_recent_alerts_data($sid,$sinfo,&$output) {

	global $cfg;
	$error=false; 
	$notifications=array(); 
	$timeout = grab_array_var($cfg,'default_timeout',15); 
	$now=time();
	$twohoursago=$now-(60*120); 
	$cmd="&cmd=getnotificationswithcontacts&starttime={$twohoursago}&endtime={$now}"; 

	$url = get_nagiosxi_backend_url($sid); 
	$url.=$cmd; 
	//echo $url; 
	$data = get_server_data_from_url($url,$timeout,true,'post');

	//echo $data['body'];
	$xml = @simplexml_load_string($data['body']); 
	
	if(!$xml) {  
		$pieces=explode("\n",$data['body'],5); 
		file_put_contents('/usr/local/nagiosfusion/var/components/recentalertsfail.log',print_r($pieces,true),FILE_APPEND);
		return array(true,0); 
	}
	$name = $sinfo['name'];
	$notifications=0;
	
	foreach($xml->notification as $n) {
		
		$time = strtotime("$n->start_time");

		//flush to DB 					
		$query = "INSERT INTO fusion_recent_alerts 
		(server_sid,server_name,host_name,service_description,type,start_time,contact_name,notification_command,state,output)
		VALUES ('$sid','$name','{$n->host_name}','{$n->service_description}','{$n->notification_reason}','{$n->start_time}',
		'{$n->contact_name}','{$n->notification_command}',{$n->state},'".substr(strval($n->output),0,511)."')";
				
		exec_sql_query(DB_NAGIOSFUSION,$query,true); 
		$notifications++; 
	}	
	
	return array($error,$notifications);

}



/**
*	
*
*/ 
function get_alert_histogram_xml($sid,$type,$args=array()) {
	$xml = false; 

	if($type=='nagiosxi') {
		//http://192.168.5.59/nagiosxi//backend/?&username=nagiosadmin&password=426d184e674e6c3020d38bde1d7ed35d&cmd=getalerthistogram
		$url = get_nagiosxi_backend_url($sid);
		$cmdurl = $url."&cmd=getalerthistogram"; 

		//echo "URL: $cmdurl<br />"; 
	
		//allow for arguments to added later 
	
		$arr = get_server_data_from_url($cmdurl,15,true,'post');
		//return false if there was a problem 
		if($arr['http_code'] > 399 || $arr['http_code'] < 200 || $arr['http_code']==0)
			return false;
			
		$xmlraw = &$arr['body']; 

	}
	if($type=='nagioscore'){	
		//return false; 
			
		$raw_data = parse_core_alert_history($sid);
		$xmlraw = core_alerts_to_xml($raw_data); 
	}

	//print "XML: <br />"; 
	//print $xml;
	$xml=@simplexml_load_string($xmlraw); 

	return $xml; 
	

}


function parse_core_alert_history($sid){
	global $cfg;

	$url = get_nagioscore_backend_url($sid);
	$url.='history.cgi?host=all&noheader=true'; 

	//echo $url."<br />"; 

	$timeout = grab_array_var($cfg, 'default_timeout', 30); 
	$result=load_url($url,array('method'=>'get','return_info'=>true, 'timeout' => $timeout));
	$http_code = $result['info']['http_code'];  
	$body =& $result['body'];  
	$size = $result['info']['size_download'];  //keep an eye on the data size 
			
	// check for auth/404 errors
	if($http_code > 399 || $http_code < 200)
		return false; 


	//break data into chunks so we don't crash php 
	$chunks = explode("<DIV CLASS='logEntries'>",$body); 
	//free some memory 
	unset($result); 

	//create array to store alert values by hour 
	$alerts = array(); 
	//start at the current hour and go down from there 
	$hours = intval(date('G')); 
	//loop through table rows 
	$i=0;
	foreach($chunks as $chunk) {
		if($i++ < 2) continue; 
				 
		$lines = explode("\n",$chunk);
		$alertcount = count($lines) - 7; //7 lines of extra content on a Core page 
		$alerts[$hours] = $alertcount;
//		echo "<br />************************* $alertcount ****************************<br />"; 
//print $chunk;

		$hours--;
	}

	//array_dump($alerts); 

	return $alerts; 
}


function core_alerts_to_xml($raw_data){

	$xmlstring='<?xml version="1.0" encoding="utf-8"?>
			<histogramdata>'; 
	
	for($i=0 ; $i<count($raw_data) ; $i++) {
		$total = isset($raw_data[$i]) ? $raw_data[$i] : 0;
		$xmlstring.="
			<histogramelement>
				<total>{$total}</total>
			</histogramelement>\n"; 
	} 
	$xmlstring.="</histogramdata>\n"; 

	return $xmlstring;

	
}

?>