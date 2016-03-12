<?php

// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: topalertproducers.inc.php 75 2010-04-01 19:40:08Z mguthrie $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

topalertproducers_dashlet_init();


function topalertproducers_dashlet_init(){
	
	// respect the name!
	$name="topalertproducers";
	
	$args=array(

		// need a name
		DASHLET_NAME => $name,
		
		// informative information
		DASHLET_VERSION => "1.0",
		DASHLET_DATE => "04-19-2012",
		DASHLET_AUTHOR => "Mike Guthrie, Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => gettext("This dashlet displays the top 15 alert producers for the last 24 hours across all fused servers."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2012 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// the good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "topalertproducers_dashlet_func",
		
		DASHLET_TITLE => gettext("Top Alert Producers"),
		
		DASHLET_OUTBOARD_CLASS => "topalertproducers_outboardclass",
		DASHLET_INBOARD_CLASS => "topalertproducers_inboardclass",
		DASHLET_PREVIEW_CLASS => "topalertproducers_previewclass",
		DASHLET_WIDTH => "400px",
		DASHLET_HEIGHT => "300px",
		
		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
		);
	register_dashlet($name,$args);
	}



function topalertproducers_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
	$output="";	
	$limit = grab_request_var('limit',false); 
	$sort =  grab_request_var('sort','alert_count'); 
	$args = array('limit' => $limit, 'sort' =>$sort,'dashlet'=>false ); 
	//array_dump($args); 

	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			$output = "<div class='topalertproducers_dashlet_form'></div>"; 
			
			break;
		case DASHLET_MODE_OUTBOARD:
			$args['mode']=$mode;
			$output.=topalertproducers_dashlet_html($args);
			break; 
		case DASHLET_MODE_INBOARD:
			$args['limit'] = 15; //make this a dashlet config option 
			$args['mode']=$mode; //main Top Alert Producers page will override this
			$output.=topalertproducers_dashlet_html($args);
			break;
		case DASHLET_MODE_PREVIEW:
			$output="<img src='".get_base_url()."/includes/dashlets/topalertproducers/tap_preview.png' />";
			break;
		}
	//$output.="<p>MODE: $mode, ID: $id</p>";
		
	return $output;
}

function topalertproducers_dashlet_html($args=array()) {

	$id="topalertproducers_dashlet_".random_string(6);
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

<div class='topalertproducerscontainer'>
	<div class='infotable_title'>Top Alert Producers Last 24 Hours</div>
	<div class='fusioncore_topalertproducers' id='{$id}'>
	<img src='{$img}' />
	</div><!--fusioncore_server_tactical_overview_dashlet-->

	<script type='text/javascript'>
	$(document).ready(function(){

		get_topalertproducers_content();
			
		$('#{$id}').everyTime(60*1000, 'timer-fusioncore_topalertproducers', function(i) {
			get_topalertproducers_content();
		});
		
		function get_topalertproducers_content(){
						
			$('#{$id}').each(function(){
				var optsarr = {
					\"func\": \"get_topalertproducers_html\",
					\"args\": $jargs
					}
				var opts=array2json(optsarr);
				get_ajax_data_innerHTML(\"getfusioncoreajax\",opts,true,this);
				});
			}		
	});
	</script>



</div> <!-- end topalertproducerscontainer -->

";

	return $output; 

}


function fusioncore_ajax_get_topalertproducers_html($args=array()) {
	//array_dump($args);
	$taps = get_topalertproducers_from_db($args); 	
	$mode = grab_array_var($args,'mode',DASHLET_MODE_OUTBOARD); 	
		
	$output = ''; 
	$servers = get_servers(); 
		
	//begin html output 
	if($mode==DASHLET_MODE_OUTBOARD) { //main page 
		$output .= "<table class='standardtable'>\n<thead><tr>
				<th><a href='topalertproducers.php?sort=server_name'>".gettext("Server")."</a></th>
				<th><a href='topalertproducers.php?sort=host_name'>".gettext("Host")."</a></th>
				<th><a href='topalertproducers.php?sort=service_description'>".gettext("Service")."</a></th>
				<th><a href='topalertproducers.php?sort=rank'>".gettext("Rank")."</th>
				<th><a href='topalertproducers.php?sort=alert_count'>".gettext("Alert Count")."</a></th>
				</tr></thead><tbody>\n"; 
	}
	else //no filtering for dashlets 
		$output .= "<table class='standardtable'>\n<thead><tr>
			<th>".gettext("Server")."</th><th>".gettext("Host")."</th><th>".gettext("Service")."</th><th>".gettext("Rank")."</th><th>".gettext("Alert Count")."</th></tr></thead><tbody>\n"; 			

	$count = 0; 
	foreach($taps as $n) {
		$server = $servers[$n['server_sid']]; //which server? 
		$service = ($n['service_description'] == '') ? 'N/A' : $n['service_description'];  
		$object_type = ($service=='N/A' || $service=='') ? OBJECTTYPE_HOST : OBJECTTYPE_SERVICE; 
		$class = ($count++ % 2 == 1) ? 'even' : 'odd'; 		
		$host_url = get_object_details_url($server['type'],$server['url'],OBJECTTYPE_HOST,$n['host_name'],$service);
		$service_url = get_object_details_url($server['type'],$server['url'],OBJECTTYPE_SERVICE,$n['host_name'],$service);
		
		$output.="<tr class='$class'>
			<td><div class='td_120'><a href='{$server['url']}' title='{$n['server_name']}' target='_blank'>{$n['server_name']}</a></div></td>
			<td>$host_url</td>
			<td>$service_url</td>
			<td>{$n['rank']}</td>
		    <td>{$n['alert_count']}</td>
		</tr>"; 	
		
	}	 
	
	$output .="</tbody></table>"; 
	$output.="".gettext("Last Update").": ".date('r');

	return $output; 

}


?>