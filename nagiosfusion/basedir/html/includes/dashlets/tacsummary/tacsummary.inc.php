<?php

//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: tacsummary.inc.php 75 2010-04-01 19:40:08Z egalstad $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

// run the initialization function
tacsummary_dashlet_init();

function tacsummary_dashlet_init(){
	
	// respect the name!
	$name="tacsummary";
	
	$args=array(

		// need a name
		DASHLET_NAME => $name,
		
		// informative information
		DASHLET_VERSION => "1.0",
		DASHLET_DATE => "04-11-2012",
		DASHLET_AUTHOR => "Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => gettext("Tactical summary dashlet that displays all tactical overview information in a compact view."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2012 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// the good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "tacsummary_dashlet_func",
		//DASHLET_URL => get_dashlet_url_base($name)."/$name.php",
		
		DASHLET_TITLE => gettext("Tactical Summary"),
		
		DASHLET_OUTBOARD_CLASS => "tacsummary_outboardclass",
		DASHLET_INBOARD_CLASS => "tacsummary_inboardclass",
		DASHLET_PREVIEW_CLASS => "tacsummary_previewclass",
		
//		DASHLET_CSS_FILE => "tacsummary.css",
//		DASHLET_JS_FILE => "tacsummary.js",

//		DASHLET_WIDTH => "700px",
//		DASHLET_HEIGHT => "60px",
//		DASHLET_OPACITY => "1.0",
//		DASHLET_BACKGROUND => "",

		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
		);
	register_dashlet($name,$args);
	
	//login page
    if(function_exists('register_callback'))
		register_callback(CALLBACK_DELETE_SERVER,'tacsummary_delete_server');
		
	}
	
function tacsummary_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
	$output="";

	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			break;
		case DASHLET_MODE_OUTBOARD:
		case DASHLET_MODE_INBOARD:
			$output.=tac_summary_dashlet($args);
			break;

		case DASHLET_MODE_PREVIEW:
			$output="<img src='".get_base_url()."/includes/dashlets/tacsummary/ts_preview.png' width='300' />";
			break;
		}
	//$output.="<p>MODE: $mode, ID: $id</p>";
		
	return $output;
}


function tac_summary_dashlet($args) {

			
	$id="fusioncore_server_tactical_overview_".random_string(6);
	
	$output='';
	
	$output.='<div class="infotable_title">'.gettext('Tactical Summary').'</div>';

	// ajax updater args
	$ajaxargs=$args;
	// build args for javascript
	$n=0;
	$jargs="{";
	foreach($ajaxargs as $var => $val){
		if($n>0)
			$jargs.=", ";
		$jargs.="\"$var\" : \"$val\"";
		$n++;
		}
	$jargs.="}";

	$output.='
	<div class="fusioncore_server_tactical_overview_dashlet" id="'.$id.'">
	<img src="'.theme_image("throbber.gif").'">
	</div><!--fusioncore_server_tactical_overview_dashlet-->

	<script type="text/javascript">
	$(document).ready(function(){

		get_'.$id.'_content();
			
		$("#'.$id.'").everyTime(60*1000, "timer-'.$id.'", function(i) {
			get_'.$id.'_content();
		});
		
		function get_'.$id.'_content(){
			$("#'.$id.'").each(function(){
				var optsarr = {
					"func": "get_tacsummary_html",
					"args": '.$jargs.'
					}
				var opts=array2json(optsarr);
				get_ajax_data_innerHTML("getfusioncoreajax",opts,true,this);
				});
			}		
	});
	</script>';

	return $output; 

}


function fusioncore_ajax_get_tacsummary_html($args) {

	//get the tac data 
	$query = "SELECT * FROM fusion_tac_data"; 
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true);
	$count=0; 
	
	//total counters
	$up=0;
	$down=0;
	$unreachable=0;
	$hpending=0;
	$hproblems=0;
	$hunhandled=0;
	$hall=0;
	$ok=0;
	$warning=0;
	$critical=0;
	$unknown=0;
	$spending=0;
	$sproblems=0;
	$sunhandled=0;
	$sall=0;

	//begin html output 	 
	$output="
	<table class='standardtable hoststatustable servicestatustable hostsummarytable servicesummarytable' style='text-align:center'>
	<thead>
	<tr>
		<th> &nbsp; </th>
		<th colspan='7'>".gettext("Hosts")."</th>
		<th colspan='8'>".gettext("Services")."</th>
	</tr>	
	<tr>
		<th>".gettext("Server")."</th>	
		<th>".gettext("Up")."</th>
		<th>".gettext("Down")."</th>
		<th>".gettext("Unreachable")."</th>
		<th>".gettext("Pending")."</th>
		<th>".gettext("Problems")."</th>
		<th>".gettext("Unhandled")."</th>
		<th>".gettext("All")."</th>
		<th>".gettext("Ok")."</th>
		<th>".gettext("Warning")."</th>
		<th>".gettext("Critical")."</th>
		<th>".gettext("Unknown")."</th>
		<th>".gettext("Pending")."</th>
		<th>".gettext("Problems")."</th>
		<th>".gettext("Unhandled")."</th>
		<th>".gettext("All")."</th>
	</tr></thead>
	<tbody>"; 	
		
				
	//table rows 
	foreach($rs as $row) {
		//skip problem servers 
		if($row['server_name']=='' || $row['error']==1) continue; 
		
		//server info 
		$sinfo=get_server_info($row['server_sid']);
		$baseurl=$sinfo["url"];
		$type=$sinfo['type'];
		//r($sinfo); 
		
		//calculate variables for table rows 
		$class=( ($count++ % 2) == 1) ? 'odd' : 'even'; 
		$host_problems = $row['hosts_down_total'] + $row['hosts_unreachable_total'];
		$host_unhandled = $row['hosts_down_unhandled'] + $row['hosts_unreachable_unhandled']; 
		$host_all = ( $row['hosts_up_total'] + $row['hosts_down_total'] + $row['hosts_unreachable_total'] + $row['hosts_pending_total'] ); 
		$service_problems = $row['services_warning_total'] + $row['services_critical_total'] + $row['services_unknown_total'];
		$service_unhandled = $row['services_warning_unhandled'] + $row['services_critical_unhandled'] + $row['services_unknown_unhandled']; 
		$service_all = ( $row['services_ok_total'] + $row['services_warning_total'] + $row['services_critical_total'] + $row['services_unknown_total'] + $row['services_pending_total'] ); 
		
		//begin html table rows 
		$output.="
		<tr class='$class'>
			<td>".$row['server_name']."</td>".
			get_tacsummary_host_td('up',$row['hosts_up_total'],$baseurl,$type).
			get_tacsummary_host_td('down',$row['hosts_down_total'],$baseurl,$type).
			get_tacsummary_host_td('unreachable',$row['hosts_unreachable_total'],$baseurl,$type).
			get_tacsummary_host_td('pending',$row['hosts_pending_total'],$baseurl,$type).
			get_tacsummary_host_td('problems',$host_problems,$baseurl,$type).
			get_tacsummary_host_td('unhandled',$host_unhandled,$baseurl,$type).
			get_tacsummary_host_td('all',$host_all,$baseurl,$type).

			get_tacsummary_service_td('ok',$row['services_ok_total'],$baseurl,$type).
			get_tacsummary_service_td('warning',$row['services_warning_total'],$baseurl,$type).
			get_tacsummary_service_td('critical',$row['services_critical_total'],$baseurl,$type).
			get_tacsummary_service_td('unknown',$row['services_unknown_total'],$baseurl,$type).
			get_tacsummary_service_td('pending',$row['services_pending_total'],$baseurl,$type).
			get_tacsummary_service_td('problems',$service_problems,$baseurl,$type).
			get_tacsummary_service_td('unhandled',$service_unhandled,$baseurl,$type).
			get_tacsummary_service_td('all',$service_all,$baseurl,$type).
		"</tr>";  
		
		//update total counters
		$up += $row['hosts_up_total'];
		$down += $row['hosts_down_total'];
		$unreachable += $row['hosts_unreachable_total'];
		$hpending += $row['hosts_pending_total'];
		$hproblems += $host_problems;
		$hunhandled += $host_unhandled;
		$hall += $host_all;
		$ok += $row['services_ok_total'];
		$warning += $row['services_warning_total'];;
		$critical += $row['services_critical_total'];;
		$unknown += $row['services_unknown_total'];;
		$spending += $row['services_pending_total'];
		$sproblems += $service_problems;
		$sunhandled += $service_unhandled;
		$sall += $service_all;
	} //end row loop 
	
	//close table with totals 
	$output .= "
	<tr style='font-weight:bold;'>
		<td>".gettext("Total")."</td>
		<td>$up</td>
		<td>$down</td>
		<td>$unreachable</td>
		<td>$hpending</td>
		<td>$hproblems</td>
		<td>$hunhandled</td>
		<td>$hall</td>
		<td>$ok</td>
		<td>$warning</td>
		<td>$critical</td>
		<td>$unknown</td>
		<td>$spending</td>
		<td>$sproblems</td>
		<td>$sunhandled</td>
		<td>$sall</td>
	</tr>"; 	
					
		
	$output .="</tbody></table>".gettext("Last Update Time").": ".date('r').""; 
	
	return $output;
}


function get_tacsummary_service_td($state,$value,$baseurl,$servertype){

//	$gotourl=$baseurl;
	//echo $baseurl; 
	if($servertype=='nagiosxi')
		$gotourl=$baseurl."?xiwindow=includes/components/xicore/status.php?";
	else 	 
		$gotourl=$baseurl."/?corewindow=cgi-bin/status.cgi?";
	
	switch($state){
		case "ok":
			$gotourl.=urlencode("&host=all&servicestatustypes=2&hoststatustypes=15");
			break;
		case "warning":
			$gotourl.=urlencode("&host=all&servicestatustypes=4&hoststatustypes=15");
			break;
		case "unknown":
			$gotourl.=urlencode("&host=all&servicestatustypes=8&hoststatustypes=15");
			break;
		case "critical":
			$gotourl.=urlencode("&host=all&servicestatustypes=16&hoststatustypes=15");
			break;
		case "pending":
			$gotourl.=urlencode("&host=all&servicestatustypes=1&hoststatustypes=1");
			break;
		case 'unhandled':
			$gotourl.=urlencode("&host=all&type=detail&hoststatustypes=3&serviceprops=42&servicestatustypes=28"); 
			break;
		case 'problems':
			$gotourl.=urlencode("&host=all&servicestatustypes=28&hoststatustypes=15");
			break;		
		case 'all':
			$gotourl.=urlencode("&host=all&hoststatustypes=15");
			
			break;			
	}
		
	$baseclass="service".$state;
	$extraclass="";
	if($value>0)
		$extraclass=" ".$baseclass."-present";
	
	$output ="<td class='".$baseclass.$extraclass."'>";
	$output.="<a href='".$gotourl."' target='_blank'>".$value."</a>";	
	$output.="</td>";
	
	return $output;
}


function get_tacsummary_host_td($state,$value,$baseurl,$servertype){

	if($servertype=='nagiosxi')
		$gotourl=$baseurl."?xiwindow=includes/components/xicore/status.php?";
	else 	 
		$gotourl=$baseurl."/?corewindow=cgi-bin/status.cgi?";
	
	switch($state){
		case "up":
			$gotourl.=urlencode("&show=hosts&hoststatustypes=2");
			break;
		case "down":
			$gotourl.=urlencode("&show=hosts&hoststatustypes=4");
			break;
		case "unreachable":
			$gotourl.=urlencode("&show=hosts&hoststatustypes=8");
			break;
		case "pending":
			$gotourl.=urlencode("&hostgroup=all&style=hostdetail&hoststatustypes=1"); 
			break;
		case 'unhandled':
			$gotourl.=urlencode("&show=hosts&hoststatustypes=12&hostattr=10"); 
			break;
		case 'problems':
			$gotourl.=urlencode("&show=hosts&hoststatustypes=12");
			break;		
		case 'all':
			$gotourl.=urlencode("&show=hosts");
			break;			
	}
		
	$baseclass="host".$state;
	$extraclass="";
	if($value>0)
		$extraclass=" ".$baseclass."-present";
	
	$output ="<td class='".$baseclass.$extraclass."'>";
	$output.="<a href='".$gotourl."' target='_blank'>".$value."</a>";	
	$output.="</td>";
	
	return $output;
}

function tacsummary_delete_server($cbtype='',$args=array()) {
	
	$server_id = grab_array_var($args,'server_id',false);
	if($server_id) {	
		$query = "DELETE FROM fusion_tac_data WHERE server_sid='{$server_id}'"; 
		exec_sql_query(DB_NAGIOSFUSION,$query,true);
	}
		

}


?>