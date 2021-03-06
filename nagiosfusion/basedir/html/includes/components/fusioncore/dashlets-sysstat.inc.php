<?php
// Fusion Core Dashlet Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: dashlets-sysstat.inc.php 218 2012-12-17 20:42:02Z mguthrie $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
include_once(dirname(__FILE__).'/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// CORE SYSSTAT DASHLETS
////////////////////////////////////////////////////////////////////////
	

	

// used on system status page
function fusioncore_dashlet_server_stats($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){

	$output="";

	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			$output='';  
			break;
		case DASHLET_MODE_OUTBOARD:
		case DASHLET_MODE_INBOARD:
		
			//echo "DASHLET ARGS:<BR>\n";
			//print_r($args);
		
			$id="sysstat_serverstats_".random_string(6);
			
			$output='
	<div class="sysstat_serverstats" id="'.$id.'">

	<div class="infotable_title">Server Statistics</div>
	'.get_throbber_html().'			
			
	</div>
	<script type="text/javascript">
	$(document).ready(function(){

				get_'.$id.'_content();
					
				$("#'.$id.'").everyTime(5*1000, "timer-'.$id.'", function(i) {
					get_'.$id.'_content();
				});
				
				function get_'.$id.'_content(){
					$("#'.$id.'").each(function(){
						var optsarr = {
							"func": "get_server_stats_html",
							"args": ""
							}
						var opts=array2json(optsarr);
						get_ajax_data_innerHTML("getfusioncoreajax",opts,true,this);
						});
					}
		
	});
	</script>
			';
			break;
		case DASHLET_MODE_PREVIEW:
			$imgurl=get_component_url_base()."fusioncore/images/dashlets/server_stats_preview.png";
			$output='
			<img src="'.$imgurl.'">
			';
			break;			
		}
		
	return $output;
	}
	
// used on system status page
function fusioncore_dashlet_component_status($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){

	$output="";

	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			$output='';  
			break;
		case DASHLET_MODE_OUTBOARD:
		case DASHLET_MODE_INBOARD:
		
			//echo "DASHLET ARGS:<BR>\n";
			//print_r($args);
		
			$id="sysstat_componentstates_".random_string(6);
			
			$output='
	<div class="sysstat_componentstates" id="'.$id.'">
	
	<div class="infotable_title">'.gettext('Fusion System Component Status').'</div>
	'.get_throbber_html().'			
			
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
	
		get_'.$id.'_content();
		init_timer_'.$id.'();

	});
	

	function init_timer_'.$id.'(){
		$("#'.$id.'").everyTime('.get_dashlet_refresh_rate(7,"sysstat_componentstates").', "timer-'.$id.'", function(i) {
			get_'.$id.'_content();
			});
		}
		
	function get_'.$id.'_content(){
		$("#'.$id.'").each(function(){
			var optsarr = {
				"func": "get_component_states_html",
				"args": ""
				}
			var opts=array2json(optsarr);
			//get_ajax_data_innerHTML("getfusioncoreajax",opts,true,this);
			get_ajax_data_innerHTML_with_callback("getfusioncoreajax",opts,true,this,"init_component_dropdowns_'.$id.'");
			});
		}

	function init_component_dropdowns_'.$id.'(){
	
		$(".sysstate_componentstate_image img.actionimage").each(function(){

		// handle action clicks!
			$(this).click(function(){	
	
				var p=this.parentNode;
				
				// handle dropdown menu clicks
				$(p).children("ul").each(function(){
				
					$(this).click(function(){
						// hide the dropdown menu
						$(this).css("visibility","hidden");						
						// restart timer
						init_timer_'.$id.'();
						});
					});
				
				// hide or show this dropdown
				$(p).children("ul").each(function(){
				
					var theone=this;
				
					// hide all other hidden dropdowns (another one might be showing)
					$(".hiddendropdown").each(function(){
						if(this!=theone)
							$(this).css("visibility","hidden");
						});

					var cv=$(this).css("visibility");
					if(cv=="hidden"){
					
						// show the menu
						$(this).css("visibility","visible");
						
						// stop the timer
						$("#'.$id.'").stopTime("timer-'.$id.'");
						
						// menu should disappear on hover-out
						$(this).hover(
							function(){
								},
							function(){
							
								// hide the menu
								$(this).css("visibility","hidden");
								
								// restart timer
								init_timer_'.$id.'();
								}
							);
						}
					else{
					
						// hide the menu
						$(this).css("visibility","hidden");
						
						// restart timer
						init_timer_'.$id.'();
						}
					});
				});
			});
		}
	</script>
			';
			break;
		case DASHLET_MODE_PREVIEW:
			$imgurl=get_component_url_base()."fusioncore/images/dashlets/component_status_preview.png";
			$output='
			<img src="'.$imgurl.'">
			';
			break;			
		}
		
	return $output;
	}	


?>