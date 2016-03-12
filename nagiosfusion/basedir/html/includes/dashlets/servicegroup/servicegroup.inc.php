<?php

// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: servicegroup.inc.php 75 2010-04-01 19:40:08Z mguthrie $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

servicegroup_dashlet_init();


function servicegroup_dashlet_init(){
	
	// respect the name!
	$name="servicegroup";
	
	$args=array(

		// need a name
		DASHLET_NAME => $name,
		
		// informative information
		DASHLET_VERSION => "1.0",
		DASHLET_DATE => "04-19-2012",
		DASHLET_AUTHOR => "Mike Guthrie, Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => "<strong>".gettext("Nagios XI Servers Only")."</strong> ".gettext("This dashlet displays service summary information for a selected servicegroup."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2012 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// the good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "servicegroup_dashlet_func",
		
		DASHLET_TITLE => gettext("Service Group Summary"),
		
		DASHLET_OUTBOARD_CLASS => "servicegroup_outboardclass",
		DASHLET_INBOARD_CLASS => "servicegroup_inboardclass",
		DASHLET_PREVIEW_CLASS => "servicegroup_previewclass",
		DASHLET_WIDTH => "300px",
		DASHLET_HEIGHT => "100px",
		
		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
		);
	register_dashlet($name,$args);
	}



function servicegroup_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
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
			<select name="sid" id="sid" onchange="getservicegroups()">
				'.$optionlist.'
			</select><br class="nobr" />
			<label for="width">servicegroup: </label><br class="nobr" />
			<select name="servicegroup" id="servicegroup">
				<option value=""></option>
			 <select/><br class="nobr" />				 	
			
			<br class="nobr" />
							
			'; 
			
			break;
		case DASHLET_MODE_OUTBOARD:
			$args['mode']=$mode;
			$output.=servicegroup_dashlet_html($args);
			break; 
		case DASHLET_MODE_INBOARD:
			$args['mode']=$mode; //main Top Alert Producers page will override this
			$output.=servicegroup_dashlet_html($args);
			break;
		case DASHLET_MODE_PREVIEW:
			$output="<img src='".get_base_url()."/includes/dashlets/servicegroup/servicegroup_preview.jpg' height='75' />";
			break;
		}
	//$output.="<p>MODE: $mode, ID: $id</p>";
		
	return $output;
}

function servicegroup_dashlet_html($args=array()) {

	$id="servicegroup_dashlet_".random_string(6);
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

<div class='servicegroupcontainer'>
	<div class='fusioncore_servicegroup' id='{$id}'>
	<img src='{$img}' />
	</div>

	<script type='text/javascript'>
	$(document).ready(function(){

		get_servicegroup_content();
			
		$('#{$id}').everyTime(60*1000, 'timer-fusioncore_servicegroup', function(i) {
			get_servicegroup_content();
		});
		
		function get_servicegroup_content(){
						
			$('#{$id}').each(function(){
				var optsarr = {
					\"func\": \"get_servicegroup_html\",
					\"args\": $jargs
					}
				var opts=array2json(optsarr);
				get_ajax_data_innerHTML(\"getfusioncoreajax\",opts,true,this);
				});
			}		
	});
	</script>



</div> <!-- end servicegroupcontainer -->

";

	return $output; 

}


function fusioncore_ajax_get_servicegroup_html($args=array()) {
	//array_dump($args);
	$sid = grab_array_var($args,'sid');
	$servicegroup = grab_array_var($args,'servicegroup'); 
	$sinfo=get_server_info($sid);
	$baseurl=$sinfo["url"];
	
	$url = get_nagiosxi_backend_url($sid); 

	//servicegroup member fetch for IDs 
	$url1 = $url."&cmd=getservicegroupmembers&servicegroup_name=".urlencode($servicegroup);
	$arr = get_server_data_from_url($url1); 
	$xml = simplexml_load_string($arr['body']);
	unset($arr);  

	if(!$xml) 
		return "Unable to retrieve data from server";  
		
	//array_dump($xml);  
		
	//build id string
	$ids = array(); 
	foreach($xml->servicegroup->members as $member) {
		foreach($member->service as $service) 
			$ids[] = $service['id']; 
	}	
		
	//array_dump($ids); 	
	$idstring=implode(',',$ids); 
	$in = urlencode('in:'.$idstring); 	




	$url.="&cmd=getservicestatus&combinedhost=1&brevity=3&service_id={$in}"; 

	$arr = get_server_data_from_url($url); 

	$xml = simplexml_load_string($arr['body']); 

	if(!$xml) 
		return "Unable to retrieve data from server";  
		
	//array_dump($xml); 

	$unknown=0;
	$critical=0;
	$warning=0;
	$ok=0;

	foreach($xml->servicestatus as $service) {
		switch(intval($service->current_state)) {
			case 3:
				$unknown++; 
			break; 
			case 2:
				$critical++; 
			break; 
			case 1:
				$warning++;
			break; 
			default:
				$ok++; 
			break; 
	
		}
	}

	//urls
	$href=$baseurl.'/includes/components/xicore/status.php?show=servicestatus&servicegroup='.urlencode($servicegroup); 
	$ok_href=$href."&servicestatustypes=2";
	$warning_href = $href."&servicestatustypes=4";
	$unknown_href = $href."&servicestatustypes=8";
	$critical_href= $href.="&servicestatustypes=16";

	$output = "<table class='standardtable'>
				<tr><th>Servicegroup</th><th>Services</th></tr>
				<tr><td><a href='{$href}' target='_blank' title='Servicegroup Details' > {$servicegroup}</a></td>
					<td><div class='lineitem OK'><a href='{$ok_href}' title='View Ok Services' target='_blank'> {$ok} Ok</a></div>
						<div class='lineitem WARNING'><a href='{$warning_href}' title='View Warning Services' target='_blank'> {$warning} Warning</a></div>
						<div class='lineitem CRITICAL'><a href='{$critical_href}' title='View Critical Services' target='_blank'> {$critical} Critical</a></div>
						<div class='lineitem UNKNOWN'><a href='{$unknown_href}' title='View Unknown Services' target='_blank'> {$unknown} Unknown</a></div>
					</td>
				</tr>
				</table>"; 
	
	$output.="Last Update: ".date('H:i:s');

	return $output; 

}


?>