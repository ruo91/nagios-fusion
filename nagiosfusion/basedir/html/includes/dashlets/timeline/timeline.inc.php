<?php //timeline.inc.php

// Copyright (c) 2008-2014 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: perfgraphs.inc.php 75 2010-04-01 19:40:08Z mguthrie $

include_once(dirname(__FILE__).'/../dashlethelper.inc.php');

timeline_dashlet_init();

function timeline_dashlet_init() {
	
	$name = "timeline";
	
	$args = array(
		DASHLET_NAME => $name,
		DASHLET_VERSION => "1.1.0",
		DASHLET_DATE => "05-13-2014",
		DASHLET_AUTHOR => "Nagios Enterprises, LLC",
		DASHLET_DESCRIPTION => gettext("This dashlet displays a scalable performance graph for a host or service."),
		DASHLET_COPYRIGHT => "Copyright (c) 2009-2014 Nagios Enterprises",
		DASHLET_HOMEPAGE => "http://www.nagios.com",
		
		// The good stuff - only one output method is used.  order of preference is 1) function, 2) url
		DASHLET_FUNCTION => "timeline_dashlet_func",
		DASHLET_TITLE => gettext("Performance Graph"),
		DASHLET_WIDTH => 600,
		DASHLET_HEIGHT => 400,
		DASHLET_REFRESHRATE => get_option('active_poller_interval'),
	);

	// Register dashlet to run
	register_dashlet($name, $args);
}



function timeline_dashlet_func($mode=DASHLET_MODE_PREVIEW, $id="", $args=null) {
	$output = ''; 

	switch($mode)
	{
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
			 		
			<label for="url">'.gettext('Service').': </label><br class="nobr" />
			<select name="service" id="service">
				</option value=""></option>
			</select>
			
			<br class="nobr" />
							
			'; 
			break;

		case DASHLET_MODE_OUTBOARD:
			$args['mode'] = $mode;
			$output .= timeline_dashlet($args);
			break;

		case DASHLET_MODE_INBOARD:
			$args['mode'] = $mode; //main Top Alert Producers page will override this
			$output .= timeline_dashlet($args);
			break;

		case DASHLET_MODE_PREVIEW:
			$output = "<img src='".get_base_url()."/includes/dashlets/timeline/timeline_preview.jpg' />";
			break;
	}
		
	return $output;
}

function timeline_dashlet($args) {

	$height = grab_array_var($args,'iframe_height',300);
	$width = grab_array_var($args, 'width', 600);
	$id = "timeline_dashlet_".random_string(6);
	$id2 = $id."_2"; 
	$host = urlencode(grab_array_var($args,'host','localhost')); 
	$service = urlencode(grab_array_var($args,'service','')); 
	$sid = grab_array_var($args,'sid'); 
	$img = theme_image('throbber.gif');

	$output="

	<div class='perfgraphscontainer'>
		<div class='fusioncore_perfgraphs' id='{$id}'></div>
			<div class='graphcontainer' id='{$id2}'><img src='{$img}' /></div>
		

		<script type='text/javascript'>
		$(document).ready(function(){
	
			get_".$id."_content({$height}, {$width});
				
			$('#{$id}').everyTime(60*1000, 'timer-fusioncore_perfgraphs', function(i) {
				get_".$id."_content();
			});

			$('#".$id."').closest('.ui-resizable').on('resizestop', function(e, ui) {
		        var height = ui.size.height - 17;
		        var width = ui.size.width;
		        get_".$id."_content(height, width);
		    });
			
			function get_".$id."_content(height, width) {

				var url = '/nagiosfusion/includes/components/xidata/xidata.php?&cmd=getgraph&div={$id2}&host={$host}&service={$service}&sid={$sid}&type=timeline';
				url += '&width=' + width + '&height=' + height;

				$('#{$id}').load(url);

				// Stop clicking in graph from moving dashlet
				$('#".$id."').closest('.ui-draggable').draggable('option', 'cancel', '#".$id2."');
			}		
		});
	</script>
		
	</div>
";

	return $output; 

}

?>