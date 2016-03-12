<?php
// Fusion Core Ajax Helper Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: ajaxhelpers-sysstat.inc.php 415 2015-03-02 18:51:58Z jomann $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
	

////////////////////////////////////////////////////////////////////////
// SYSSTAT AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////



function fusioncore_ajax_get_stat_bar_html($rawval,$label,$displayval,$mult=20,$maxval=200,$level2=10,$level3=50){

	$val=(floatval($rawval) * $mult);
	if($val>$maxval)
		$val=$maxval;
	if($val<=1)
		$val=1;		
	else if($val<=0)
		$val=0;
	
	$spanval="0,$maxval,$val";
	
	if($val>$level3)
		$spanval="<div style='height: 10px; width: ".$val."px; background-color:  ".COMMONCOLOR_RED.";'>&nbsp;</div>";
	else if($val>$level2)
		$spanval="<div style='height: 10px; width: ".$val."px; background-color:  ".COMMONCOLOR_YELLOW.";'>&nbsp;</div>";
	else
		$spanval="<div style='height: 10px; width: ".$val."px; background-color:  ".COMMONCOLOR_GREEN.";'>&nbsp;</div>";
	
	$barclass="";
	
	$output='<tr><td><span class="sysstat_stat_subtitle">'.$label.'</span></td><td>'.$displayval.'</td><td><span class="statbar'.$barclass.'">'.$spanval.'</span></td></tr>';
	
	return $output;
	}

	
function fusioncore_ajax_get_server_stats_html($args=null){
	global $lstr;

	if(is_admin()==false)
		return $lstr['NotAuthorizedErrorText'];


	// get sysstat data
	//$xml=get_backend_xml_sysstat_data();
	$xml=get_xml_sysstat_data();
	//print_r($xml);
	
	//echo "ARGS2\n";
	//print_r($args);
	
	$id=random_string(6);
	
	$output='';
	$output.='<div class="infotable_title">'.gettext('Server Statistics').'</div>';
	if($xml==null){
		$output.="No data";
		}
	else{
		$output.='
		<table class="infotable">
		<thead>
		<tr><th><div style="width: 75px;">'.gettext('Metric').'</div></th>
			<th><div style="width: 60px;">'.gettext('Value').'</div></th><th><div style="width: 105px;"></div></th></tr>
		</thead>
		<tbody>
		';
		
		$output.='<tr><td colspan="2"><span class="sysstat_stat_title">'.gettext('Load').'</span></td></tr>';
		// load 1
		$output.=fusioncore_ajax_get_stat_bar_html($xml->load->load1,"1-min",$xml->load->load1,10,100,25,75);
		// load 5
		$output.=fusioncore_ajax_get_stat_bar_html($xml->load->load5,"5-min",$xml->load->load5,10,100,25,75);
		// load 15
		$output.=fusioncore_ajax_get_stat_bar_html($xml->load->load15,"15-min",$xml->load->load15,10,100,25,75);
		
		
		$output.='<tr><td colspan="2"><span class="sysstat_stat_title">'.gettext('CPU Stats').'</span></td></tr>';
		$output.=fusioncore_ajax_get_stat_bar_html($xml->iostat->user,gettext("User"),$xml->iostat->user."%",1,100,75,95);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->iostat->nice,gettext("Nice"),$xml->iostat->nice."%",1,100,75,95);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->iostat->system,gettext("System"),$xml->iostat->system."%",1,100,75,95);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->iostat->iowait,gettext("I/O Wait"),$xml->iostat->iowait."%",1,100,5,15);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->iostat->steal,gettext("Steal"),$xml->iostat->steal."%",1,100,5,15);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->iostat->idle,gettext("Idle"),$xml->iostat->idle."%",1,100,100,100);


		$output.='<tr><td colspan="2"><span class="sysstat_stat_title">'.gettext('Memory').'</span></td></tr>';
		$total=intval($xml->memory->total);
		$output.='<tr><td><span class="sysstat_stat_subtitle">'.gettext('Total').'</div></td><td colspan="2">'.$xml->memory->total.' MB</td></tr>';
		$t=intval($xml->memory->used);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->memory->used,gettext("Used"),get_formatted_number($xml->memory->used,0)." MB",(1/$total)*100,100,98,99);
		$t=intval($xml->memory->free);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->memory->free,gettext("Free"),get_formatted_number($xml->memory->free,0)." MB",(1/$total)*100,100,101,101);
		$t=intval($xml->memory->shared);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->memory->shared,gettext("Shared"),get_formatted_number($xml->memory->shared,0)." MB",(1/$total)*100,100,101,101);
		$t=intval($xml->memory->buffers);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->memory->buffers,gettext("Buffers"),get_formatted_number($xml->memory->buffers,0)." MB",(1/$total)*100,100,101,101);
		$t=intval($xml->memory->cached);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->memory->cached,gettext("Cached"),get_formatted_number($xml->memory->cached,0)." MB",(1/$total)*100,100,101,101);

		$output.='<tr><td colspan="2"><span class="sysstat_stat_title">'.gettext('Swap').'</span></td></tr>';
		$total=intval($xml->swap->total);
		$output.='<tr><td><span class="sysstat_stat_subtitle">'.gettext('Total').'</td></td><td colspan="2">'.get_formatted_number($xml->swap->total,0).' MB</td></tr>';
		$t=intval($xml->swap->used);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->swap->used,gettext("Used"),get_formatted_number($xml->swap->used,0)." MB",(1/$total)*100,100,50,80);
		$t=intval($xml->swap->free);
		$output.=fusioncore_ajax_get_stat_bar_html($xml->swap->free,gettext("Free"),get_formatted_number($xml->swap->free,0)." MB",(1/$total)*100,100,100,100);


		$output.='
		</tbody>
		</table>';

		}
		
	$output.='
	<div class="ajax_date">'.gettext('Last Updated').': '.get_datetime_string(time()).'</div>
	';
	
	return $output;
	}
	
	
	
	
	
function fusioncore_ajax_get_component_states_html($args=null){
	global $lstr;

	if(is_admin()==false)
		return $lstr['NotAuthorizedErrorText'];


	// get sysstat data
	//$xml=get_backend_xml_sysstat_data();
	$xml=get_xml_sysstat_data();
	//print_r($xml);
	
	//echo "ARGS2\n";
	//print_r($args);
	
	$output='<div class="infotable_title">'.gettext('Fusion System Component Status').'</div>
';
	if($xml==null){
		$output.=gettext("No data");
		}
	else{
		$output.='
		<table class="infotable">
		<thead>
		<tr><th>'.gettext('Component').'</th><th>'.gettext('Status').'</th></tr>
		</thead>
		<tbody>
		';
		
		$components=array(
			"poller",
			"dbmaint",
			"cmdsubsys",
			"eventman",
			"sysstat",
			);
		foreach($components as $c){
			$output.=fusioncore_ajax_get_component_state_html($c,$xml);
			}
		$output.='
		</tbody>
		</table>';
		}
		
	$output.='
	<div class="ajax_date">'.gettext('Last Updated').': '.get_datetime_string(time()).'</div>
	';
	
	return $output;
	}
	
function fusioncore_ajax_get_component_state_html($c,$xml){
	global $lstr;

	if(is_admin()==false)
		return $lstr['NotAuthorizedErrorText'];


	if($xml==null){
		return "No data";
		return;
		}


	$actionimg="action_small.gif";
	$actionimgtitle=gettext("Actions");
	$img="unknown_small.png";
	$imgtitle=gettext("Unknown state");
	$status=SUBSYS_COMPONENT_STATUS_UNKNOWN;
	$description="";
	$menu="";
	
	switch($c){
		case "poller":
			$title=gettext("Subsystem Poller");
			$x=$xml->poller;
			$lastupdate=intval($x->last_check);
			$diff=time()-$lastupdate;
			if($diff<=3600)
				$status=SUBSYS_COMPONENT_STATUS_OK;
			else
				$status=SUBSYS_COMPONENT_STATUS_ERROR;
			$ustr=get_duration_string($diff);
			$imgtitle=gettext("Last Run ").$ustr. gettext(" Ago");
			if($lastupdate==0){
				$status=SUBSYS_COMPONENT_STATUS_UNKNOWN;
				$imgtitle=gettext("Not Run Yet");
				}
			break;
			
		case "dbmaint":
			$title=gettext("Database Maintenance");
			$x=$xml->dbmaint;
			$lastupdate=intval($x->last_check);
			$diff=time()-$lastupdate;
			if($diff<=3600)
				$status=SUBSYS_COMPONENT_STATUS_OK;
			else
				$status=SUBSYS_COMPONENT_STATUS_ERROR;
			$ustr=get_duration_string($diff);
			$imgtitle=gettext("Last Run ").$ustr. gettext(" Ago");
			if($lastupdate==0){
				$status=SUBSYS_COMPONENT_STATUS_UNKNOWN;
				$imgtitle=gettext("Not Run Yet");
				}
			break;
			
		case "cmdsubsys":
			$title=gettext("Command Subsystem");
			$x=$xml->cmdsubsys;
			$lastupdate=intval($x->last_check);
			$diff=time()-$lastupdate;
			if($diff<=120)
				$status=SUBSYS_COMPONENT_STATUS_OK;
			else
				$status=SUBSYS_COMPONENT_STATUS_ERROR;
			$ustr=get_duration_string($diff);
			$imgtitle=gettext("Last Run ").$ustr. gettext(" Ago");
			break;
			
		case "eventman":
			$title=gettext("Event Manager");
			$x=$xml->eventman;
			$lastupdate=intval($x->last_check);
			$diff=time()-$lastupdate;
			if($diff<=120)
				$status=SUBSYS_COMPONENT_STATUS_OK;
			else
				$status=SUBSYS_COMPONENT_STATUS_ERROR;
			$ustr=get_duration_string($diff);
			$imgtitle=gettext("Last Run ").$ustr. gettext(" Ago");
			break;

		case "cleaner":
			$title=gettext("Cleaner");
			$x=$xml->cleaner;
			$lastupdate=intval($x->last_check);
			$diff=time()-$lastupdate;
			if($diff<=3600)
				$status=SUBSYS_COMPONENT_STATUS_OK;
			else
				$status=SUBSYS_COMPONENT_STATUS_ERROR;
			$ustr=get_duration_string($diff);
			$imgtitle=gettext("Last Run ").$ustr. gettext(" Ago");
			if($lastupdate==0){
				$status=SUBSYS_COMPONENT_STATUS_UNKNOWN;
				$imgtitle=gettext("Not Run Yet");
				}
			break;
			
		case "sysstat":
			$title=gettext("System Statistics");
			$x=$xml->sysstat;
			$lastupdate=intval($x->last_check);
			$diff=time()-$lastupdate;
			if($diff<=120)
				$status=SUBSYS_COMPONENT_STATUS_OK;
			else
				$status=SUBSYS_COMPONENT_STATUS_ERROR;
			$ustr=get_duration_string($diff);
			$imgtitle=gettext("Last Updated ").$ustr. gettext(" Ago");
			break;
		default:
			break;
		}
		
	if($xml==null){
		$img="unknown_small.png";
		$imgtile=gettext("Data unavailable");
		}
	else{
		switch($status){
			case SUBSYS_COMPONENT_STATUS_OK:
				$img="ok_small.png";
				break;
			case SUBSYS_COMPONENT_STATUS_ERROR:
				$img="critical_small.png";
				break;
			case SUBSYS_COMPONENT_STATUS_UNKNOWN:
				$img="unknown_small.png";
				break;
			default:
				break;
			}
		}

		
	$output='
	<tr>
	<td>
		<div class="sysstat_componentstate_title">'.$title.'</div>
		<div class="sysstat_componentstate_description">'.$description.'</div>
	</td>
	<td><div class="sysstate_componentstate_image" style="text-align:center"><img src="'.theme_image($img).'" title="'.$imgtitle.'"></div></td>
	</tr>
	';
	
	return $output;
	}

	
	
?>