<?php

// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: servicestatus.inc.php 75 2010-04-01 19:40:08Z mguthrie $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

servicestatus_dashlet_init();


function servicestatus_dashlet_init(){
	
	// respect the name!
	$name="servicestatus";
	
	$args=array(

		// need a name
		DASHLET_NAME => $name,
		
		// informative information
		DASHLET_VERSION => "1.0",
		DASHLET_DATE => "04-19-2012",
		DASHLET_AUTHOR => "Mike Guthrie, Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => gettext("Nagios XI Servers Only. This dashlet displays service status information for a single Nagios XI service."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2012 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// the good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "servicestatus_dashlet_func",
		
		DASHLET_TITLE => gettext("Service Status"),
		
		DASHLET_OUTBOARD_CLASS => "servicestatus_outboardclass",
		DASHLET_INBOARD_CLASS => "servicestatus_inboardclass",
		DASHLET_PREVIEW_CLASS => "servicestatus_previewclass",
		DASHLET_WIDTH => "400px",
		DASHLET_HEIGHT => "100px",
		
		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
		);
	register_dashlet($name,$args);
	}



function servicestatus_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
	$output="";	

	//array_dump($args); 

	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			$optionlist = '<option value="">'.gettext('Select A Server').'</option>'; 
			foreach(get_servers() as $key => $val) {
				if($val['type']!='nagiosxi') continue; //only works for XI 
				$optionlist.="<option value='{$key}'>{$val['name']}</option>\n"; 
			}

			$output='
				
			<br />
			<label for="server">'.gettext('Server').': </label><br class="nobr" />
			<select name="sid" id="sid" onchange="gethosts()">
				'.$optionlist.'
			</select><br class="nobr" />
			<label for="width">'.gettext('Host').': </label><br class="nobr" />
			<select name="host" id="host" onchange="getservices()">
				<option value=""></option>
			 <select/><br class="nobr" />	
			 <label for="service">'.gettext('Service').': </label><br class="nobr" />
			<select name="service" id="service">
				<option value=""></option>
			 <select/><br class="nobr" />			 	
			
			<br class="nobr" />
							
			'; 
			
			break;
		case DASHLET_MODE_OUTBOARD:
			$args['mode']=$mode;
			$output.=servicestatus_dashlet_html($args);
			break; 
		case DASHLET_MODE_INBOARD:
			$args['mode']=$mode; //main Top Alert Producers page will override this
			$output.=servicestatus_dashlet_html($args);
			break;
		case DASHLET_MODE_PREVIEW:
			$output="<img src='".get_base_url()."/includes/dashlets/servicestatus/servicestatus_preview.jpg' width='325' />";
			break;
		}
	//$output.="<p>MODE: $mode, ID: $id</p>";
		
	return $output;
}

function servicestatus_dashlet_html($args=array()) {

	$id="servicestatus_dashlet_".random_string(6);
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
	$img = theme_image('throbber.gif');
	
	//echo $jargs; 

	$output="

<div class='servicestatuscontainer'>
	<div class='fusioncore_servicestatus' id='{$id}'>
	<img src='{$img}' />
	</div>

	<script type='text/javascript'>
	$(document).ready(function(){

		get_servicestatus_content();
			
		$('#{$id}').everyTime(60*1000, 'timer-fusioncore_servicestatus', function(i) {
			get_servicestatus_content();
		});
		
		function get_servicestatus_content(){
						
			$('#{$id}').each(function(){
				var optsarr = {
					\"func\": \"get_servicestatus_html\",
					\"args\": $jargs
					}
				var opts=array2json(optsarr);
				get_ajax_data_innerHTML(\"getfusioncoreajax\",opts,true,this);
				});
			}		
	});
	</script>



</div> <!-- end servicestatuscontainer -->

";

	return $output; 

}


function fusioncore_ajax_get_servicestatus_html($args=array()) {
	//array_dump($args);
	$sid = grab_array_var($args,'sid');
	$host = grab_array_var($args,'host'); 
	$service = grab_array_var($args,'service'); 
	$sinfo=get_server_info($sid);
	$baseurl=$sinfo["url"];
	
	$url = get_nagiosxi_backend_url($sid); 
	$url.="&cmd=getservicestatus&brevity=3&combinedhost=true&host_name=".urlencode($host)."&service_description=".urlencode($service);  
	//echo $url; 

	$arr = get_server_data_from_url($url); 

	$xml = simplexml_load_string($arr['body']); 

	if(!$xml) return;  
	
	//array_dump($xml); 

	switch(intval($xml->servicestatus->host_current_state)) {
		case 2:
			$hoststate = 'UNREACHABLE'; 
		break; 
		case 1:
			$hoststate = 'DOWN';
		break; 
		default:
			$hoststate='UP'; 
		break; 

	}

	switch(intval($xml->servicestatus->current_state)) {
		case 3:
			$servicestate = "UNKNOWN"; 
		break;
		case 2:
			$servicestate = 'CRITICAL'; 
		break; 
		case 1:
			$servicestate = 'WARNING';
		break; 
		default:
			$servicestate='OK'; 
		break; 

	}
	$h_href = $baseurl.'/includes/components/xicore/status.php?show=hostdetail&host='.urlencode($host);
	$s_href=$baseurl.'/includes/components/xicore/status.php?show=servicedetail&host='.urlencode($host).'&service='.urlencode($service);
	$output = "<table class='standardtable'>
				<tr><td><strong>".gettext("Host").": </strong></td>
					<td class='$hoststate'><a href='{$h_href}' target='_blank' title='".gettext("Host Details")."'>{$xml->servicestatus->host_name}</a></td>
					<td><strong>".gettext("Service").": </strong></td>
					<td class='{$servicestate}'><a href='{$s_href}' target='_blank' title='".gettext("Service Details")."'>{$xml->servicestatus->name}</a></td>
					<td>{$xml->servicestatus->status_update_time}</td>
					<td>{$xml->servicestatus->status_text}</td></tr></table>"; 
	
	//$output.="Last Update: ".date('r');

	return $output; 

}


?>