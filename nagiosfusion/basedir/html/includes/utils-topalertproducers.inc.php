<?php //utils-topalertproducers.inc.php

//
// Copyright (c) 2008-2012 Nagios Enterprises, LLC.  All rights reserved.
//
// subsystem polling functions for topalertproducers 

//echo "register recent alerts!\n";
require_once(dirname(__FILE__).'/common.inc.php');

register_callback(CALLBACK_POLLING_FUNCTIONS,'poll_topalertproducers'); 


function poll_topalertproducers($type='',$args) {

	//print_r($args);

	$session = grab_array_var($args,'session',false);
	$first_run = grab_array_var($args,'first_run',0);
	
	if(!$session) return; //only run if there's an active session 
	if($first_run==0) return; //only poll every 5mn if a session is active 
	
	echo "Polling Top Alert Producers...\n"; 
		
	 		
	//use default active interval if a session is active 
	
	//clear stale data
	echo "TRUNCATING topalertproducers table\n"; 
	exec_sql_query(DB_NAGIOSFUSION,'TRUNCATE TABLE fusion_topalertproducers',true); 
	exec_sql_query(DB_NAGIOSFUSION,"SELECT setval('fusion_topalertproducers_id_seq', 1)",true);

	$servers=get_servers();
	$output=''; 
	foreach($servers as $sid => $sinfo){	
		if($sinfo['type']=='nagiosxi') {
			//print "NAGIOSXI TAP FETCH ".$sinfo['name']."\n"; 	
			$error = get_nagiosxi_topalertproducers($sid,$sinfo,$output);
	
		}//end if 
		if($sinfo['type']=='nagioscore') {
			//print "NAGIOSCORE TAP FETCH ".$sinfo['name']."\n"; 
			$error=get_nagioscore_topalertproducers($sid,$sinfo,$output); 
			
		}//end if
	}//end foreach 
}//end poll_recent_alerts()


//XXX TODO: the data functions need to flush to DB 

function get_nagioscore_topalertproducers($sid,$sinfo,&$output) {
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
		$output.="<span class='noauthdefined'>".gettext("No credentials defined for this server yet")."</span>\n";
		return array(true,array()); 
	} 

	$url = get_nagioscore_backend_url($sid);
	//core URL table 
	$url.='summary.cgi?report=1&displaytype=3&timeperiod=last24hours&host=all&alerttypes=3&statetypes=3&hoststates=7&servicestates=120&limit=25&noheader=true'; 

	//echo $url."<br />"; 

	$timeout = grab_array_var($cfg, 'default_timeout', 20); 
	$result=load_url($url,array('method'=>'get','return_info'=>true, 'timeout' => $timeout));
	$http_code = $result['info']['http_code'];  
	$body =& $result['body'];  
	$size = $result['info']['size_download'];  //keep an eye on the data size 
			
	// check for auth/404 errors
	if($http_code > 400 || $http_code==401){
		$output.='<span class="tacservernotfound"><img src="'.theme_image("critical_small.png").'"> '.gettext('Unable to fetch data from server').'.</span>';
		return array(true,array());
	}

	//break data into chunks so we don't crash php 
	$rows = explode('</tr>',$body); 
	$name = $sinfo['name']; 
	$i=0;
	//loop through table rows 
	foreach($rows as $row) {

		//break string down by line
		$tds = explode("</td>",$row);
		//if(!isset($tds[2])) continue; //not a line we need 		
		
		$rank = isset($tds[0]) ? strip_tags($tds[0]) : '';
		$host = isset($tds[2]) ? strip_tags($tds[2]) : '';
		$service = isset($tds[3]) ? strip_tags($tds[3]) : '';
		$alertcount = isset($tds[4]) ? strip_tags($tds[4]) : ''; 
		
		//free up some memory
		unset($tds); 
		//unset($rows[$i]);
		
		//sanity check and cleanup on rank
		$rank = trim(str_replace('#','',$rank));		
		if($rank=='') continue; 
		if($i==0) $rank = 1; //special case for first part of page

		//flush to DB 					
		$query = "INSERT INTO fusion_topalertproducers
		(server_sid,server_name,host_name,service_description,rank,alert_count)
		VALUES ('$sid','$name','{$host}','{$service}','{$rank}','{$alertcount}')";
				
		exec_sql_query(DB_NAGIOSFUSION,$query,true); 	
		$i++;			
	}
	
	echo "Saved top alert producers for {$name}\n";

	return $error;
}


function get_nagiosxi_topalertproducers($sid,$sinfo,&$output) {

	global $cfg;
	$error=false; 
	$timeout = grab_array_var($cfg,'default_timeout',20); 

	$cmd="&cmd=gettopalertproducers&reportperiod=last24hours&records=25"; 
	$url = get_nagiosxi_backend_url($sid); 
	$url.=$cmd; 
	//echo $url; 
	
	$name = $sinfo['name'];
	$rank=0; //top alert producer rank 
	
	$data = get_server_data_from_url($url,$timeout,true,'post');

	//echo $data['body'];
	$xml = simplexml_load_string($data['body']); 
	
	if(!$xml)  return true; //bail if there's nothing there 
	
	foreach($xml->producer as $n) {		
		$rank++;

		//flush to DB 					
		$query = "INSERT INTO fusion_topalertproducers 
		(server_sid,server_name,host_name,service_description,rank,alert_count)
		VALUES ('$sid','$name','{$n->host_name}','{$n->service_description}',".$rank.",".intval($n->total_alerts).")";
				
		exec_sql_query(DB_NAGIOSFUSION,$query,true); 
	}	
	
	echo "Saved top alert producers for {$name}\n"; 	
	
	return $error;
}


function get_topalertproducers_from_db($args) {

	$taps = array(); 
	$limit = grab_array_var($args,'limit',false); 
	$sort =  grab_array_var($args, 'sort','alert_count'); 
	//$sortorder = grab_array_var($args,'sortorder','DESC'); 

	$query = "SELECT * FROM fusion_topalertproducers ";
	
	//add filtering options later 
	if($sort=='alert_count') 
		$sortorder ='DESC'; 
	else 
		$sortorder = 'ASC'; 
	
	//if($sort!="server_name") $sort.=",server_name";
	
	$query.="ORDER BY ".escape_sql_param($sort,'pgsql')." $sortorder "; 
 	
	if($limit)
		$query.=" LIMIT ".escape_sql_param($limit,'pgsql'); 
		
	//echo $query; 	
	
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true); 
	//add a checker for empty result set, make sure table isn't truncated when we make the call
	if($rs && $rs->RecordCount()==0) {
		sleep(2);
		$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true);		
	}	
	
	if(!$rs) return false; 
		
	//return as an array 
	foreach($rs as $row) 
		$taps[] = $row;   
    	
	return $taps; 

}

?>