<?php //fusedstatus.inc.php

//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: fusedstatus.inc.php 75 2010-04-01 19:40:08Z egalstad $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

// run the initialization function
fusedstatus_dashlet_init();

function fusedstatus_dashlet_init(){
	
	// respect the name!
	$name="fusedstatus";
	
	$args=array(

		// need a name
		DASHLET_NAME => $name,
		
		// informative information
		DASHLET_VERSION => "1.0",
		DASHLET_DATE => "04-11-2012",
		DASHLET_AUTHOR => "Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => gettext("Fused Status dashlets shows status totals across all servers in a single dashlet."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2012 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// the good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "fusedstatus_dashlet_func",
		//DASHLET_URL => get_dashlet_url_base($name)."/$name.php",
		
		DASHLET_TITLE => gettext("Fused Status Summary"),
		
		DASHLET_OUTBOARD_CLASS => "fusedstatus_outboardclass",
		DASHLET_INBOARD_CLASS => "fusedstatus_inboardclass",
		DASHLET_PREVIEW_CLASS => "fusedstatus_previewclass",
		
		DASHLET_CSS_FILE => "fusedstatus.css",
		DASHLET_JS_FILE => "fusedstatus.js",

//		DASHLET_WIDTH => "700px",
//		DASHLET_HEIGHT => "60px",
//		DASHLET_OPACITY => "1.0",
//		DASHLET_BACKGROUND => "",

		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
		);
	register_dashlet($name,$args);
	}
	
function fusedstatus_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
	$output="";

	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			break;
		case DASHLET_MODE_OUTBOARD:
		case DASHLET_MODE_INBOARD:
			$output.=fused_status_dashlet($args);
			break;
		case DASHLET_MODE_PREVIEW:
			$output="<img src='".get_base_url()."/includes/dashlets/fusedstatus/fs_preview.png' />";
			break;
		}
	//$output.="<p>MODE: $mode, ID: $id</p>";
		
	return $output;
}


function fused_status_dashlet($args) {

			
	$id="fused_status_dashlet_".random_string(6);
	
	$output='';
	
	$output.='<div class="infotable_title">'.gettext('Fused Status Summary').'</div>';

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
					"func": "get_fused_status_html",
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


function fusioncore_ajax_get_fused_status_html($args) {

	$query="SELECT SUM(hosts_up_total) as up, SUM(hosts_down_total) as down, 
				   SUM(hosts_unreachable_total) as unreachable, SUM(hosts_pending_total) as hosts_pending,
				   SUM(hosts_down_total + hosts_unreachable_total) as host_problems,
				   SUM(hosts_down_unhandled + hosts_unreachable_unhandled) as hosts_unhandled,
				   SUM(hosts_up_total + hosts_down_total + hosts_unreachable_total + hosts_pending_total) as hosts_all,
				   SUM(services_ok_total) as ok, SUM(services_warning_total) as warning,
				   SUM(services_critical_total) as critical, SUM(services_unknown_total) as unknown,
				   SUM(services_pending_total) as services_pending,
				   SUM(services_critical_total + services_unknown_total + services_warning_total) as service_problems,
				   SUM(services_critical_unhandled + services_warning_unhandled + services_unknown_unhandled) as services_unhandled,
				   SUM(services_ok_total + services_warning_total + services_critical_total + services_unknown_total + services_pending_total) as services_all 
				   FROM fusion_tac_data"; //can add user filter later 
				   
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true); 			   
	foreach($rs as $row)
		$r = $row; //single result set 
		
	//begin html output 	 
	$output="
	<div class='fusedstatusdashlet'>
	<table class='standardtable hoststatustable' style='text-align:center'>
	<tr>
		<th>".gettext("Hosts")."</th><th>".gettext("Up")."</th><th>".gettext("Down")."</th><th>".gettext("Unreachable")."</th>
		<th>".gettext("Pending")."</th><th>".gettext("Problems")."</th><th>".gettext("Unhandled")."</th><th colspan='2'>".gettext("All")."</th>
	</tr>
	<tr class='even'>
		<th> &nbsp; </th>
		<td class='hostup'><a href='javascript:fss_reveal(\"up\");'>{$r['up']}</a></td>
		<td class='hostdown'><a href='javascript:fss_reveal(\"down\");'>{$r['down']}</a></td>
		<td class='hostunreachable'><a href='javascript:fss_reveal(\"unreachable\");'>{$r['unreachable']}</a></td>
		<td class='hostpending'><a href='javascript:fss_reveal(\"hostspending\");'>{$r['hosts_pending']}</a></td>
		<td class='hostproblems'><a href='javascript:fss_reveal(\"hostproblems\");'>{$r['host_problems']}</a></td>
		<td class='hostunhandled'><a href='javascript:fss_reveal(\"hostsunhandled\");'>{$r['hosts_unhandled']}</a></td>
		<td colspan='2'><a href='javascript:fss_reveal(\"hostsall\");'>{$r['hosts_all']}</a></td>
	</tr>	
	<tr>	
		<th> ".gettext("Services")." </th><th>".gettext("Ok")."</th><th>".gettext("Warning")."</th>
		<th>".gettext("Critical")."</th><th>".gettext("Unknown")."</th><th>".gettext("Pending")."</th>
		<th>".gettext("Problems")."</th><th>".gettext("Unhandled")."</th><th>".gettext("All")."</th>
	</tr>
	<tr class='odd'>
		<th> &nbsp; </th>
		<td class='serviceok'><a href='javascript:fss_reveal(\"ok\");'>{$r['ok']}</a></td>
		<td class='servicewarning'><a href='javascript:fss_reveal(\"warning\");'>{$r['warning']}</a></td>
		<td class='servicecritical'><a href='javascript:fss_reveal(\"critical\");'>{$r['critical']}</a></td>
		<td class='serviceunknown'><a href='javascript:fss_reveal(\"unknown\");'>{$r['unknown']}</a></td>
		<td class='servicepending'><a href='javascript:fss_reveal(\"servicespending\");'>{$r['services_pending']}</a></td>
		<td class='serviceproblems'><a href='javascript:fss_reveal(\"serviceproblems\");'>{$r['service_problems']}</a></td>
		<td class='serviceunhandled'><a href='javascript:fss_reveal(\"servicesunhandled\");'>{$r['services_unhandled']}</a></td>
		<td><a href='javascript:fss_reveal(\"servicesall\");'>{$r['services_all']}</a></td>
	</tr>
	</table>
	".gettext("Last Update Time").": ".date('r')."
	</div>"; 	
	
	$output .=get_fused_status_overlays_html(); 	

	return $output;

}



function get_fused_status_overlays_html($args=array()) {

	//get the tac data 
	$query = "SELECT * FROM fusion_tac_data"; 
	$rs = exec_sql_query(DB_NAGIOSFUSION,$query,true);
	$count=0; 
	

	//begin html output 	 
	$output="<div class='hiddencontainer'>";
	
	//build each table individually
	$innerdivs = array('up' => '','down' => '', 'unreachable' => '', 'hostspending' => '', 'hostproblems' => '','hostsunhandled' => '','hostsall' =>'',
	'ok' => '', 'warning' => '', 'critical' => '', 'unknown' => '', 'servicespending' => '', 'serviceproblems' => '', 'servicesunhandled' => '', 'servicesall' => '');  	
						
	//table rows 
	foreach($rs as $row) {
		//skip problem servers 
		if($row['server_name']=='' || $row['error']==1) continue; 
		
		//server info 
		$sinfo=get_server_info($row['server_sid']);
		$baseurl=$sinfo["url"];
		$type=$sinfo['type'];
		$name = $row['server_name'];
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
		//hosts 
		$innerdivs['up'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_host_td('up',$row['hosts_up_total'],$baseurl,$type)."</tr>"; 
		$innerdivs['down'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_host_td('down',$row['hosts_down_total'],$baseurl,$type)."</tr>";
		$innerdivs['unreachable'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_host_td('unreachable',$row['hosts_unreachable_total'],$baseurl,$type)."</tr>";
		$innerdivs['hostspending'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_host_td('pending',$row['hosts_pending_total'],$baseurl,$type)."</tr>";
		$innerdivs['hostproblems'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_host_td('problems',$host_problems,$baseurl,$type)."</tr>";
		$innerdivs['hostsunhandled'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_host_td('unhandled',$host_unhandled,$baseurl,$type)."</tr>";
		$innerdivs['hostsall'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_host_td('all',$host_all,$baseurl,$type)."</tr>";
		//services
		$innerdivs['ok'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_service_td('ok',$row['services_ok_total'],$baseurl,$type)."</tr>";
		$innerdivs['warning'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_service_td('warning',$row['services_warning_total'],$baseurl,$type)."</tr>";
		$innerdivs['critical'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_service_td('critical',$row['services_critical_total'],$baseurl,$type)."</tr>";
		$innerdivs['unknown'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_service_td('unknown',$row['services_unknown_total'],$baseurl,$type)."</tr>";
		$innerdivs['servicespending'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_service_td('pending',$row['services_pending_total'],$baseurl,$type)."</tr>";
		$innerdivs['serviceproblems'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_service_td('problems',$service_problems,$baseurl,$type)."</tr>";
		$innerdivs['servicesunhandled'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_service_td('unhandled',$service_unhandled,$baseurl,$type)."</tr>";
		$innerdivs['servicesall'].="<tr class='$class'><td>".$row['server_name']."</td>".get_tacsummary_service_td('all',$service_all,$baseurl,$type)."</tr>";  
		
	} //end row loop 
	
	foreach($innerdivs as $key => $html) {
		
		$output.= "<div class='fss_hidden' id='fss_{$key}'>
					<table class='standardtable fss_table'>$html</table>
					<div class='fss_close'><a href='javascript:fss_close(\"{$key}\");' title='Close' >".gettext("Close")."</a></div>
				  </div> <!-- end hidden div -->"; 	
	}	
						
	$output .="</div> <!-- end hiddencontainer div -->"; 
	
	return $output;
}



?>