<?php //utils for gathering and processing tactical overview data





function get_nagiosxi_tac_overview_data($sid,$username,$password,&$output){
	
	$output="";
	$hxml=false;
	$sxml=false;
	$mxml=false;
	
	///////////////////////////////////////
	// get host status data
	///////////////////////////////////////
	$host_data=array();
	get_server_data($sid,$username,$password,"hoststatus",$host_data);

	// check for errors...
	$error=false;
	
	// check for timeout
	if($host_data["timeout"]==true){
		$output.='<span class="tacservertimeout"><img src="'.theme_image("critical_small.png").'"> '.gettext('A timeout occurred while attempting to contact the server').'.</span>';
		$error=true;
		}
		
	// check for auth error
	if($error==false){
		if($host_data["http_code"]==401){
			$output.='<span class="tacserverautherror"><img src="'.theme_image("critical_small.png").'"> '.gettext('Failed to authenticate to server').'.  
						<a href="'.get_base_url().'config/" target="_top">'.gettext('Change credentials').'</a>.</span>';
			$error=true;
			}
		}

	// make sure we can parse XML
	if($error==false){
		// load xml
		$hxml=simplexml_load_string($host_data["body"]);
		if(!$hxml){
			$output.='<span class="tacserverparseerror"><img src="'.theme_image("critical_small.png").'"> '.gettext('Unable to parse Nagios XI server response').'.</span>';
			$error=true; 
			}
		}
			
	///////////////////////////////////////
	// get service status data
	///////////////////////////////////////
	if($error==false){

		$service_data=array();
		get_server_data($sid,$username,$password,"servicestatus",$service_data);
		
		// check for errors...
		$error=false;
		
		// check for timeout
		if($service_data["timeout"]==true){
			$output.='<span class="tacservertimeout"><img src="'.theme_image("critical_small.png").'"> '.gettext('A timeout occurred while attempting to contact the server').'.</span>';
			$error=true;
			}
	}
		
	// make sure we can parse XML
	if($error==false){
		// load xml
		$sxml=simplexml_load_string($service_data["body"]);
		if(!$sxml){
			$output.='<span class="tacserverparseerror"><img src="'.theme_image("critical_small.png").'"> '.gettext('Unable to parse server response').'.</span>';
			$error=true;
		}
	}

	///////////////////////////////////////
	// Get program status data
	///////////////////////////////////////
	if ($error == false) {
		$program_data = array();
		get_server_data($sid,$username, $password, "programstatus", $program_data);
		
		// check for errors...
		$error = false;
		
		// check for timeout
		if ($program_data["timeout"] == true) {
			$output .= '<span class="tacservertimeout"><img src="'.theme_image("critical_small.png").'"> '.gettext('A timeout occurred while attempting to contact the server').'.</span>';
			$error = true;
		}
	}

	// make sure we can parse XML
	if ($error == false) {
		// load xml
		$mxml=simplexml_load_string($program_data["body"]);
		if (!$mxml) {
			$output .= '<span class="tacserverparseerror"><img src="'.theme_image("critical_small.png").'"> '.gettext('Unable to parse server response').'.</span>';
			$error = true;
		}
	}
	
	return array($error,$hxml,$sxml, $mxml); 
	
} //end get_nagiosxi_tac_overview_data



function get_nagiosxi_server_tac_stats($xmlh,$xmls,$mxml){
	$hosts_up_disabled=0;
	$hosts_up_total=0;
	$hosts_down_disabled=0;
	$hosts_down_scheduled=0;
	$hosts_down_acknowledged=0;
	$hosts_down_unhandled=0;
	$hosts_down_total=0;
	$hosts_unreachable_disabled=0;
	$hosts_unreachable_scheduled=0;
	$hosts_unreachable_acknowledged=0;
	$hosts_unreachable_unhandled=0;
	$hosts_unreachable_total=0;
	$hosts_pending_disabled=0;
	$hosts_pending_total=0;
	
	$services_ok_disabled=0;
	$services_ok_total=0;
	$services_pending_disabled=0;
	$services_pending_total=0;
	$services_warning_total=0;
	$services_warning_disabled=0;
	$services_warning_acknowledged=0;
	$services_warning_scheduled=0;
	$services_warning_hostproblem=0;
	$services_warning_unhandled=0;
	$services_critical_total=0;
	$services_critical_disabled=0;
	$services_critical_acknowledged=0;
	$services_critical_scheduled=0;
	$services_critical_hostproblem=0;
	$services_critical_unhandled=0;
	$services_unknown_total=0;
	$services_unknown_disabled=0;
	$services_unknown_acknowledged=0;
	$services_unknown_scheduled=0;
	$services_unknown_hostproblem=0;
	$services_unknown_unhandled=0;
	
	$problem=false;
	
	foreach($xmls->servicestatus as $ss){

		$state=intval($ss->current_state);
		$hbc=intval($ss->has_been_checked);
		$active_checks=intval($ss->active_checks_enabled);
		$flapping=intval($ss->is_flapping);
		$downtime=intval($ss->scheduled_downtime_depth);
		$ack=intval($ss->problem_acknowledged);
		$hoststate=intval($ss->host_current_state);
		
		// fake pending state
		if($hbc==0)
			$state=-1;
			
		switch($state){
			case 0:
				$services_ok_total++;
				if($active_checks!=1)
					$services_ok_disabled++;
				break;
			case 1:
				$problem=true;
				$services_warning_total++;
				if($active_checks!=1){
					$services_warning_disabled++;
					$problem=false;
					}
				if($downtime>0){
					$services_warning_scheduled++;
					$problem=false;
					}
				if($ack==1){
					$services_warning_acknowledged++;
					$problem=false;
					}
				if($hoststate!=0){
					$services_warning_hostproblem++;
					$problem=false;
					}
				if($problem==true)
					$services_warning_unhandled++;
				break;
			case 2:
				$problem=true;
				$services_critical_total++;
				if($active_checks!=1){
					$services_critical_disabled++;
					$problem=false;
					}
				if($downtime>0){
					$services_critical_scheduled++;
					$problem=false;
					}
				if($ack==1){
					$services_critical_acknowledged++;
					$problem=false;
					}
				if($hoststate!=0){
					$services_critical_hostproblem++;
					$problem=false;
					}
				if($problem==true)
					$services_critical_unhandled++;
				break;
			case 3:
				$problem=true;
				$services_unknown_total++;
				if($active_checks!=1){
					$services_unknown_disabled++;
					$problem=false;
					}
				if($downtime>0){
					$services_unknown_scheduled++;
					$problem=false;
					}
				if($ack==1){
					$services_unknown_acknowledged++;
					$problem=false;
					}
				if($hoststate!=0){
					$services_unknown_hostproblem++;
					$problem=false;
					}
				if($problem==true)
					$services_unknown_unhandled++;
				break;
			case -1:
				$services_pending_total++;
				if($active_checks!=1)
					$services_pending_disabled++;
				break;
			default:
				break;
			}
		
		}
	
	foreach($xmlh->hoststatus as $hs){
	
		$state=intval($hs->current_state);
		$hbc=intval($hs->has_been_checked);
		$active_checks=intval($hs->active_checks_enabled);
		$flapping=intval($hs->is_flapping);
		$downtime=intval($hs->scheduled_downtime_depth);
		$ack=intval($hs->problem_acknowledged);
		
		
		// fake pending state
		if($hbc==0)
			$state=-1;
		
		switch($state){
			case 0:
				$hosts_up_total++;
				if($active_checks!=1)
					$hosts_up_disabled++;
				break;
			case 1:
				$problem=true;
				$hosts_down_total++;
				if($active_checks!=1){
					$hosts_down_disabled++;
					$problem=false;
					}
				if($downtime>0){
					$hosts_down_scheduled++;
					$problem=false;
					}
				if($ack==1){
					$hosts_down_acknowledged++;
					$problem=false;
					}
				if($problem==true)
					$hosts_down_unhandled++;
				break;
			case 2:
				$problem=true;
				$hosts_unreachable_total++;
				if($active_checks!=1){
					$hosts_unreachable_disabled++;
					$problem=false;
					}
				if($downtime>0){
					$hosts_unreachable_scheduled++;
					$problem=false;
					}
				if($ack==1){
					$hosts_unreachable_acknowledged++;
					$problem=false;
					}
				if($problem==true)
					$hosts_unreachable_unhandled++;
				break;
			case -1:
				$hosts_pending_total++;
				if($active_checks!=1)
					$hosts_pending_disabled++;
				break;
			default:
				break;
			}
		}
		
	$xstr="<tacinfo>";
	
	$xstr.="<hoststatustotals>";

	$xstr.="<unreachable><total>".$hosts_unreachable_total."</total><disabled>".$hosts_unreachable_disabled."</disabled><unhandled>".$hosts_unreachable_unhandled."</unhandled><scheduleddowntime>".$hosts_unreachable_scheduled."</scheduleddowntime><acknowledged>".$hosts_unreachable_acknowledged."</acknowledged></unreachable>";

	$xstr.="<down><total>".$hosts_down_total."</total><disabled>".$hosts_down_disabled."</disabled><unhandled>".$hosts_down_unhandled."</unhandled><scheduleddowntime>".$hosts_down_scheduled."</scheduleddowntime><acknowledged>".$hosts_down_acknowledged."</acknowledged></down>";

	$xstr.="<up><total>".$hosts_up_total."</total><disabled>".$hosts_up_disabled."</disabled></up>";

	$xstr.="<pending><total>".$hosts_pending_total."</total><disabled>".$hosts_pending_disabled."</disabled></pending>";
	
	$xstr.="</hoststatustotals>";
	
	$xstr.="<servicestatustotals>";
	
	$xstr.="<ok><total>".$services_ok_total."</total><disabled>".$services_ok_disabled."</disabled></ok>";
	
	$xstr.="<warning><total>".$services_warning_total."</total><disabled>".$services_warning_disabled."</disabled><unhandled>".$services_warning_unhandled."</unhandled><scheduleddowntime>".$services_warning_scheduled."</scheduleddowntime><acknowledged>".$services_warning_acknowledged."</acknowledged><hostproblem>".$services_warning_hostproblem."</hostproblem></warning>";

	$xstr.="<unknown><total>".$services_unknown_total."</total><disabled>".$services_unknown_disabled."</disabled><unhandled>".$services_unknown_unhandled."</unhandled><scheduleddowntime>".$services_unknown_scheduled."</scheduleddowntime><acknowledged>".$services_unknown_acknowledged."</acknowledged><hostproblem>".$services_unknown_hostproblem."</hostproblem></unknown>";

	$xstr.="<critical><total>".$services_critical_total."</total><disabled>".$services_critical_disabled."</disabled><unhandled>".$services_critical_unhandled."</unhandled><scheduleddowntime>".$services_critical_scheduled."</scheduleddowntime><acknowledged>".$services_critical_acknowledged."</acknowledged><hostproblem>".$services_critical_hostproblem."</hostproblem></critical>";

	$xstr.="<pending><total>".$services_pending_total."</total><disabled>".$services_pending_disabled."</disabled></pending>";
	
	$xstr.="</servicestatustotals>";

	$xstr .= "<monitoringfeaturestatus>	
			<flapdetection>
				<global>".intval($mxml->programstatus->flap_detection_enabled)."</global>
			</flapdetection>
			<notifications>
				<global>".intval($mxml->programstatus->notifications_enabled)."</global>
			</notifications>
			<eventhandlers>
				<global>".intval($mxml->programstatus->event_handlers_enabled)."</global>
			</eventhandlers>
			<activeservicechecks>
				<global>".intval($mxml->programstatus->active_service_checks_enabled)."</global>
			</activeservicechecks>
			<passiveservicechecks>		
				<global>".intval($mxml->programstatus->passive_host_checks_enabled)."</global>
			</passiveservicechecks>
		</monitoringfeaturestatus>";

	$xstr.="</tacinfo>";
		
	$xml=simplexml_load_string($xstr);
	
	return $xml;
	}
	

	
/**
*	processes nagioscore curl fetch into XML data for tac
*/ 	
function get_nagioscore_tac_overview_data($sid,$username,$password,&$output){
	
	$output="";
	

	///////////////////////////////////////
	// get tac overview data
	///////////////////////////////////////
	$tac_data=array();
	get_server_data($sid,$username,$password,"tac",$tac_data);
	
	// check for errors...
	$error=false;
	
	// check for timeout
	if($tac_data["timeout"]==true){
		$output.='<span class="tacservertimeout"><img src="'.theme_image("critical_small.png").'"> A timeout occurred while attempting to contact the server.</span>';
		$error=true;
	}
		
	// check for auth/404 errors
	if($error==false){
		if($tac_data["http_code"]==401){
			$output.='<span class="tacserverautherror"><img src="'.theme_image("critical_small.png").'"> Failed to authenticate to server.  <a href="'.get_base_url().'config/" target="_top">Change credentials</a>.</span>';
			$error=true;
			}
		if($tac_data["http_code"]==404){
			$output.='<span class="tacservernotfound"><img src="'.theme_image("critical_small.png").'"> Unable to find the required Nagios Core XML CGI.</span>';
			$error=true;
			}
		}
		
	// make sure we can parse XML
	if($error==false) {
		//////////////////////////////////////////
		
		//parse all hyperlinks from the tactical overview 
		preg_match_all('@<a href=(.+?)>(.+?)</a>@is',$tac_data['body'],$links); //removed href=

		$string = '';
		
		///////DEBUG//////////
		//print $tac_data['body'];
		//print_r($links[0]);
		
		//turn preg results into a single string 
		foreach($links[0] as $data) 
			$string .=  tidy_repair_string(trim($data), array('output-xml' => true, 'input-xml' => true) ) ;
		
		//Added to handle Active Service Checks
		if(strpos($tac_data['body'], 'Active Checks Disabled') !== false)
			$string .=  tidy_repair_string(trim('cmd.cgi?cmd_typ=35'), array('output-xml' => true, 'input-xml' => true) ) ;
		if(strpos($tac_data['body'], 'Passive Checks Disabled') !== false)
			$string .=  tidy_repair_string(trim('cmd.cgi?cmd_typ=37'), array('output-xml' => true, 'input-xml' => true) ) ;
		//turn links into xml object
		//$prep = tidy_repair_string(trim($string), array('output-xml' => true, 'input-xml' => true) );

		$prep = '<?xml version="1.0" encoding="utf-8"?>'."\n<data>".$string."</data>";
		
		//print "PREP: \n".$prep;
		
		$prep_xml = simplexml_load_string($prep);
		
		if(!$prep_xml)
			echo "ERROR: Failed to build XML for sid: {$sid}".print_r(libxml_get_errors(),true);	
			
		//echo "PREPXML: \n".print_r($prep_xml,true);	

		//create assoc array from the xml 
		$link_vals = array(); 
		foreach($prep_xml as $x){
			//capture the count integer 

			$vals = explode(' ',trim($x));
			//create array( URL => count) 
			$key = "{$x['href']}";
			$link_vals[$key] = $vals[0]; 
		}
		
		// DEBUG
		//print "SID: $sid\n\n";
		//print_r($link_vals);
		
		// load xml
		//parse URLs grabbed from core tac, determine values and build xml 
		$xml = nagioscore_links_to_xml($link_vals);
	}

	return array($error,$xml);

}//end get_nagioscore_tac_overview_data	


/**
*	helper function to turn nagioscore tac html into valid XML data.  Parses links in core page for the actual data 
*/ 
function nagioscore_links_to_xml($link_vals)
{
	 
	//create template for xml data needed for the fusion tac table, set default counts to 0 
	$xml_template = '<?xml version="1.0" encoding="utf-8"?>';
	$xml_template .=<<<XML
	<tacinfo>	
		<!-- hosts -->
		<hoststatustotals>
			<down>
				<total>0</total>
				<unhandled>0</unhandled>
				<scheduleddowntime>0</scheduleddowntime>	
				<acknowledged>0</acknowledged>
				<disabled>0</disabled>
			</down>
			<unreachable>
				<total>0</total>
				<unhandled>0</unhandled>
				<scheduleddowntime>0</scheduleddowntime>
				<acknowledged>0</acknowledged>
				<disabled>0</disabled>
			</unreachable>	
			<up>
				<total>0</total>
				<disabled>0</disabled>
			</up>
			<pending>
				<total>0</total>
				<disabled>0</disabled>
			</pending>
		</hoststatustotals>
		
		<!-- services -->
		<servicestatustotals>
			<warning>
				<total>0</total>	
				<unhandled>0</unhandled>
				<scheduleddowntime>0</scheduleddowntime>
				<acknowledged>0</acknowledged>
				<hostproblem>0</hostproblem>
				<disabled>0</disabled>
			</warning>
			<unknown>
				<total>0</total>
				<unhandled>0</unhandled>
				<scheduleddowntime>0</scheduleddowntime>	
				<acknowledged>0</acknowledged>
				<hostproblem>0</hostproblem>
				<disabled>0</disabled>
			</unknown>
			<critical>
				<total>0</total>
				<unhandled>0</unhandled>
				<scheduleddowntime>0</scheduleddowntime>
				<acknowledged>0</acknowledged>
				<hostproblem>0</hostproblem>	
				<disabled>0</disabled>
			</critical>
			<ok>
				<total>0</total>
				<disabled>0</disabled>
			</ok>
			<pending>
				<total>0</total>
				<disabled>0</disabled>
			</pending>
		</servicestatustotals>
		
		<!-- monitoring features -->
		<monitoringfeaturestatus>	
			<flapdetection>
				<global>1</global>
			</flapdetection>
			<notifications>
				<global>1</global>
			</notifications>
			<eventhandlers>
				<global>1</global>
			</eventhandlers>
			<activeservicechecks>
				<global>1</global>
			</activeservicechecks>
			<passiveservicechecks>		
				<global>1</global>
			</passiveservicechecks>
		</monitoringfeaturestatus>
	</tacinfo>
XML;

	
	$xml_object = simplexml_load_string(trim($xml_template)); 
	
	///////////////////////HOST STATES////////////////////////////
	
	//DOWN 
	$hostsdowntotal = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=4'; 
	if(array_key_exists($hostsdowntotal, $link_vals) ) 
		$xml_object->hoststatustotals->down->total = $link_vals[$hostsdowntotal]; 
	$hostsdownunhandled = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=4&hostprops=42';
	if(array_key_exists($hostsdownunhandled, $link_vals) ) 
		$xml_object->hoststatustotals->down->unhandled = $link_vals[$hostsdownunhandled];
	$hostsdownscheduled = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=4&hostprops=1';
	if(array_key_exists($hostsdownscheduled, $link_vals) ) 
		$xml_object->hoststatustotals->down->scheduled = $link_vals[$hostsdownscheduled];
	$hostsdownacknowledged = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=4&hostprops=4'; 
	if(array_key_exists($hostsdownacknowledged, $link_vals) ) 
		$xml_object->hoststatustotals->down->acknowledged = $link_vals[$hostsdownacknowledged];
	$hostsdowndisabled = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=4&hostprops=16'; 
	if(array_key_exists($hostsdowndisabled, $link_vals) ) 
		$xml_object->hoststatustotals->down->disabled = $link_vals[$hostsdowndisabled];
		
	//UNREACHABLE 	
	$hostsunreachabletotal = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=8';
	if(array_key_exists($hostsunreachabletotal, $link_vals) ) 
		$xml_object->hoststatustotals->unreachable->total = $link_vals[$hostsunreachabletotal];
	$hostsunreachableunhandled = 'status.cgi?host=all&style=hostdetail&hoststatustypes=8&hostprops=42';
	if(array_key_exists($hostsunreachableunhandled, $link_vals) ) 
		$xml_object->hoststatustotals->unreachable->unhandled = $link_vals[$hostsunreachableunhandled];
	$hostsunreachablescheduled = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=8&hostprops=1';
	if(array_key_exists($hostsunreachablescheduled, $link_vals) ) 
		$xml_object->hoststatustotals->unreachable->scheduled = $link_vals[$hostsunreachablescheduled];
	$hostsunreachableacknowledged = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=8&hostprops=4';
	if(array_key_exists($hostsunreachableacknowledged, $link_vals) ) 
		$xml_object->hoststatustotals->unreachable->acknowledged = $link_vals[$hostsunreachableacknowledged]; 
	$hostsunreachabledisabled = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=8&hostprops=16'; 
	if(array_key_exists($hostsunreachabledisabled, $link_vals) ) 
		$xml_object->hoststatustotals->unreachable->disabled = $link_vals[$hostsunreachabledisabled];
		
	//UP 	
	$hostsuptotal = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=2';
	if(array_key_exists($hostsuptotal, $link_vals) ) 
		$xml_object->hoststatustotals->up->total = $link_vals[$hostsuptotal];
	$hostsupdisabled = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=2&hostprops=16'; 
	if(array_key_exists($hostsuptotal, $link_vals) ) 
		$xml_object->hoststatustotals->up->total = $link_vals[$hostsuptotal];
	
	//PENDING 	
	$hostspendingtotal = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=1';
	if(array_key_exists($hostspendingtotal, $link_vals) ) 
		$xml_object->hoststatustotals->pending->total = $link_vals[$hostspendingtotal];
	$hostspendingdisabled = 'status.cgi?hostgroup=all&style=hostdetail&hoststatustypes=1&hostprops=16';
	if(array_key_exists($hostspendingdisabled, $link_vals) ) 
		$xml_object->hoststatustotals->pending->disabled = $link_vals[$hostspendingdisabled];
	
	//////////////////////////////////SERVICES////////////////////////////////////
	
	//WARNING 
	$serviceswarningtotal = 'status.cgi?host=all&style=detail&servicestatustypes=4';
	if(array_key_exists($serviceswarningtotal, $link_vals) ) 
		$xml_object->servicestatustotals->warning->total = $link_vals[$serviceswarningtotal];
	$serviceswarningunhandled = 'status.cgi?host=all&type=detail&servicestatustypes=4&hoststatustypes=3&serviceprops=42';
	if(array_key_exists($serviceswarningunhandled, $link_vals) ) 
		$xml_object->servicestatustotals->warning->unhandled = $link_vals[$serviceswarningunhandled];
	$serviceswarningscheduled = 'status.cgi?host=all&type=detail&servicestatustypes=4&serviceprops=1';
	if(array_key_exists($serviceswarningscheduled, $link_vals) ) 
		$xml_object->servicestatustotals->warning->scheduled = $link_vals[$serviceswarningscheduled];
	$serviceswarningacknowledged = 'status.cgi?host=all&type=detail&servicestatustypes=4&serviceprops=4';
	if(array_key_exists($serviceswarningacknowledged, $link_vals) ) 
		$xml_object->servicestatustotals->warning->acknowledged = $link_vals[$serviceswarningacknowledged];
	$serviceswarningdisabled = 'status.cgi?host=all&type=detail&servicestatustypes=4&serviceprops=16';
	if(array_key_exists($serviceswarningdisabled, $link_vals) ) 
		$xml_object->servicestatustotals->warning->disabled = $link_vals[$serviceswarningdisabled];
	$serviceswarninghostproblem = 'status.cgi?host=all&type=detail&servicestatustypes=4&hoststatustypes=12';
	if(array_key_exists($serviceswarninghostproblem, $link_vals) ) 
		$xml_object->servicestatustotals->warning->hostproblem = $link_vals[$serviceswarninghostproblem];
	
	//CRITICAL 
	$servicescriticaltotal = 'status.cgi?host=all&style=detail&servicestatustypes=16';
	if(array_key_exists($servicescriticaltotal, $link_vals) ) 
		$xml_object->servicestatustotals->critical->total = $link_vals[$servicescriticaltotal];
	$servicescriticalunhandled = 'status.cgi?host=all&type=detail&servicestatustypes=16&hoststatustypes=3&serviceprops=42';
	if(array_key_exists($servicescriticalunhandled, $link_vals) ) 
		$xml_object->servicestatustotals->critical->unhandled = $link_vals[$servicescriticalunhandled];
	$servicescriticalscheduled = 'status.cgi?host=all&type=detail&servicestatustypes=16&serviceprops=1';
	if(array_key_exists($servicescriticalscheduled, $link_vals) ) 
		$xml_object->servicestatustotals->critical->scheduled = $link_vals[$servicescriticalscheduled];
	$servicescriticalacknowledged = 'status.cgi?host=all&type=detail&servicestatustypes=16&serviceprops=4';
	if(array_key_exists($servicescriticalacknowledged, $link_vals) ) 
		$xml_object->servicestatustotals->critical->acknowledged = $link_vals[$servicescriticalacknowledged];
	$servicescriticaldisabled = 'status.cgi?host=all&type=detail&servicestatustypes=16&serviceprops=16';
	if(array_key_exists($servicescriticaldisabled, $link_vals) ) 
		$xml_object->servicestatustotals->critical->disabled = $link_vals[$servicescriticaldisabled];
	$servicescriticalhostproblem = 'status.cgi?host=all&type=detail&servicestatustypes=16&hoststatustypes=12';
	if(array_key_exists($servicescriticalhostproblem, $link_vals) ) 
		$xml_object->servicestatustotals->critical->hostproblem = $link_vals[$servicescriticalhostproblem];
	
	//UNKNOWN 
	$servicesunknowntotal = 'status.cgi?host=all&style=detail&servicestatustypes=8';
	if(array_key_exists($servicesunknowntotal, $link_vals) ) 
		$xml_object->servicestatustotals->unknown->total = $link_vals[$servicesunknowntotal];
	$servicesunknownunhandled = 'status.cgi?host=all&type=detail&servicestatustypes=8&hoststatustypes=3&serviceprops=42';
	if(array_key_exists($servicesunknownunhandled, $link_vals) ) 
		$xml_object->servicestatustotals->unknown->unhandled = $link_vals[$servicesunknownunhandled];
	$servicesunknownscheduled = 'status.cgi?host=all&type=detail&servicestatustypes=8&serviceprops=1';
	if(array_key_exists($servicesunknownscheduled, $link_vals) ) 
		$xml_object->servicestatustotals->unknown->scheduled = $link_vals[$servicesunknownscheduled];
	$servicesunknownacknowledged = 'status.cgi?host=all&type=detail&servicestatustypes=8&serviceprops=4';
	if(array_key_exists($servicesunknownacknowledged, $link_vals) ) 
		$xml_object->servicestatustotals->unknown->acknowledged = $link_vals[$servicesunknownacknowledged];
	$servicesunknowndisabled = 'status.cgi?host=all&type=detail&servicestatustypes=8&serviceprops=16';
	if(array_key_exists($servicesunknownacknowledged, $link_vals) ) 
		$xml_object->servicestatustotals->unknown->acknowledged = $link_vals[$servicesunknownacknowledged];
	$servicesunknownhostproblem = 'status.cgi?host=all&type=detail&servicestatustypes=8&hoststatustypes=12';
	if(array_key_exists($servicesunknownhostproblem, $link_vals) ) 
		$xml_object->servicestatustotals->unknown->hostproblem = $link_vals[$servicesunknownhostproblem];
	
	//OK
	$servicesoktotal = 'status.cgi?host=all&style=detail&servicestatustypes=2';
	if(array_key_exists($servicesoktotal, $link_vals) ) 
		$xml_object->servicestatustotals->ok->total = $link_vals[$servicesoktotal];
	$servicesokdisabled = 'status.cgi?host=all&type=detail&servicestatustypes=2&serviceprops=16';
	if(array_key_exists($servicesokdisabled, $link_vals) ) 
		$xml_object->servicestatustotals->ok->disabled = $link_vals[$servicesokdisabled];
		
	//PENDING 
	$servicespendingtotal = 'status.cgi?host=all&style=detail&servicestatustypes=1';
	if(array_key_exists($servicespendingtotal, $link_vals) ) 
		$xml_object->servicestatustotals->pending->total = $link_vals[$servicespendingtotal];
	$servicespendingdisabled = 'status.cgi?host=all&type=detail&servicestatustypes=1&serviceprops=16';
	if(array_key_exists($servicespendingtotal, $link_vals) ) 
		$xml_object->servicestatustotals->pending->total = $link_vals[$servicespendingtotal];
	
	//////////////////////////MONITORING FEATURES ////////////////////////////////////////////
	//check for 'disabled' url commands 
	$flapdet = 'cmd.cgi?cmd_typ=61';
	if(array_key_exists($flapdet, $link_vals) )
		$xml_object->monitoringfeaturestatus->flapdetection->global = 0;
	$notif = 'cmd.cgi?cmd_typ=12';
	if(array_key_exists($notif, $link_vals) )
		$xml_object->monitoringfeaturestatus->notifications->global = 0;
	$eventhand = 'cmd.cgi?cmd_typ=41';
	if(array_key_exists($eventhand, $link_vals) )
		$xml_object->monitoringfeaturestatus->eventhandlers->global = 0;
	$asc = 'cmd.cgi?type=35';
	if(array_key_exists($asc, $link_vals) )
		$xml_object->monitoringfeaturestatus->activeservicechecks->global = 0;
	$psc = 'cmd.cgi?type=37';
	if(array_key_exists($psc, $link_vals) )
		$xml_object->monitoringfeaturestatus->passiveservicechecks->global = 0;
	
	return $xml_object; 

}//end nagioscore_links_to_xml() 	


/**
*	wrapper function for v-shell data grab, mainly an abstraction for consistency
*/ 
function get_nagiosvshell_tac_overview_data($sid,$username,$password,&$output){
	
	$output="";
	$tac_data=array();
	get_server_data($sid,$username,$password,"tac",$tac_data);
	
	// check for errors...
	$error=false;
	
	// check for timeout
	if($tac_data["timeout"]==true){
		$output.='<span class="tacservertimeout"><img src="'.theme_image("critical_small.png").'"> A timeout occurred while attempting to contact the server.</span>';
		$error=true;
		}
		
	// check for auth error
	if($error==false){
		if($tac_data["http_code"]==401){
			$output.='<span class="tacserverautherror"><img src="'.theme_image("critical_small.png").'"> Failed to authenticate to server.  <a href="'.get_base_url().'config/" target="_top">Change credentials</a>.</span>';
			$error=true;
			}
		}
	return array($error,$tac_data['body']); 		
}		


//function


/**
*	@param int $sid server id from storage array 
*	checks against last_update_time for tac data of server ID 
*	should we use cached info from DB, or do a live fetch for the information
*	@return bool $use_db should we use DB information or not? 
*/
function tacdata_check_freshness($sid) {

	$threshold = 60;
	$threshold = is_null(get_option('active_threshold')) ? $threshold : get_option('active_threshold');
	$now = time(); 
	
	$query="SELECT last_update_time FROM fusion_tac_data WHERE server_sid='$sid'"; 
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true);
	
	foreach($rs as $r) $last = strtotime($r['last_update_time']);	
	
	echo "Data Age = ".($now-$last)."<br />"; 
	
	if( ($now-$last) > $threshold) {
		echo "Threshold exceeded, data is stale, grabbing fresh!<br />"; 
		return false; 
	}		
	else {
		echo "Data is fresh, use DB!<br />"; 
		return true;
	}	

}

/**
*	takes processed XML object and feeds it to DB, should work for XI, Core, or V-Shell XML.  
*
*/
function tacdata_save_to_db($xml,$sid,$error,$output='') {

	//array_dump($xml);
	
	 
	//echo "HOST: ".$xml->hoststatustotals->down->total."<br />"; 
	//echo "TEST {$xml->hoststatustotals->unreachable->scheduleddowntime}";

	$error_message = ($error==true) ? $output : ''; 
	$sinfo=get_server_info($sid);
	$error=intval($error);
	
	echo "Saving Tac Data to DB: ".$sinfo['name']."\n";
	
	//array_dump($sinfo);
	
	//check if we're updating or inserting
	$query="SELECT COUNT(*) FROM fusion_tac_data WHERE server_sid='$sid'"; 
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,SQLDEBUG);
	
	//array_dump($rs['fields']);
	
	//simple data count 
	foreach($rs as $r) $count = $r['count'];	
	
	if($error==1) {
		if($count==0)
			$query = "INSERT INTO fusion_tac_data (server_id,server_sid,server_name,last_update_time,error,error_message)
							VALUES (0,'$sid','{$sinfo['name']}',1,NOW(),1,'$error','$error_message')"; 
		else {
				$query = " UPDATE fusion_tac_data SET server_sid='$sid',server_name='{$sinfo['name']}',valid_credentials=1,
						   last_update_time=NOW(),server_is_reachable=1,error='$error',error_message='$error_message' WHERE server_sid='$sid'"; 
		}
							
		exec_sql_query(DB_NAGIOSFUSION,$query,SQLDEBUG);
		return; 					
	}

	//insert or update??	
	if($count==0)
		$query="INSERT INTO fusion_tac_data ( server_id,server_sid,server_name,valid_credentials,last_update_time, server_is_reachable,error,error_message,
	
		hosts_down_total,
		hosts_down_unhandled,
		hosts_down_scheduleddowntime,
		hosts_down_acknowledged,
		hosts_down_disabled,
		
		hosts_unreachable_total,
		hosts_unreachable_unhandled,
		hosts_unreachable_scheduleddowntime,
		hosts_unreachable_acknowledged,
		hosts_unreachable_disabled,
		
		hosts_up_total,
		hosts_up_disabled,
		
		hosts_pending_total,
		hosts_pending_disabled,	
		
		services_critical_total,
		services_critical_unhandled,
		services_critical_scheduleddowntime,
		services_critical_acknowledged,
		services_critical_disabled,
		services_critical_hostproblem,
		
		services_warning_total,
		services_warning_unhandled,
		services_warning_scheduleddowntime,
		services_warning_acknowledged,
		services_warning_disabled,
		services_warning_hostproblem,
	
		services_unknown_total,
		services_unknown_unhandled,
		services_unknown_scheduleddowntime,
		services_unknown_acknowledged,
		services_unknown_disabled,
		services_unknown_hostproblem,
	
		services_ok_total,
		services_ok_disabled,
		
		services_pending_total,
		services_pending_disabled )
	
		VALUES (0,'$sid','{$sinfo['name']}',1,NOW(),1,'$error','$error_message',
		
		'{$xml->hoststatustotals->down->total}',
		'{$xml->hoststatustotals->down->unhandled}',
		'{$xml->hoststatustotals->down->scheduleddowntime}',
		'{$xml->hoststatustotals->down->acknowledged}',
		'{$xml->hoststatustotals->down->disabled}',
		
		'{$xml->hoststatustotals->unreachable->total}',
		'{$xml->hoststatustotals->unreachable->unhandled}',
		'{$xml->hoststatustotals->unreachable->scheduleddowntime}',
		'{$xml->hoststatustotals->unreachable->acknowledged}',
		'{$xml->hoststatustotals->unreachable->disabled}',	
		
		'{$xml->hoststatustotals->up->total}',	
		'{$xml->hoststatustotals->up->disabled}',	
		
		'{$xml->hoststatustotals->pending->total}',	
		'{$xml->hoststatustotals->pending->disabled}',	
		
		'{$xml->servicestatustotals->critical->total}',
		'{$xml->servicestatustotals->critical->unhandled}',
		'{$xml->servicestatustotals->critical->scheduleddowntime}',
		'{$xml->servicestatustotals->critical->acknowledged}',
		'{$xml->servicestatustotals->critical->disabled}',
		'{$xml->servicestatustotals->critical->hostproblem}',	
		
		'{$xml->servicestatustotals->warning->total}',
		'{$xml->servicestatustotals->warning->unhandled}',
		'{$xml->servicestatustotals->warning->scheduleddowntime}',
		'{$xml->servicestatustotals->warning->acknowledged}',
		'{$xml->servicestatustotals->warning->disabled}',	
		'{$xml->servicestatustotals->warning->hostproblem}',
	
		'{$xml->servicestatustotals->unknown->total}',
		'{$xml->servicestatustotals->unknown->unhandled}',
		'{$xml->servicestatustotals->unknown->scheduleddowntime}',
		'{$xml->servicestatustotals->unknown->acknowledged}',
		'{$xml->servicestatustotals->unknown->disabled}',	
		'{$xml->servicestatustotals->unknown->hostproblem}',
	
		'{$xml->servicestatustotals->ok->total}',	
		'{$xml->servicestatustotals->ok->disabled}',	
		
		'{$xml->servicestatustotals->pending->total}',	
		'{$xml->servicestatustotals->pending->disabled}'
	
	)";
	
	else {
	
	$query = " UPDATE fusion_tac_data SET 
	server_sid='$sid',
	server_name='{$sinfo['name']}',
	valid_credentials=1,
   last_update_time=NOW(),
	server_is_reachable=1,
	error='$error',
	error_message='$error_message',
	
	hosts_down_total='{$xml->hoststatustotals->down->total}',
	hosts_down_unhandled='{$xml->hoststatustotals->down->unhandled}',
	hosts_down_scheduleddowntime='{$xml->hoststatustotals->down->scheduleddowntime}',
	hosts_down_acknowledged='{$xml->hoststatustotals->down->acknowledged}',
	hosts_down_disabled='{$xml->hoststatustotals->down->disabled}',
	
	hosts_unreachable_total='{$xml->hoststatustotals->unreachable->total}',
	hosts_unreachable_unhandled='{$xml->hoststatustotals->unreachable->unhandled}',
	hosts_unreachable_scheduleddowntime='{$xml->hoststatustotals->unreachable->scheduleddowntime}',
	hosts_unreachable_acknowledged='{$xml->hoststatustotals->unreachable->acknowledged}',
	hosts_unreachable_disabled='{$xml->hoststatustotals->unreachable->disabled}',	
	
	hosts_up_total='{$xml->hoststatustotals->up->total}',	
	hosts_up_disabled='{$xml->hoststatustotals->up->disabled}',	
	
	hosts_pending_total='{$xml->hoststatustotals->pending->total}',	
	hosts_pending_disabled='{$xml->hoststatustotals->pending->disabled}',	
	
	services_critical_total='{$xml->servicestatustotals->critical->total}',
	services_critical_unhandled='{$xml->servicestatustotals->critical->unhandled}',
	services_critical_scheduleddowntime='{$xml->servicestatustotals->critical->scheduleddowntime}',
	services_critical_acknowledged='{$xml->servicestatustotals->critical->acknowledged}',
	services_critical_disabled='{$xml->servicestatustotals->critical->disabled}',
	services_critical_hostproblem='{$xml->servicestatustotals->critical->hostproblem}',	
	
	services_warning_total='{$xml->servicestatustotals->warning->total}',
	services_warning_unhandled='{$xml->servicestatustotals->warning->unhandled}',
	services_warning_scheduleddowntime='{$xml->servicestatustotals->warning->scheduleddowntime}',
	services_warning_acknowledged='{$xml->servicestatustotals->warning->acknowledged}',
	services_warning_disabled='{$xml->servicestatustotals->warning->disabled}',	
	services_warning_hostproblem='{$xml->servicestatustotals->warning->hostproblem}',

	services_unknown_total='{$xml->servicestatustotals->unknown->total}',
	services_unknown_unhandled='{$xml->servicestatustotals->unknown->unhandled}',
	services_unknown_scheduleddowntime='{$xml->servicestatustotals->unknown->scheduleddowntime}',
	services_unknown_acknowledged='{$xml->servicestatustotals->unknown->acknowledged}',
	services_unknown_disabled='{$xml->servicestatustotals->unknown->disabled}',	
	services_unknown_hostproblem='{$xml->servicestatustotals->unknown->hostproblem}',

	services_ok_total='{$xml->servicestatustotals->ok->total}',	
	services_ok_disabled='{$xml->servicestatustotals->ok->disabled}',	
	
	services_pending_total='{$xml->servicestatustotals->pending->total}',	
	services_pending_disabled='{$xml->servicestatustotals->pending->disabled}',
	flap_detection={$xml->monitoringfeaturestatus->flapdetection->global},
	notifications={$xml->monitoringfeaturestatus->notifications->global},
	event_handlers={$xml->monitoringfeaturestatus->eventhandlers->global},
	active_checks={$xml->monitoringfeaturestatus->activeservicechecks->global},
	passive_checks={$xml->monitoringfeaturestatus->passiveservicechecks->global}
	
	WHERE server_sid='{$sid}'"; 	

	}

	//echo "<pre>".$query."</pre>"; 
	
	exec_sql_query(DB_NAGIOSFUSION,$query,SQLDEBUG);

}



/**
*	fetches tactical overview data from DB for a single server and returns XML object, false on failure 
*
*/ 
function get_tacdata_xml_from_db($sid,&$error,&$output) {

	//this could probably be cleaner 
	$query="SELECT * FROM fusion_tac_data WHERE server_sid='{$sid}' LIMIT 1"; 
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,SQLDEBUG);
	
	//if this failed retry once more 
	if(!$rs) {
		$output.="<span class='error'> Unable to fetch results from database </span>\n";
		$error=true;
		return;			
	}		
	
	//should only be a single row 
	foreach($rs as $row)
		$d = $row;
		
	//prevent race condition if we're currently refreshing the data	
	if(!isset($d['hosts_down_total'])) {
		sleep(3); //pause 
		$rs = exec_sql_query(DB_NAGIOSFUSION,$query,SQLDEBUG);		
		foreach($rs as $row)
			$d = $row;
	}	
	
	//make sure dataset is not empty 	
	if($d['error']==true) {
		$output.=$d['error_message'];
		$error=true;
		return;
	} 	
	
	if(!isset($d['hosts_down_total'])) {
		$output.="<span class='error'> Unable to retrieve data from the local cache. </span>\n";
		$error=true;		
	}
	//array_dump($d)		
	//create template for xml data needed for the fusion tac table, set default counts to 0 
	$xml_template = '<?xml version="1.0" encoding="utf-8"?>';
	$xml_template .="<tacinfo>	
		<!-- hosts -->
		<hoststatustotals>
			<down>
				<total>{$d['hosts_down_total']}</total>
				<unhandled>{$d['hosts_down_unhandled']}</unhandled>
				<scheduleddowntime>{$d['hosts_down_scheduleddowntime']}</scheduleddowntime>	
				<acknowledged>{$d['hosts_down_acknowledged']}</acknowledged>
				<disabled>{$d['hosts_down_disabled']}</disabled>
			</down>
			<unreachable>
				<total>{$d['hosts_unreachable_total']}</total>
				<unhandled>{$d['hosts_unreachable_unhandled']}</unhandled>
				<scheduledunreachabletime>{$d['hosts_unreachable_scheduleddowntime']}</scheduledunreachabletime>
				<acknowledged>{$d['hosts_unreachable_acknowledged']}</acknowledged>
				<disabled>{$d['hosts_unreachable_disabled']}</disabled>
			</unreachable>	
			<up>
				<total>{$d['hosts_up_total']}</total>
				<disabled>{$d['hosts_up_disabled']}</disabled>
			</up>
			<pending>
				<total>{$d['hosts_pending_total']}</total>
				<disabled>{$d['hosts_pending_disabled']}</disabled>
			</pending>
		</hoststatustotals>
		
		<!-- services -->
		<servicestatustotals>
			<warning>
				<total>{$d['services_warning_total']}</total>	
				<unhandled>{$d['services_warning_unhandled']}</unhandled>
				<scheduleddowntime>{$d['services_warning_scheduleddowntime']}</scheduleddowntime>
				<acknowledged>{$d['services_warning_acknowledged']}</acknowledged>
				<hostproblem>{$d['services_warning_hostproblem']}</hostproblem>
				<disabled>{$d['services_warning_disabled']}</disabled>
			</warning>
			<unknown>
				<total>{$d['services_unknown_total']}</total>
				<unhandled>{$d['services_unknown_unhandled']}</unhandled>
				<scheduleddowntime>{$d['services_unknown_scheduleddowntime']}</scheduleddowntime>	
				<acknowledged>{$d['services_unknown_acknowledged']}</acknowledged>
				<hostproblem>{$d['services_unknown_hostproblem']}</hostproblem>
				<disabled>{$d['services_unknown_disabled']}</disabled>
			</unknown>
			<critical>
				<total>{$d['services_critical_total']}</total>
				<unhandled>{$d['services_critical_unhandled']}</unhandled>
				<scheduleddowntime>{$d['services_critical_scheduleddowntime']}</scheduleddowntime>
				<acknowledged>{$d['services_critical_acknowledged']}</acknowledged>
				<hostproblem>{$d['services_critical_hostproblem']}</hostproblem>	
				<disabled>{$d['services_critical_disabled']}</disabled>
			</critical>
			<ok>
				<total>{$d['services_ok_total']}</total>
				<disabled>{$d['services_ok_disabled']}</disabled>
			</ok>
			<pending>
				<total>{$d['services_pending_total']}</total>
				<disabled>{$d['services_pending_disabled']}</disabled>
			</pending>
		</servicestatustotals>
		
		<!-- monitoring features -->
		<monitoringfeaturestatus>	
			<flapdetection>
				<global>{$d['flap_detection']}</global>
			</flapdetection>
			<notifications>
				<global>{$d['notifications']}</global>
			</notifications>
			<eventhandlers>
				<global>{$d['event_handlers']}</global>
			</eventhandlers>
			<activeservicechecks>
				<global>{$d['active_checks']}</global>
			</activeservicechecks>
			<passiveservicechecks>		
				<global>{$d['passive_checks']}</global>
			</passiveservicechecks>
		</monitoringfeaturestatus>
	</tacinfo>";

	
	return simplexml_load_string($xml_template); 

}


?>