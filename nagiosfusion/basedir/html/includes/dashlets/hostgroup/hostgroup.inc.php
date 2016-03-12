<?php

// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: hostgroup.inc.php 75 2010-04-01 19:40:08Z mguthrie $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

hostgroup_dashlet_init();


function hostgroup_dashlet_init(){
	
	// respect the name!
	$name="hostgroup";
	
	$args=array(

		// need a name
		DASHLET_NAME => $name,
		
		// informative information
		DASHLET_VERSION => "1.0.1",
		DASHLET_DATE => "06-05-2014",
		DASHLET_AUTHOR => "Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => gettext("Nagios XI Servers Only.  This dashlet displays host summary information for a selected hostgroup."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2014 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// the good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "hostgroup_dashlet_func",
		
		DASHLET_TITLE => gettext("Host Group Summary"),
		
		DASHLET_OUTBOARD_CLASS => "hostgroup_outboardclass",
		DASHLET_INBOARD_CLASS => "hostgroup_inboardclass",
		DASHLET_PREVIEW_CLASS => "hostgroup_previewclass",
		DASHLET_WIDTH => "300px",
		DASHLET_HEIGHT => "100px",
		
		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
		);
	register_dashlet($name,$args);
	}



function hostgroup_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
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
			<select name="sid" id="sid" onchange="gethostgroups()">
				'.$optionlist.'
			</select><br class="nobr" />
			<label for="width">'.gettext('Hostgroup').': </label><br class="nobr" />
			<select name="hostgroup" id="hostgroup">
				<option value=""></option>
			 <select/><br class="nobr" />				 	
			
			<br class="nobr" />
							
			'; 
			
			break;
		case DASHLET_MODE_OUTBOARD:
			$args['mode']=$mode;
			$output.=hostgroup_dashlet_html($args);
			break; 
		case DASHLET_MODE_INBOARD:
			$args['mode']=$mode; //main Top Alert Producers page will override this
			$output.=hostgroup_dashlet_html($args);
			break;
		case DASHLET_MODE_PREVIEW:
			$output="<img src='".get_base_url()."/includes/dashlets/hostgroup/hostgroup_preview.jpg' height='75' />";
			break;
		}
	//$output.="<p>MODE: $mode, ID: $id</p>";
		
	return $output;
}

function hostgroup_dashlet_html($args=array()) {

	$id="hostgroup_dashlet_".random_string(6);
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

<div class='hostgroupcontainer'>
	<div class="fusioncore_hostgroup" id="{$id}">
	<img src="{$img}" />
	</div>

	<script type="text/javascript">
	$(document).ready(function(){

		get_hostgroup_content();
			
		$("#{$id}").everyTime(60*1000, "timer-fusioncore_hostgroup", function(i) {
			get_hostgroup_content();
		});
		
		function get_hostgroup_content(){
						
			$("#{$id}").each(function(){
				var optsarr = {
					"func": "get_hostgroup_html",
					"args": $jargs
					}
				var opts=array2json(optsarr);
				get_ajax_data_innerHTML("getfusioncoreajax",opts,true,this);
				});
			}		
	});
	</script>



</div> <!-- end hostgroupcontainer -->

OUTPUT;

	return $output; 

}


function fusioncore_ajax_get_hostgroup_html($args=array()) {
	//array_dump($args);
	$sid = grab_array_var($args,'sid');
	$hostgroup = grab_array_var($args,'hostgroup'); 
	$sinfo=get_server_info($sid);
	$baseurl=$sinfo["url"];
	
	$url = get_nagiosxi_backend_url($sid); 
	
	//hostgroup member fetch for IDs 
	$url1 = $url."&cmd=gethostgroupmembers&hostgroup_name=".urlencode($hostgroup);
	$arr = get_server_data_from_url($url1); 
	$xml = simplexml_load_string($arr['body']);
	unset($arr);  

	if(!$xml) 
		return gettext("Unable to retrieve data from server");  
		
	//array_dump($xml);  
		
	//build id string
	$ids = array(); 
	foreach($xml->hostgroup->members as $member) {
		foreach($member->host as $host) 
			$ids[] = $host['id']; 
	}	
		
	//array_dump($ids); 	
	$idstring=implode(',',$ids); 
	$in = urlencode('in:'.$idstring); 			
			
	//hostgroup status fetch 	
	$url2 = $url."&cmd=gethoststatus&brevity=3&host_id={$in}"; 
	//echo $url2; 

	$arr = get_server_data_from_url($url2); 

	$xml = simplexml_load_string($arr['body']); 
	unset($arr);

	if(!$xml) 
		return gettext("Unable to retrieve data from server");  
		
	//array_dump($xml); 

	$unreachable=0;
	$down=0;
	$up=0;

	foreach($xml->hoststatus as $host) {
		switch(intval($host->current_state)) {
			case 2:
				$unreachable++; 
			break; 
			case 1:
				$down++;
			break; 
			default:
				$up++; 
			break; 
	
		}
	}

	$href=$baseurl.'/includes/components/xicore/status.php?show=hosts&hostgroup='.urlencode($hostgroup); 

	//up url
	$up_href=$href."&hoststatustypes=2";
	//down url
	$down_href = $href."&hoststatustypes=4";
	//unreachable url 
	$unreachable_href = $href."&hoststatustypes=8";

	$output = "<table class='standardtable'>
				<tr><th>".gettext("Hostgroup")."</th><th>".gettext("Hosts")."</th></tr>
				<tr><td><a href='{$href}' target='_blank' title='Hostgroup Details' >{$hostgroup}</a></td>
					<td><div class='lineitem UP'><a href='{$up_href}' title='".gettext("View Up Hosts")."' target='_blank'>{$up} ".gettext("Up")."</a></div>
						<div class='lineitem DOWN'><a href='{$down_href}' title='".gettext("View Down Hosts")."' target='_blank'>$down ".gettext("Down")."</a></div>
						<div class='lineitem UNREACHABLE'><a href='{$unreachable_href}' title='".gettext("View Unreachable Hosts")."' target='_blank'>$unreachable ".gettext("Unreachable")."</a></div>
					</td>
				</tr>
				</table>"; 
	
	$output.=gettext("Last Update").": ".date('H:i:s');

	return $output; 

}


?>