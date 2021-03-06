<?php
// Fusion Core Dashlet Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: dashlets-misc.inc.php 218 2012-12-17 20:42:02Z mguthrie $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
include_once(dirname(__FILE__).'/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// MISC DASHLETS
////////////////////////////////////////////////////////////////////////
	

// xi news
/*
function xicore_dashlet_xi_news_feed($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
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
		
			$id="xi_news_feed_".random_string(6);
			
			$output='';
			
			$output.='<div class="infotable_title">Latest Nagios XI News</div>';

			$output.='
			<div class="xi_news_feed_dashlet" id="'.$id.'">
			<img src="'.theme_image("throbber.gif").'"> Loading latest news...
			</div><!--xi_news_feed_dashlet-->

			<script type="text/javascript">
			$(document).ready(function(){
				
				$("#'.$id.'").each(function() {
				var optsarr = {
					"func": "get_xi_news_feed_html",
					"args": ""
					}
				var opts=array2json(optsarr);
				//var newcontent=get_ajax_data("getxicoreajax",opts);
				//this.innerHTML=newcontent;
				get_ajax_data_innerHTML("getxicoreajax",opts,true,this);
				});
				
			});
			</script>
			';
			
			break;
			
		case DASHLET_MODE_PREVIEW:
			$imgurl=get_component_url_base()."xicore/images/dashlets/xi_news_feed_preview.png";
			$output='
			<img src="'.$imgurl.'">
			';
			break;			
		}
		
	return $output;
	}
*/

// available updates
function fusioncore_dashlet_available_updates($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
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
		
			$id="fusion_available_updates_".random_string(6);
			
			$output='';
			
			$output.='<div class="infotable_title">'.gettext('Available Updates').'</div>';

			$output.='
			<div class="fusion_available_updates_dashlet" id="'.$id.'">
			<img src="'.theme_image("throbber.gif").'"> '.gettext('Checking for updates...').'
			</div><!--fusion_available_updates_dashlet-->

	<script type="text/javascript">
	$(document).ready(function(){

				get_'.$id.'_content();
					
				$("#'.$id.'").everyTime(24*60*60*1000, "timer-'.$id.'", function(i) {
					get_'.$id.'_content();
				});
				
				function get_'.$id.'_content(){
					$("#'.$id.'").each(function(){
						var optsarr = {
							"func": "get_available_updates_html",
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
			$imgurl=get_component_url_base()."fusioncore/images/dashlets/available_updates_preview.png";
			$output='
			<img src="'.$imgurl.'">
			';
			break;			
		}
		
	return $output;
	}


?>