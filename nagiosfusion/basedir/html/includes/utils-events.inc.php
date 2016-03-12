<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// Development Started 03/22/2008
// $Id: utils-events.inc.php 75 2010-04-01 19:40:08Z egalstad $

//require_once(dirname(__FILE__).'/common.inc.php');

////////////////////////////////////////////////////////////////////////
//EVENT FUNCTIONS
////////////////////////////////////////////////////////////////////////


function add_event($event_source=EVENTSOURCE_OTHER,$event_type=EVENTTYPE_OTHER,$event_time=0,$event_meta=null){
	global $db_tables;

	$timestring=sql_time_from_timestamp($event_time,DB_NAGIOSFUSION);
	
	$sql="INSERT INTO ".$db_tables[DB_NAGIOSFUSION]["events"]." (event_source,event_type,event_time,processing_time,status_code) VALUES ('".escape_sql_param($event_source,DB_NAGIOSFUSION)."','".escape_sql_param($event_type,DB_NAGIOSFUSION)."',".$timestring.",NOW(),'".escape_sql_param(EVENTSTATUS_QUEUED,DB_NAGIOSFUSION)."')";
	//echo "SQL: $sql<BR>\n";
	$rs=exec_sql_query(DB_NAGIOSFUSION,$sql);
	if(!$rs)
		$event_id=-1;
	else
		$event_id=get_sql_insert_id(DB_NAGIOSFUSION,"xi_events_event_id_seq");
		
	// add meta
	if($event_id>0 && $event_meta!=null){
		$sql="INSERT INTO ".$db_tables[DB_NAGIOSFUSION]["meta"]." (metatype_id,metaobj_id,keyname,keyvalue) VALUES ('".escape_sql_param(METATYPE_EVENT,DB_NAGIOSFUSION)."','".escape_sql_param($event_id,DB_NAGIOSFUSION)."','event_meta','".escape_sql_param(serialize($event_meta),DB_NAGIOSFUSION)."')";
		$rs=exec_sql_query(DB_NAGIOSFUSION,$sql);
		}
		
	// do callbacks
	$args=array(
		"event_id" => $event_id,
		"event_source" => $event_source,
		"event_type" => $event_type,
		"event_time" => $event_time,
		"event_meta" => $event_meta,
		);
	do_callbacks(CALLBACK_EVENT_ADDED,$args);
	
	return $event_id;
	}

?>