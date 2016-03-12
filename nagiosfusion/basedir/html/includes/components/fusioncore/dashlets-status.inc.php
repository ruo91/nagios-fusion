<?php
// Fusion Core Dashlet Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: dashlets-misc.inc.php 18 2010-07-08 20:21:34Z egalstad $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
include_once(dirname(__FILE__).'/../../utils-dashlets.inc.php');


////////////////////////////////////////////////////////////////////////
// TAC OVERVIEW DASHLETS
////////////////////////////////////////////////////////////////////////
	
function fusioncore_server_tactical_overview($mode=DASHLET_MODE_PREVIEW,$id="",$args=null){
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
		
			$sid=grab_array_var($args,"id");
		
			$title='';
			
			$sinfo=get_server_info($sid);
			$title.="<a href='".$sinfo["url"]."' target='_blank'>".htmlentities($sinfo["name"])."</a>";
			if($sinfo["location"]!="")
				$title.=" - ".htmlentities($sinfo["location"]);
			
			$id="fusioncore_server_tactical_overview_".random_string(6);
			
			$output='';
			
			$output.='<div class="infotable_title">'.$title.'</div>';

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

			$output.='
			<div class="fusioncore_server_tactical_overview_dashlet" id="'.$id.'">
			<img src="'.theme_image("throbber.gif").'">
			</div><!--fusioncore_server_tactical_overview_dashlet-->

	<script type="text/javascript">
	$(document).ready(function(){

				get_'.$id.'_content();
					
				$("#'.$id.'").everyTime(60*1000, "timer-'.$id.'", function(i) {
					get_'.$id.'_content();
				});
				
				function get_'.$id.'_content(){
					$("#'.$id.'").each(function(){
						var optsarr = {
							"func": "get_server_tactical_overview_html",
							"args": '.$jargs.'
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
			$imgurl=get_component_url_base()."fusioncore/images/dashlets/server_tactical_overview_preview.png";
			$output='
			<img src="'.$imgurl.'">
			';
			break;			
		}
		
	return $output;
	}


?>