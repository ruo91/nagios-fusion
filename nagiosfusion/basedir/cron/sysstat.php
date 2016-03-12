#!/usr/bin/php -q
<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: sysstat.php 75 2010-04-01 19:40:08Z egalstad $

define("SUBSYSTEM",1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


// start session
init_session();



$max_time=50;
$sleep_time=10;  // in seconds

init_sysstat();
do_sysstat_jobs();



function init_sysstat(){

	// make database connections
	$dbok=db_connect_all();
	if($dbok==false){
		echo "ERROR CONNECTING TO DATABASES!\n";
		exit();
		}

	return;
	}

function do_sysstat_jobs(){
	global $max_time;
	global $sleep_time;

	$start_time=time();

	while(1){
	
		$n=0;
	
		// bail if if we're been here too long
		$now=time();
		if(($now-$start_time)>$max_time)
			break;
	
		process_sysstat();
		$n++;
		
		// record our run in sysstat table
		$arr=array(
			"last_check" => $now,
			);
		$sdata=serialize($arr);
		update_systat_value("sysstat",$sdata);

		// sleep for a bit...
		echo ".";
		sleep($sleep_time);
		//usleep($sleep_time);
		}
		
	echo "\n";
	}
	
function process_sysstat(){
	global $db_tables;
	
	get_machine_stats();
	}
	

	
function get_machine_stats(){

	$return_code=0;

	// GET LOAD INFO
	$cmdline=sprintf("/usr/bin/uptime | sed s/,//g | awk -F'average: ' '{  print $2 }'");
	$output=array();
	exec($cmdline,$output,$return_code);
	echo "LOAD:\n";
	//print_r($output);
	$rawload=$output[0];
	$loads=explode(" ",$rawload);
	$load=array(
		"load1" => $loads[0],
		"load5" => $loads[1],
		"load15" => $loads[2],
		);
	print_r($load);
	$sdata=serialize($load);
	update_systat_value("load",$sdata);

	// GET MEMORY INFO
	$cmdline=sprintf("/usr/bin/free -m | head --lines=2 | tail --lines=1 | awk '{ print $2,$3,$4,$5,$6,$7}'");
	$output=array();
	exec($cmdline,$output,$return_code);
	echo "MEMORY:\n";
	//print_r($output);
	$rawmem=$output[0];
	$meminfo=explode(" ",$rawmem);
	$mem=array(
		"total" => $meminfo[0],
		"used" => $meminfo[1],
		"free" => $meminfo[2],
		"shared" => $meminfo[3],
		"buffers" => $meminfo[4],
		"cached" => $meminfo[5],
		);
	print_r($mem);
	$sdata=serialize($mem);
	update_systat_value("memory",$sdata);

	// GET SWAP INFO
	$cmdline=sprintf("/usr/bin/free -m | tail --lines=1 | awk '{ print $2,$3,$4}'");
	$output=array();
	exec($cmdline,$output,$return_code);
	echo "SWAP:\n";
	//print_r($output);
	$rawswap=$output[0];
	$swapinfo=explode(" ",$rawswap);
	$swap=array(
		"total" => $swapinfo[0],
		"used" => $swapinfo[1],
		"free" => $swapinfo[2],
		);
	print_r($swap);
	$sdata=serialize($swap);
	update_systat_value("swap",$sdata);

	// GET IOSTAT INFO
	$cmdline=sprintf("/usr/bin/iostat -c 5 2 | tail --lines=2 | head --lines=1 | awk '{ print $1,$2,$3,$4,$5,$6 }'");
	$output=array();
	exec($cmdline,$output,$return_code);
	echo "IOSTAT:\n";
	//print_r($output);
	$rawiostat=$output[0];
	$iostatinfo=explode(" ",$rawiostat);
	$iostat=array(
		"user" => $iostatinfo[0],
		"nice" => $iostatinfo[1],
		"system" => $iostatinfo[2],
		"iowait" => $iostatinfo[3],
		"steal" => $iostatinfo[4],
		"idle" => $iostatinfo[5],
		);
	print_r($iostat);
	$sdata=serialize($iostat);
	update_systat_value("iostat",$sdata);
	}
	
	

?>