<?php
// Fusion Core Ajax Helper Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: ajaxhelpers-misc.inc.php 18 2010-07-08 20:21:34Z egalstad $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
	

////////////////////////////////////////////////////////////////////////
// STATUSAJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////
	
function fusioncore_ajax_get_server_tactical_overview_html($args=null){
	global $lstr;
	
	$sid=grab_array_var($args,"id");
	
	// get saved credentials
	$sc=get_option("server_credentials");
	if($sc==null)
		$sc=array();
	else
		$sc=unserialize($sc);	
	
	$output='';
		
	// get credentials
	$username=grab_array_var($sc[$sid],"username");
	$password=grab_array_var($sc[$sid],"password");
	
	// we dont' have credentials
	if(!have_value($username) || !have_value($password)){
		$output.='<span class="tacservernocredentials"><img src="'.theme_image("critical_small.png").'"> 
			'.gettext('You have not defined authentication credentials for this server').'.  <a href="'.get_base_url().'config/" target="_top">'.gettext('Define them now').'</a>.</a>';
		}
	else {	
		$sinfo=get_server_info($sid);
				
		$error=false;
		$xml = get_tacdata_xml_from_db($sid,$error,$output);
		
		if(!$xml || $error==true) {
			return $output; 
		} 	
		switch($sinfo["type"]){
			case "nagiosxi":
				$output.=get_nagiosxi_tac_overview_html($sid,$error,$xml,$output,$username,$password);
			break;
				
			case "nagioscore":	
				$output.=get_nagioscore_tac_overview_html($sid,$error,$xml,$output); 
			break;
				
			case 'nagiosvshell':
				$output.=get_nagiosvshell_tac_overview_html($sid,$error,$xml);
			break; 
			}
		}
			
	$output.='<div class="ajax_date">'.gettext('Last Updated').': '.get_datetime_string(time()).'</div>';

	return $output;
	}


	
//////////////////////////////////// VSHELL ////////////////////////////////	
	
/**
*	build tactical dashboard from v-shell data 
*/ 	
function get_nagiosvshell_tac_overview_html($sid,$error,$xml,$output){
	
	// make sure we can parse XML
	if($error==false)
	{
		//get necessary vars 
		$sinfo=get_server_info($sid);
		$baseurl=$sinfo["url"];
	
		// load xml
		$xml=@simplexml_load_string($xml);
		if(!$xml){
			$output.='<span class="tacserverparseerror"><img src="'.theme_image("critical_small.png").'"> Unable to parse Nagios V-Shell server response.</span>';
			$error=true;
			}
		
		$output.='
		<table class="infotable tacsummary">
		<thead>
		<tr><th colspan="6">Status Summary&nbsp;</th><th nowrap>Notes <a href="'.get_base_url().'?fusionwindow=config/servers.php%3Fedit%3D1%26server_id[]%3D'.urlencode($sid).'" target="_parent">[ Edit ]</a></th></tr>
		</thead>
		<tbody>
		';
		
		$output.='<tr>';
		$output.='<th>Hosts</th>';
		$output.=get_nagiosvshell_tac_status_td($xml,'host',"up",$baseurl);
		$output.=get_nagiosvshell_tac_status_td($xml,'host',"down",$baseurl);
		$output.=get_nagiosvshell_tac_status_td($xml,'host',"unreachable",$baseurl);
		$output.=get_nagiosvshell_tac_status_td($xml,'host',"pending",$baseurl);
		
		$notes=str_replace("\n","<BR>",$sinfo["notes"]);
		
		$output.='<td>&nbsp;</td>';
		$output.='<td rowspan="3" valign="top">'.htmlentities($notes).'</td>';
		$output.='</tr>';
		
		$output.='<tr>';
		$output.='<th>Services</th>';
		$output.=get_nagiosvshell_tac_status_td($xml,'service',"ok",$baseurl);
		$output.=get_nagiosvshell_tac_status_td($xml,'service',"warning",$baseurl);
		$output.=get_nagiosvshell_tac_status_td($xml,'service',"unknown",$baseurl);
		$output.=get_nagiosvshell_tac_status_td($xml,'service',"critical",$baseurl);
		$output.=get_nagiosvshell_tac_status_td($xml,'service',"pending",$baseurl);
		$output.='</tr>';
			
		$output.='<tr>';
		$output.='<th>Settings</th>';
		$output.='<td colspan="6">';
		
		$output.=get_nagiosvshell_tac_setting_status($xml,"notifications","Notifications",1,$baseurl);
		$output.=get_nagiosvshell_tac_setting_status($xml,"activeservicechecks","Active Checks",2,$baseurl);
		$output.=get_nagiosvshell_tac_setting_status($xml,"passiveservicechecks","Passive Checks",0,$baseurl);
		$output.=get_nagiosvshell_tac_setting_status($xml,"eventhandlers","Event Handlers",0,$baseurl);
		
		$output.='</td>';
		$output.='</tr>';

		$output.='
		</tbody>
		</table>
		';		

	
	} //end if $error==false 
	
	//print_r($tac_data); 
	
	return $output;
}	

/**
*
*/
function get_nagiosvshell_tac_status_td($xml,$type,$shortstate,$baseurl){

	$gotourl=$baseurl."/?type={$type}s";
	$statename = ucfirst($shortstate); 
	$output="";
	$typestatustotals = $type.'statustotals';
	$total=intval($xml->$typestatustotals->$shortstate->total);
	
	$baseclass=$type."status".$shortstate;
	$extraclass="";
	if($total>0)
		$extraclass=" ".$baseclass."-present";
	
	$output.="<td valign='top' nowrap class='".$baseclass.$extraclass."'>";
	
	$filter = strtoupper($shortstate);
	//$output.="<span class='".$baseclass.$extraclass."'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";
	$output.="<span class='total'><a href='{$gotourl}&state_filter={$filter}' target='_blank'>".$total." ".$statename."</a></span>";

	if($shortstate!="ok" && $shortstate!="pending"){

		// unhandled problems
		$total=intval($xml->$typestatustotals->$shortstate->unhandled);
		$filter = 'UNHANDLED';
		if($total>0)
			$output.="<span class='substatus unhandled'><a href='{$gotourl}&state_filter={$filter}' target='_blank'>".$total." Unhandled</a></span>";
		
		// acknowledged
		$total=intval($xml->$typestatustotals->$shortstate->acknowledged);
		$filter = 'ACKNOWLEDGED'; 
		if($total>0)
			$output.="<span class='substatus acknowledged'><a href='{$gotourl}&state_filter={$filter}' target='_blank'>".$total." Acknowledged</a></span>";
		
		// scheduled downtime
		$total=intval($xml->$typestatustotals->$shortstate->scheduleddowntime);
		$filter = ''; //filter doesn't exist in V-Shell yet
		if($total>0)
			$output.="<span class='substatus scheduleddowntime'><a href='{$gotourl}&state_filter=".strtoupper($shortstate)."' target='_blank'>".$total." Scheduled</a></span>";
		
		// problem hosts
		$total=intval($xml->$typestatustotals->$shortstate->hostproblem);
		$filter = 'PROBLEMS'; 
		if($total>0)
			$output.="<span class='substatus hostproblem'><a href='{$gotourl}&state_filter={$filter}' target='_blank'>".$total." On Problem Hosts</a></span>";
		}
	
	// disabled
	$total=intval($xml->$typestatustotals->$shortstate->disabled);
	if($total>0)
		$output.="<span class='substatus disabled'><a href='{$gotourl}&state_filter=".strtoupper($shortstate)."' target='_blank'>".$total." Disabled</a></span>";
		
	$output.="</td>";	
	return $output;
	
} //end VSHELL xml function 


/**
*
*/
function get_nagiosvshell_tac_setting_status($xml,$shortname,$longname,$disabledstate,$baseurl){

	$output="";	
	$gotourl=$baseurl;
		
	$val=intval($xml->monitoringfeaturestatus->$shortname->global);
	
	$statetext="";
	$class="";
	if($val==0){
		$statetext="Disabled";
		switch($disabledstate){
			case 2:
				$class="settingcritical";
				break;
			case 1:
				$class="settingwarning";
				break;
			default:
				$class="settingok";
				break;
			}
		}
	else{
		$statetext="Enabled";
		$class="settingok";
		}

	$output.="<span class='settingstatus ".$class."'><a href='".$gotourl."' target='_blank'>".$longname.": ".$statetext."</a></span>";

	return $output;
}

//////////////////////////////////// CORE ////////////////////////////////

/**
*
*/	
function get_nagioscore_tac_overview_html($sid,$error,$xml,$output){
 		
	if(!$xml){
		$output.='<span class="tacserverparseerror"><img src="'.theme_image("critical_small.png").'"> '.gettext('Unable to parse Nagios Core server response').'.</span>';
		$error=true;
	}


	// ok to go
	if($error==false){
		//$output.=serialize($tac_data["body"]);
		//$output.="Nagios Core servers are not supported at this time.";

		//$output.="Good to go.";
		
		$sinfo=get_server_info($sid);
		$baseurl=$sinfo["url"];
		
		$output.='
		<table class="infotable tacsummary">
		<thead>
		<tr><th colspan="6">'.gettext('Status Summary').'&nbsp;</th><th nowrap>'.gettext('Notes').' <a href="'.get_base_url().'?fusionwindow=config/servers.php%3Fedit%3D1%26server_id[]%3D'.urlencode($sid).'" target="_parent">[ '.gettext('Edit').' ]</a></th></tr>
		</thead>
		<tbody>
		';
		
		$output.='<tr>';
		$output.='<th>'.gettext('Hosts').'</th>';
		$output.=get_nagioscore_tac_host_status_td($xml,"up",gettext("Up"),$baseurl);
		$output.=get_nagioscore_tac_host_status_td($xml,"down",gettext("Down"),$baseurl);
		$output.=get_nagioscore_tac_host_status_td($xml,"unreachable",gettext("Unreachable"),$baseurl);
		$output.=get_nagioscore_tac_host_status_td($xml,"pending",gettext("Pending"),$baseurl);
		
		$notes=str_replace("\n","<br />",$sinfo["notes"]);
		
		$output.='<td>&nbsp;</td>';
		$output.='<td rowspan="3" valign="top">'.htmlentities($notes).'</td>';
		$output.='</tr>';
		
		$output.='<tr>';
		$output.='<th>'.gettext('Services').'</th>';
		$output.=get_nagioscore_tac_service_status_td($xml,"ok",gettext("Ok"),$baseurl);
		$output.=get_nagioscore_tac_service_status_td($xml,"warning",gettext("Warning"),$baseurl);
		$output.=get_nagioscore_tac_service_status_td($xml,"unknown",gettext("Unknown"),$baseurl);
		$output.=get_nagioscore_tac_service_status_td($xml,"critical",gettext("Critical"),$baseurl);
		$output.=get_nagioscore_tac_service_status_td($xml,"pending",gettext("Pending"),$baseurl);
		$output.='</tr>';
			
		$output.='<tr>';
		$output.='<th>'.gettext('Settings').'</th>';
		$output.='<td colspan="6">';
		
		$output.=get_nagioscore_tac_setting_status($xml,"notifications",gettext("Notifications"),1,$baseurl);
		$output.=get_nagioscore_tac_setting_status($xml,"activeservicechecks",gettext("Active Checks"),2,$baseurl);
		$output.=get_nagioscore_tac_setting_status($xml,"passiveservicechecks",gettext("Passive Checks"),0,$baseurl);
		$output.=get_nagioscore_tac_setting_status($xml,"eventhandlers",gettext("Event Handlers"),0,$baseurl);
		
		$output.='</td>';
		$output.='</tr>';

		$output.='
		</tbody>
		</table>
		';		
		
		
		}

	return $output;
	}


/**
*
*/ 	
function get_nagioscore_tac_setting_status($xml,$shortname,$longname,$disabledstate,$baseurl){

	$output="";
	
	$gotourl=$baseurl."/?corewindow=cgi-bin/extinfo.cgi%3Ftype%3D0";
		
	$val=intval($xml->monitoringfeaturestatus->$shortname->global);
	
	$statetext="";
	$class="";
	if($val==0){
		$statetext="Disabled";
		switch($disabledstate){
			case 2:
				$class="settingcritical";
				break;
			case 1:
				$class="settingwarning";
				break;
			default:
				$class="settingok";
				break;
			}
		}
	else{
		$statetext=gettext("Enabled");
		$class="settingok";
		}

	$output.="<span class='settingstatus ".$class."'><a href='".$gotourl."' target='_blank'>".$longname.": ".$statetext."</a></span>";

	return $output;
	}


/**
*	
*
*/	
function get_nagioscore_tac_host_status_td($xml,$shortstate,$statename,$baseurl){

	$gotourl=$baseurl."/?corewindow=cgi-bin/status.cgi%3Fhostgroup%3Dall%26style%3Dhostdetail%26hoststatustypes%3D";
	
	switch($shortstate){
		case "up":
			$gotourl.="2";
			break;
		case "down":
			$gotourl.="4";
			break;
		case "unreachable":
			$gotourl.="8";
			break;
		case "pending":
			$gotourl.="1";
			break;
		}

	$output="";
	
	$total=intval($xml->hoststatustotals->$shortstate->total);
	
	$baseclass="hoststatus".$shortstate;
	$extraclass="";
	if($total>0)
		$extraclass=" ".$baseclass."-present";
	
	$output.="<td nowrap valign='top' class='".$baseclass.$extraclass."'>";

	//$output.="<span class='".$baseclass.$extraclass."'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";
	$output.="<span class='total'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";

	if($shortstate!="ok" && $shortstate!="pending"){

		// unhandled problems
		$total=intval($xml->hoststatustotals->$shortstate->unhandled);
		if($total>0)
			$output.="<span class='substatus unhandled'><a href='".$gotourl."%26hostprops%3D42' target='_blank'>".$total." ".gettext("Unhandled")."</a></span>";
		
		// acknowledged
		$total=intval($xml->hoststatustotals->$shortstate->acknowledged);
		if($total>0)
			$output.="<span class='substatus acknowledged'><a href='".$gotourl."%26hostprops%3D4' target='_blank'>".$total." ".gettext("Acknowledged")."</a></span>";
		
		// scheduled downtime
		$total=intval($xml->hoststatustotals->$shortstate->scheduleddowntime);
		if($total>0)
			$output.="<span class='substatus scheduleddowntime'><a href='".$gotourl."%26hostprops%3D1' target='_blank'>".$total." ".gettext("Scheduled")."</a></span>";
		}
	
	// disabled
	$total=intval($xml->hoststatustotals->$shortstate->disabled);
	if($total>0)
		$output.="<span class='substatus disabled'><a href='".$gotourl."%26hostprops%3D16' target='_blank'>".$total." ".gettext("Disabled")."</a></span>";
	

	$output.="</td>";
	
	return $output;
	}

/**
*	build nagioscore html td 
*/	
function get_nagioscore_tac_service_status_td($xml,$shortstate,$statename,$baseurl){

//	$gotourl=$baseurl;
	$gotourl=$baseurl."/?corewindow=cgi-bin/status.cgi%3Fhost%3Dall%26servicestatustypes%3D";
	
	switch($shortstate){
		case "ok":
			$gotourl.="2";
			break;
		case "warning":
			$gotourl.="4";
			break;
		case "unknown":
			$gotourl.="8";
			break;
		case "critical":
			$gotourl.="16";
			break;
		case "pending":
			$gotourl.="1";
			break;
		}

	$output="";
	
	$total=intval($xml->servicestatustotals->$shortstate->total);
	
	$baseclass="servicestatus".$shortstate;
	$extraclass="";
	if($total>0)
		$extraclass=" ".$baseclass."-present";
	
	$output.="<td valign='top' nowrap class='".$baseclass.$extraclass."'>";
	
	//$output.="<span class='".$baseclass.$extraclass."'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";
	$output.="<span class='total'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";

	if($shortstate!="ok" && $shortstate!="pending"){

		// unhandled problems
		$total=intval($xml->servicestatustotals->$shortstate->unhandled);
		if($total>0)
			$output.="<span class='substatus unhandled'><a href='".$gotourl."%26hoststatustypes%3D3%26serviceprops%3D42' target='_blank'>".$total." ".gettext("Unhandled")."</a></span>";
		
		// acknowledged
		$total=intval($xml->servicestatustotals->$shortstate->acknowledged);
		if($total>0)
			$output.="<span class='substatus acknowledged'><a href='".$gotourl."%26serviceprops%3D4' target='_blank'>".$total." ".gettext("Acknowledged")."</a></span>";
		
		// scheduled downtime
		$total=intval($xml->servicestatustotals->$shortstate->scheduleddowntime);
		if($total>0)
			$output.="<span class='substatus scheduleddowntime'><a href='".$gotourl."%26serviceprops%3D1' target='_blank'>".$total." ".gettext("Scheduled")."</a></span>";
		
		// problem hosts
		$total=intval($xml->servicestatustotals->$shortstate->hostproblem);
		if($total>0)
			$output.="<span class='substatus hostproblem'><a href='".$gotourl."%26hoststatustypes%3D12' target='_blank'>".$total." ".gettext("On Problem Hosts")."</a></span>";
		}
	
	// disabled
	$total=intval($xml->servicestatustotals->$shortstate->disabled);
	if($total>0)
		$output.="<span class='substatus disabled'><a href='".$gotourl."%26serviceprops%3D16' target='_blank'>".$total." ".gettext("Disabled")."</a></span>";
	
	
	$output.="</td>";
	
	return $output;
	}


	//////////////////////////////////// NAGIOSXI ////////////////////////////////

/**
*	build html tac dashboard based on XML and params 
*
*/ 	
function get_nagiosxi_tac_overview_html($sid,$error,$xml,$output,$username,$password){
		
	// okay to go
	if($error==false){
			
		$sinfo=get_server_info($sid);
		$baseurl=$sinfo["url"];
		
		$output.='
		<table class="infotable tacsummary">
		<thead>
		<tr><th colspan="6">'.gettext('Status Summary').'&nbsp;</th>
			<th nowrap>'.gettext('Notes').' <a href="'.get_base_url().'?fusionwindow=config/servers.php%3Fedit%3D1%26server_id[]%3D'.urlencode($sid).'" target="_parent">[ '.gettext('Edit').' ]</a></th></tr>
		</thead>
		<tbody>
		';
		
		$output.='<tr>';
		$output.='<th>'.gettext('Hosts').'</th>';
		$output.=get_nagiosxi_tac_host_status_td($xml,"up",gettext("Up"),$baseurl);
		$output.=get_nagiosxi_tac_host_status_td($xml,"down",gettext("Down"),$baseurl);
		$output.=get_nagiosxi_tac_host_status_td($xml,"unreachable",gettext("Unreachable"),$baseurl);
		$output.=get_nagiosxi_tac_host_status_td($xml,"pending",gettext("Pending"),$baseurl);
		
		$notes=str_replace("\n","<br />",$sinfo["notes"]);
		
		//$output.='<td>&nbsp;</td>';
		$output.='<td></td>'; //.embed_nagiosxi_magic_login($sid).'</td>';
		$output.='<td rowspan="2" valign="top">'.$notes.'</td>';
		$output.='</tr>';
		
		$output.='<tr>';
		$output.='<th>'.gettext('Services').'</th>';
		$output.=get_nagiosxi_tac_service_status_td($xml,"ok",gettext("Ok"),$baseurl);
		$output.=get_nagiosxi_tac_service_status_td($xml,"warning",gettext("Warning"),$baseurl);
		$output.=get_nagiosxi_tac_service_status_td($xml,"unknown",gettext("Unknown"),$baseurl);
		$output.=get_nagiosxi_tac_service_status_td($xml,"critical",gettext("Critical"),$baseurl);
		$output.=get_nagiosxi_tac_service_status_td($xml,"pending",gettext("Pending"),$baseurl);
		$output.='</tr>';
			
		$settings_data=array();
		get_server_data($sid,$username,$password,"programstatus",$settings_data);
		$settingsxml=@simplexml_load_string($settings_data["body"]);

		$output.='<tr>';
		$output.='<th>'.gettext('Settings').'</th>';
		$output.='<td colspan="6">';
		
		if($settingsxml){
		
			//print_r($settingsxml);
		
			$output.=get_nagiosxi_tac_setting_status($settingsxml,"is_currently_running",gettext("Monitoring Engine"),2,$baseurl);
			$output.=get_nagiosxi_tac_setting_status($settingsxml,"notifications_enabled",gettext("Notifications"),1,$baseurl);
			$output.=get_nagiosxi_tac_setting_status($settingsxml,"active_service_checks_enabled",gettext("Active Checks"),2,$baseurl);
			$output.=get_nagiosxi_tac_setting_status($settingsxml,"passive_service_checks_enabled",gettext("Passive Checks"),0,$baseurl);
			$output.=get_nagiosxi_tac_setting_status($settingsxml,"event_handlers_enabled",gettext("Event Handlers"),0,$baseurl);
			}
		
		$output.='</td>';
		$output.='</tr>';

		$output.='
		</tbody>
		</table>
		';		
		
		}	
	
	return $output;
	}

	
function get_nagiosxi_tac_setting_status($xml,$shortname,$longname,$disabledstate,$baseurl){

	$output="";
	
	$gotourl=$baseurl."/?xiwindow=admin/sysstat.php%3Fpageopt%3Dmonitoringengine";
	
	
	$val=intval($xml->programstatus->$shortname);
	
	$statetext="";
	$class="";
	if($val==0){
		$statetext="Disabled";
		switch($disabledstate){
			case 2:
				$class="settingcritical";
				break;
			case 1:
				$class="settingwarning";
				break;
			default:
				$class="settingok";
				break;
			}
		}
	else{
		$statetext=gettext("Enabled");
		$class="settingok";
		}

	$output.="<span class='settingstatus ".$class."'><a href='".$gotourl."' target='_blank'>".$longname.": ".$statetext."</a></span>";

	return $output;
	}


/**
*	builds html table data for tac
*/ 		
function get_nagiosxi_tac_host_status_td($xml,$shortstate,$statename,$baseurl){

	$gotourl=$baseurl."/?xiwindow=includes/components/xicore/status.php%3Fshow%3Dhosts%26servicestatustypes%3D0%26hoststatustypes%3D";
	
	switch($shortstate){
		case "up":
			$gotourl.="2";
			break;
		case "down":
			$gotourl.="4";
			break;
		case "unreachable":
			$gotourl.="8";
			break;
		case "pending":
			$gotourl.="1";
			break;
		}

	$output="";
	
	$total=intval($xml->hoststatustotals->$shortstate->total);
	
	$baseclass="hoststatus".$shortstate;
	$extraclass="";
	if($total>0)
		$extraclass=" ".$baseclass."-present";
	
	$output.="<td nowrap valign='top' class='".$baseclass.$extraclass."'>";

	//$output.="<span class='".$baseclass.$extraclass."'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";
	$output.="<span class='total'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";

	if($shortstate!="ok" && $shortstate!="pending"){

		// unhandled problems
		$total=intval($xml->hoststatustotals->$shortstate->unhandled);
		if($total>0)
			$output.="<span class='substatus unhandled'><a href='".$gotourl."%26hostattr%3D42' target='_blank'>".$total." ".gettext("Unhandled")."</a></span>";
		
		// acknowledged
		$total=intval($xml->hoststatustotals->$shortstate->acknowledged);
		if($total>0)
			$output.="<span class='substatus acknowledged'><a href='".$gotourl."%26hostattr%3D4' target='_blank'>".$total." ".gettext("Acknowledged")."</a></span>";
		
		// scheduled downtime
		$total=intval($xml->hoststatustotals->$shortstate->scheduleddowntime);
		if($total>0)
			$output.="<span class='substatus scheduleddowntime'><a href='".$gotourl."%26hostattr%3D1' target='_blank'>".$total." ".gettext("Scheduled")."</a></span>";
		}
	
	// disabled
	$total=intval($xml->hoststatustotals->$shortstate->disabled);
	if($total>0)
		$output.="<span class='substatus disabled'><a href='".$gotourl."%26hostattr%3D16' target='_blank'>".$total." ".gettext("Disabled")."</a></span>";
	

	$output.="</td>";
	
	return $output;
	}

	
/**
*	builds nagiosxi html table data for tac
*/ 	
function get_nagiosxi_tac_service_status_td($xml,$shortstate,$statename,$baseurl){

	$gotourl=$baseurl."/?xiwindow=includes/components/xicore/status.php%3Fshow%3Dservices%26servicestatustypes%3D";
	
	switch($shortstate){
		case "ok":
			$gotourl.="2";
			break;
		case "warning":
			$gotourl.="4";
			break;
		case "unknown":
			$gotourl.="8";
			break;
		case "critical":
			$gotourl.="16";
			break;
		case "pending":
			$gotourl.="1";
			break;
		}

	$output="";
	
	$total=intval($xml->servicestatustotals->$shortstate->total);
	
	$baseclass="servicestatus".$shortstate;
	$extraclass="";
	if($total>0)
		$extraclass=" ".$baseclass."-present";
	
	$output.="<td valign='top' nowrap class='".$baseclass.$extraclass."'>";
	
	//$output.="<span class='".$baseclass.$extraclass."'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";
	$output.="<span class='total'><a href='".$gotourl."' target='_blank'>".$total." ".$statename."</a></span>";

	if($shortstate!="ok" && $shortstate!="pending"){

		// unhandled problems
		$total=intval($xml->servicestatustotals->$shortstate->unhandled);
		if($total>0)
			$output.="<span class='substatus unhandled'><a href='".$gotourl."%26hoststatustypes%3D3%26serviceattr%3D42' target='_blank'>".$total." ".gettext("Unhandled")."</a></span>";
		
		// acknowledged
		$total=intval($xml->servicestatustotals->$shortstate->acknowledged);
		if($total>0)
			$output.="<span class='substatus acknowledged'><a href='".$gotourl."%26serviceattr%3D4' target='_blank'>".$total." ".gettext("Acknowledged")."</a></span>";
		
		// scheduled downtime
		$total=intval($xml->servicestatustotals->$shortstate->scheduleddowntime);
		if($total>0)
			$output.="<span class='substatus scheduleddowntime'><a href='".$gotourl."%26serviceattr%3D1' target='_blank'>".$total." ".gettext("Scheduled")."</a></span>";
		
		// problem hosts
		$total=intval($xml->servicestatustotals->$shortstate->hostproblem);
		if($total>0)
			$output.="<span class='substatus hostproblem'><a href='".$gotourl."%26hoststatustypes%3D12' target='_blank'>".$total." ".gettext("On Problem Hosts")."</a></span>";
		}
	
	// disabled
	$total=intval($xml->servicestatustotals->$shortstate->disabled);
	if($total>0)
		$output.="<span class='substatus disabled'><a href='".$gotourl."%26serviceattr%3D16' target='_blank'>".$total." ".gettext("Disabled")."</a></span>";
	
	
	$output.="</td>";
	
	return $output;
	}

	
?>