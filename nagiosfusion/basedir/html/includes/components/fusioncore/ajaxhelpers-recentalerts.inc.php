<?php //ajaxhelpers-recentalerts.inc.php



/**
*	main ajax html function for loading recent alerts 
*	@return string $output large html content string 
*/ 
function fusioncore_ajax_get_recent_alerts_html($args=array()) {
	
	//array_dump($args);
	$mode = grab_array_var($args,'mode',DASHLET_MODE_OUTBOARD); 	
		
	$output = ''; 
	$notifs= get_recent_alerts_from_db($args);
	$servers = get_servers(); 
	//array_dump($servers);
	
	
	//begin html output 
	if($mode==DASHLET_MODE_OUTBOARD) { //main page 
		$output .= "<table class='standardtable'>\n<thead><tr>
				<th><a href='recentalerts.php?sort=server_name'>".gettext("Server")."</a></th>
				<th><a href='recentalerts.php?sort=host_name'>".gettext("Host")."</a></th>
				<th><a href='recentalerts.php?sort=service_description'>".gettext("Service")."</a></th>
				<th>".gettext("Type")."</th>
				<th>".gettext("State")."</th>
				<th><a href='recentalerts.php?sort=start_time'>".gettext("Time")."</a></th>
				<th><a href='recentalerts.php?sort=contact_name'>".gettext("Contact")."</a></th>
				<th>".gettext("Output")."</th></tr></thead><tbody>\n"; 
	}
	else //no filtering for dashlets 
		$output .= "<table class='standardtable'>\n<thead><tr>
			<th>".gettext("Server")."</th>
			<th>".gettext("Host")."</th>
			<th>".gettext("Service")."</th>
			<th>".gettext("Type")."</th>
			<th>".gettext("State")."</th>
			<th>".gettext("Time")."</th>
			<th>".gettext("Contact")."</th>
			<th>".gettext("Output")."</th>
			</tr>
			</thead><tbody>\n"; 			

	$count = 0; 
	foreach($notifs as $n) {
		$server = $servers[$n['server_sid']]; //which server? 
		$service = ($n['service_description'] == '') ? 'N/A' : $n['service_description'];  
		$object_type = ($service=='N/A') ? OBJECTTYPE_HOST : OBJECTTYPE_SERVICE; 
		$class = ($count++ % 2 == 1) ? 'even' : 'odd'; 		
		$type = (is_numeric($n['type'])) ? get_notification_reason_string($n['type'],$object_type,$n['state']) : $n['type']; 
		$state = ($n['state'] == 4 ) ? $n['type'] : get_text_state($object_type,$n['state']); 
		$td_state_class = str_replace(' ','_',$state);
		$td_type_class = str_replace(' ','_',$type);
		$host_url = get_object_details_url($server['type'],$server['url'],OBJECTTYPE_HOST,$n['host_name'],$service);
		$service_url = get_object_details_url($server['type'],$server['url'],OBJECTTYPE_SERVICE,$n['host_name'],$service);
		
		$output.="<tr class='$class'>
			<td><div class='td_120'><a href='{$server['url']}' title='{$n['server_name']}' target='_blank'>{$n['server_name']}</a></div></td>
			<td>$host_url</td>
			<td>$service_url</td>
			<td class='$td_type_class'>$type</td>
		    <td class='$td_state_class'>$state</td>
			<td><div class='td_120'>{$n['start_time']}</div></td>
			<td>{$n['contact_name']}</td>			
			<td>{$n['output']}</td>
		</tr>"; 	
		
	}	 
	
	$output .="</tbody></table>"; 
	$output.=gettext("Last Update").": ".date('r');

	return $output; 
	
	
}


/**
*	this function will need to handle query arguments, build the query, and return the results as an array 
*
*/ 

function get_recent_alerts_from_db($args=array()) {
	
	$notifs = array(); 
	$limit = grab_array_var($args,'limit',false); 
	$sort =  grab_array_var($args, 'sort','start_time'); 

	$query = "SELECT * FROM fusion_recent_alerts ";
	
	//add filtering options later 
	if($sort=='start_time')
		$query.="ORDER BY ".escape_sql_param($sort,'pgsql')." DESC"; 
	else 
		$query.="ORDER BY ".escape_sql_param($sort,'pgsql')." ASC"; 	

	if($limit)
		$query.=" LIMIT ".escape_sql_param($limit,'pgsql'); 
		
	//echo $query; 	
	
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true); 
	//add a checker for empty result set, make sure table isn't truncated when we make the call
	if($rs->RecordCount()==0) {
		sleep(2);
		$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true);		
	}	
		
	//return as an array 
	foreach($rs as $row) 
		$notifs[] = $row;   
    	
	return $notifs; 

}


/**
*	returns the text state for a host or service 
*/
function get_text_state($type,$state) {

	$text = ''; 
	if($type==OBJECTTYPE_SERVICE) { //object type is host
		switch($state) {
			case 0:
			$text='OK'; 
			break; 			
			case 1:
			$text = 'WARNING';
			break;			
			case 2:
			$text = 'CRITICAL';
			break; 			
			case 3:
			default:
			$text = 'UNKNOWN'; 
			break; 				
		}	
	}
	else { //host 
		switch($state) {
			case 0:
			$text='UP';
			break;
			case 1:
			$text='DOWN';
			break; 
			case 2:
			$text='UNREACHABLE';
			break; 			
		}	
	}	 
	
	return $text; 
}	


function get_object_details_url($servertype,$baseurl,$object_type,$host,$service) {

	if($service=='N/A' && $object_type==OBJECTTYPE_SERVICE) return $service; //skip if we don't need a link 
	
	$url = ''; 
	if($servertype=='nagiosxi') {
		if($object_type==OBJECTTYPE_HOST) 
			$url = 'includes/components/xicore/status.php?show=hostdetail&host='.$host; 	
		else
			$url = 'includes/components/xicore/status.php?show=servicedetail&host='.$host.'&service='.$service.'&dest=auto'; 
	}
	else
		$url = 'cgi-bin/extinfo.cgi?type='.$object_type.'&host='.str_replace(' ','+',$host).'&service='.str_replace(' ','+',$service);  
	

	$url = $baseurl.htmlentities($url); 
	$text = ($object_type==OBJECTTYPE_HOST) ? $host : $service; 
	$link = "<a href='$url' title='Object Details' target='_blank'>".htmlentities($text)."</a>"; 

	return $link;
	
}


/**
*	XI function
*
*/ 
function get_notification_reason_string($reason,$object_type,$state){
		
	$text="";
	
	switch($reason){
		case NOTIFICATIONREASON_NORMAL:
			//text="Normal";
			if($object_type==OBJECTTYPE_HOST){
				if($state==0)
					$text=gettext("Host Recovery");
				else
					$text=gettext("Host Problem");
				}
			else{
				if($state==0)
					$text=gettext("Service Recovery");
				else
					$text=gettext("Service Problem");
				}
			break;
		case NOTIFICATIONREASON_ACKNOWLEDGEMENT:
			$text=gettext("Problem Acknowledged");
			break;
		case NOTIFICATIONREASON_FLAPPINGSTART:
			$text=gettext("Flapping Started");
			break;
		case NOTIFICATIONREASON_FLAPPINGSTOP:
			$text=gettext("Flapping Stopped");
			break;
		case NOTIFICATIONREASON_FLAPPINGDISABLED:
			$text=gettext("Flapping Disabled");
			break;
		case NOTIFICATIONREASON_DOWNTIMESTART:
			$text=gettext("Downtime Started");
			break;
		case NOTIFICATIONREASON_DOWNTIMEEND:
			$text=gettext("Downtime Ended");
			break;
		case NOTIFICATIONREASON_DOWNTIMECANCELLED:
			$text=gettext("Downtime Cancelled");
			break;
		case NOTIFICATIONREASON_Custom:
			$text=gettext("Custom");
			break;
		default:
			break;
		}
	
	return $text;
	}


?>