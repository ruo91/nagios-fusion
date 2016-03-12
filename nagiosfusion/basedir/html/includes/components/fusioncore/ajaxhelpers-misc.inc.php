<?php
// Fusion Core Ajax Helper Functions
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: ajaxhelpers-misc.inc.php 373 2014-06-04 21:16:08Z jomann $

include_once(dirname(__FILE__).'/../componenthelper.inc.php');
	

////////////////////////////////////////////////////////////////////////
// MISC AJAX FUNCTIONS
////////////////////////////////////////////////////////////////////////
	
function fusioncore_ajax_get_fusion_news_feed_html($args=null){
	global $lstr;
	
	$output='';

	$output.='
	<table class="infotable">
	<thead>
	<tr><th>&nbsp;</th></tr>
	</thead>
	<tbody>
	';
	
	$output.="<tr><td>";
	$output.="<ul>";
	
	// where do we get news from
	$url="http://www.nagios.com/backend/feeds/products/nagiosfusion/";
	
	$update_news=false;
	$news=array();
	$newsraw=get_meta(METATYPE_NONE,0,"fusionnews");
	if($newsraw==null || have_value($newsraw)==false)
		$update_news=true;
	else{
	
		$news=unserialize($newsraw);
		
		// is it time to update the news? */
		$now=time();
		if(($now-intval($news["time"])) > 60*60*24)
			$update_news=true;
			
		//print_r($news);
		}
		
	$update_news=true;
	
	// fetch new news
	if($update_news==true){
	
		// fetch news
		$rss=simplexml_load_file($url);
		//$rss=new SimpleXMLElement($url,LIBXML_NOCDATA,true);
		//print_r($rss);
		
		$newsitems=array();
		foreach($rss->channel->item as $i){
			$newsitems[]=array(
				"link" => strval($i->link),
				"title" => strval($i->title),
				"description" => strval($i->description),		
				);
			}
		
		// cache news
		$news["time"]=time();
		//$news["rss"]=json_encode($rss);
		$news["rss"]=json_encode($newsitems);
		//print_r($news);
		set_meta(METATYPE_NONE,0,"fusionnews",serialize($news));
		$newsitems_s=json_decode($news["rss"]);
		$newsitems=(array)$newsitems_s;
		}
		
	// use cached news
	else{
		//print_r($newsraw);
		$news=unserialize($newsraw);
		$newsitems_s=json_decode($news["rss"]);
		$newsitems=(array)$newsitems_s;
		}
	
	
	
	$x=0;
	//print_r($newsitems);
	foreach($newsitems as $is){
		$x++;
		if($x>3)
			break;
		$i=(array)$is;
		$link=strval($i["link"]);
		$title=strval($i["title"]);
		$description=strval($i["description"]);
		$output.="<li><a href='".$link."' target='_blank'>".$title."</a><br>".$description."</li>";
		}

		
	$output.="</ul>";
	$output.="</td></tr>";
		
	$output.='
	</tbody>
	</table>
	';
			
	return $output;
	}
	
	

function fusioncore_ajax_get_available_updates_html($args=null){
	global $lstr;
	
	// Check if we are going to force an update
	$force = false;
	if ($args != null) {
		if ($args['force'] == "yes") {
			$force = true;
		}
	}
	
	// check for updates
	do_update_check($force);

	$update_info=array(
		"last_update_check_time" => get_option("last_update_check_time"),
		"last_update_check_succeeded" => get_option("last_update_check_succeeded"),
		"update_available" => get_option("update_available"),
		"update_version" => get_option("update_version"),
		"update_release_date" => get_option("update_release_date"),
		"update_release_notes" => get_option("update_release_notes"),
		);
		
	//print_r($update_info);
	
	if($update_info["last_update_check_succeeded"]==0){
		$update_str="<p><div style='float: left; margin-right: 10px;'><img src='".theme_image("unknown_small.png")."'></div><b>".gettext("Update Check Problem: Last update check failed").".</b></p>";
		}
	else if($update_info["update_available"]==1){
		$update_str="<p><div style='float: left; margin-right: 10px;'><img src='".theme_image("critical_small.png")."'></div><b>".gettext("A new Nagios Fusion update is available").".</b></p>";
		
		if($update_info["update_release_notes"]!="")
			$update_str.="<p>".$update_info["update_release_notes"];
		
		$update_str.="<p>".gettext("Visit")." <a href='http://www.nagios.com/products/nagiosfusion/' target='_blank'>www.nagios.com</a> ".gettext("to obtain the latest update").".</p>";
		}
	else{
		$update_str="<p><div style='float: left; margin-right: 10px;'><img src='".theme_image("ok_small.png")."'></div><b>".gettext("Your Nagios Fusion installation is up to date").".</b></p>";
		}
	//$update_str.="<BR><BR>";
	
	$output='';
	
	$output.='
	<table class="infotable">
	<tbody>
	';
	
	$output.='<tr><td colspan="2">'.$update_str.'</td></tr>';

	$output.='<tr><td>'.gettext('Latest Available Version').':</td><td>'.$update_info["update_version"].'</td></tr>';
	$output.='<tr><td>'.gettext('Installed Version').':</td><td>'.get_product_version().'</td></tr>';
	$output.='<tr><td>'.gettext('Last Update Check').':</td><td>'.get_datetime_string($update_info["last_update_check_time"]).'</td></tr>';
			
	$output.='
	</tbody>
	</table>
	';
			
	$output.='
	<div class="ajax_date">'.gettext('Last Updated').': '.get_datetime_string(time()).'</div>
	';

	return $output;
	}
	
function fusioncore_ajax_get_pagetop_alert_content_html($args=null){

	$admin=is_admin();
	$urlbase=get_base_url();
	
	$error=false;
	$warning=false;

	$output="";
	
	//$output.="The time is ".time();
	
	// get sysstat data
	$xml=get_xml_sysstat_data();
	//array_dump($xml); 
	if($xml==null){
		if($admin==true)
			$output.="<a href='".$urlbase."admin/sysstat.php'>";

		$text=gettext("Could not read program data!");
		$img=theme_image("critical_small.png");
		$output.="<img src='".$img."'> ".$text;

		if($admin==true)
			$output.="</a>";
		}	
	else{

		if($admin==true)
			$output.="<a href='".$urlbase."admin/main.php'>";

			////////////////////////////////////////////////////////
	//$actionimg="action_small.gif";
	//$actionimgtitle="Actions";
	//$img="unknown_small.png";
	//$imgtitle="Unknown state";
	//$status=SUBSYS_COMPONENT_STATUS_UNKNOWN;
	//$description="";
	//$menu="";
	$components=array(
			"poller",
			"dbmaint",
			"cmdsubsys",
			"eventman",
			"sysstat",
			);
		foreach($components as $c){
			$output.=fusioncore_ajax_get_component_state_icon($c,$xml);
			}	

	/*	
	if($xml==null){
		$img="unknown_small.png";
		$imgtile="Data unavailable";
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
	}
	*/
	$class="ok";
	$t=gettext("System Ok").":&nbsp;";
	if($error==true){
		$class="error";
		$t=gettext("System Problem").":&nbsp;";
		}
	else if($warning==true){
		$class="warning";
		$t=gettext("System Problem").":&nbsp;";
		}
	$pre="<div class='pagetopalert".$class."'>";

	$post="";
	$post.="</div>";
	
	return $pre.$t.$output.$post;
	}
}	
	
	
function fusioncore_ajax_get_component_state_icon($c,$xml){
	global $lstr;

	if(is_admin()==false)
		return $lstr['NotAuthorizedErrorText'];


	if($xml==null){
		return "No data";
		return;
		}


	$actionimg="action_small.gif";
	$actionimgtitle="Actions";
	$img="unknown_small.png";
	$imgtitle="Unknown state";
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
			$imgtitle="$title: ".gettext("Last Run")." ".$ustr. " Ago";
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
			$imgtitle="$title: ".gettext("Last Run")." ".$ustr. " Ago";
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
			$imgtitle="$title: ".gettext("Last Run")." ".$ustr. " Ago";
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
			$imgtitle="$title: ".gettext("Last Run")." ".$ustr. " Ago";
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
			$imgtitle="$title: ".gettext("Last Run")." ".$ustr. " Ago";
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
			$imgtitle=" $title: ".gettext("Last Updated")." ".$ustr. " Ago";
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

		
	//$output='<div class="sysstate_componentstate_image">
			$output='<img class="subcomponent_state_image" src="'.theme_image($img).'" title="'.$imgtitle.'">'; 
				//</div>';
	
	return $output;
	}
	
	
?>