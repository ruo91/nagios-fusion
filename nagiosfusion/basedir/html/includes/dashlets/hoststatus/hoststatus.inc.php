<?php

// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: hoststatus.inc.php 75 2010-04-01 19:40:08Z mguthrie $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

hoststatus_dashlet_init();


function hoststatus_dashlet_init(){
	
	// respect the name!
	$name="hoststatus";
	
	$args=array(

		// need a name
		DASHLET_NAME => $name,
		
		// informative information
		DASHLET_VERSION => "1.0",
		DASHLET_DATE => "04-19-2012",
		DASHLET_AUTHOR => "Mike Guthrie, Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => "<strong>".gettext("Nagios XI Servers Only").".</strong> ".gettext("This dashlet displays a status bar for a single Nagios XI host."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2012 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// the good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "hoststatus_dashlet_func",
		
		DASHLET_TITLE => gettext("Host Status"),
		
		DASHLET_OUTBOARD_CLASS => "hoststatus_outboardclass",
		DASHLET_INBOARD_CLASS => "hoststatus_inboardclass",
		DASHLET_PREVIEW_CLASS => "hoststatus_previewclass",
		DASHLET_WIDTH => "300px",
		DASHLET_HEIGHT => "100px",
		
		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
		);
	register_dashlet($name,$args);
	}



function hoststatus_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
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
			<label for="server">Server: </label><br class="nobr" />
			<select name="sid" id="sid" onchange="gethosts()">
				'.$optionlist.'
			</select><br class="nobr" />
			<label for="width">'.gettext('Host').': </label><br class="nobr" />
			<select name="host" id="host">
				<option value=""></option>
			 <select/><br class="nobr" />				 	
			
			<br class="nobr" />
							
			'; 
			
			break;
		case DASHLET_MODE_OUTBOARD:
			$args['mode']=$mode;
			$output.=hoststatus_dashlet_html($args);
			break; 
		case DASHLET_MODE_INBOARD:
			$args['mode']=$mode; //main Top Alert Producers page will override this
			$output.=hoststatus_dashlet_html($args);
			break;
		case DASHLET_MODE_PREVIEW:
			$output="<img src='".get_base_url()."/includes/dashlets/hoststatus/hoststatus_preview.jpg' width='325' />";
			break;
		}
	//$output.="<p>MODE: $mode, ID: $id</p>";
		
	return $output;
}

function hoststatus_dashlet_html($args=array()) {

	$id="hoststatus_dashlet_".random_string(6);
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

	$output=<<<OUTPUT

<div class='hoststatuscontainer'>
	<div class="fusioncore_hoststatus" id="{$id}">
	<img src="{$img}" />
	</div>

	<script type="text/javascript">
	$(document).ready(function(){

		get_hoststatus_content();
			
		$("#{$id}").everyTime(60*1000, "timer-fusioncore_hoststatus", function(i) {
			get_hoststatus_content();
		});
		
		function get_hoststatus_content(){
						
			$("#{$id}").each(function(){
				var optsarr = {
					"func": "get_hoststatus_html",
					"args": $jargs
					}
				var opts=array2json(optsarr);
				get_ajax_data_innerHTML("getfusioncoreajax",opts,true,this);
				});
			}		
	});
	</script>



</div> <!-- end hoststatuscontainer -->

OUTPUT;

	return $output; 

}


function fusioncore_ajax_get_hoststatus_html($args=array()) {
	//array_dump($args);
	$sid = grab_array_var($args,'sid');
	$host = grab_array_var($args,'host'); 
	$sinfo=get_server_info($sid);
	$baseurl=$sinfo["url"];
	
	$url = get_nagiosxi_backend_url($sid); 
	$url.="&cmd=gethoststatus&brevity=3&name={$host}"; 

	$arr = get_server_data_from_url($url); 

	$xml = simplexml_load_string($arr['body']); 

	if(!$xml) return;  

	switch(intval($xml->hoststatus->current_state)) {
		case 2:
			$state = 'UNREACHABLE'; 
		break; 
		case 1:
			$state = 'DOWN';
		break; 
		default:
			$state='UP'; 
		break; 

	}

	$href=$baseurl.'/includes/components/xicore/status.php?show=hostdetail&host='.urlencode($host); 
	$output = "<table class='standardtable'>
				<tr><td><strong>".gettext("Host").": </strong></td>
					<td class='$state'><a href='{$href}' target='_blank' title='".gettext("Host Details")."' >{$xml->hoststatus->name}</a></td>
					<td>{$xml->hoststatus->status_update_time}</td>
					<td>{$xml->hoststatus->status_text}</td></tr></table>"; 
	
	//$output.="Last Update: ".date('r');

	return $output; 

}


?>