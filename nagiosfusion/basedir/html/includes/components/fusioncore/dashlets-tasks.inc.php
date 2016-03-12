<?php
// FusionCore Dashlet Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: dashlets-tasks.inc.php 75 2010-04-01 19:40:08Z egalstad $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
include_once(dirname(__FILE__).'/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// TASK DASHLETS
////////////////////////////////////////////////////////////////////////
	

// admin tasks
function fusioncore_dashlet_admin_tasks($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
	global $lstr;

	$output="";
	
	if($args==null)
		$args=array();
		
	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			$output='';
			break;
		case DASHLET_MODE_OUTBOARD:
		case DASHLET_MODE_INBOARD:
		
			$id="admin_task_".random_string(6);
			
			$output='
			<div class="admin_task_dashlet" id="'.$id.'">

			<div class="infotable_title">'.gettext('Administrative Tasks').'</div>
			'.get_throbber_html().'			
			
			</div><!--admin_task_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){

				get_'.$id.'_content();
					
				$("#'.$id.'").everyTime(60*1000, "timer-'.$id.'", function(i) {
					get_'.$id.'_content();
				});
				
				function get_'.$id.'_content(){
					$("#'.$id.'").each(function(){
						var optsarr = {
							"func": "get_admin_tasks_html",
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
			$imgurl=get_component_url_base()."fusioncore/images/dashlets/admin_tasks_preview.png";
			$output='
			<img src="'.$imgurl.'">
			';
			break;			
		}
		
	return $output;
	}


// getting started
function fusioncore_dashlet_getting_started($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
	global $lstr;

	$output="";
	
	if($args==null)
		$args=array();
		
	switch($mode){
		case DASHLET_MODE_GETCONFIGHTML:
			$output='';
			break;
		case DASHLET_MODE_OUTBOARD:
		case DASHLET_MODE_INBOARD:
		
			$id="getting_started_".random_string(6);
			
			$output='
			<div class="getting_started_dashlet" id="'.$id.'">

			<div class="infotable_title">'.gettext('Getting Started Guide').'</div>
			'.get_throbber_html().'			

			</div><!--getting_started_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){

				get_'.$id.'_content();
					
				$("#'.$id.'").everyTime(60*1000, "timer-'.$id.'", function(i) {
					get_'.$id.'_content();
				});
				
				function get_'.$id.'_content(){
					$("#'.$id.'").each(function(){
						var optsarr = {
							"func": "get_getting_started_html",
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
			$imgurl=get_component_url_base()."fusioncore/images/dashlets/getting_started_preview.png";
			$output='
			<img src="'.$imgurl.'">
			';
			break;			
		}
		
	return $output;
	}


?>