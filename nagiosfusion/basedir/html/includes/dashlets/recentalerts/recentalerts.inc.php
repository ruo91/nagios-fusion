<?php //recentalerts.inc.php

//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: recentalerts.inc.php 75 2010-04-01 19:40:08Z mguthrie $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

recentalerts_dashlet_init();


function recentalerts_dashlet_init(){
	
	// respect the name!
	$name="recentalerts";
	
	$args=array(

		// need a name
		DASHLET_NAME => $name,
		
		// informative information
		DASHLET_VERSION => "1.0",
		DASHLET_DATE => "04-19-2012",
		DASHLET_AUTHOR => "Mike Guthrie, Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => gettext("This dashlet displays the last 15 alerts across all fused servers."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2012 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// the good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "recentalerts_dashlet_func",
		
		DASHLET_TITLE => gettext("Recent Alerts"),
		
		DASHLET_OUTBOARD_CLASS => "recentalerts_outboardclass",
		DASHLET_INBOARD_CLASS => "recentalerts_inboardclass",
		DASHLET_PREVIEW_CLASS => "recentalerts_previewclass",
		DASHLET_WIDTH => "800px",
		DASHLET_HEIGHT => "300px",
		
		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
		);
	register_dashlet($name,$args);
	}



function recentalerts_dashlet_func($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
	$output="";	
	$limit = grab_request_var('limit',false); 
	$sort =  grab_request_var('sort','start_time'); 
	$args = array('limit' => $limit, 'sort' =>$sort,'dashlet'=>false ); 
	//array_dump($args); 

	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			$output = "<div class='recentalerts_dashlet_form'></div>"; 
			
			break;
		case DASHLET_MODE_OUTBOARD:
			$args['mode']=$mode;
			$output.=recentalerts_dashlet_html($args);
			break; 
		case DASHLET_MODE_INBOARD:
			$args['limit'] = 15; //make this a dashlet config option 
			$args['mode']=$mode; //main recent alerts page will override this
			$output.=recentalerts_dashlet_html($args);
			break;
		case DASHLET_MODE_PREVIEW:
			$output="<img src='".get_base_url()."/includes/dashlets/recentalerts/ra_preview.png' />";
			break;
		}
	//$output.="<p>MODE: $mode, ID: $id</p>";
		
	return $output;
}

function recentalerts_dashlet_html($args=array()) {

	$id="recentalerts_dashlet_".random_string(6);
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

<div class='recentalertscontainer'>
	<div class='infotable_title'>".gettext('Recent Notifications - Last 2 hours')."</div>
	<div class='fusioncore_recentalerts' id='{$id}'>
	<img src='{$img}' />
	</div><!--fusioncore_server_tactical_overview_dashlet-->

	<script type='text/javascript'>
	$(document).ready(function(){

		get_recentalerts_content();
			
		$('#{$id}').everyTime(60*1000, 'timer-fusioncore_recentalerts', function(i) {
			get_recentalerts_content();
		});
		
		function get_recentalerts_content(){
						
			$('#{$id}').each(function(){
				var optsarr = {
					\"func\": \"get_recent_alerts_html\",
					\"args\": $jargs
					}
				var opts=array2json(optsarr);
				get_ajax_data_innerHTML('getfusioncoreajax',opts,true,this);
				});
			}		
	});
	</script>



</div> <!-- end recentalertscontainer -->

";

	return $output; 

}

?>