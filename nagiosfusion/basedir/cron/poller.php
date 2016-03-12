#!/usr/bin/php -q
<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
//

define("SUBSYSTEM",1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
@require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');


// start session
init_session();  //add check in init session that ignores subsystem sessions


init_poller();
poll_server_data();



function init_poller(){

	// make database connections
	$dbok=db_connect_all();
	if($dbok==false){
		echo "ERROR CONNECTING TO DATABASES!\n";
		exit();
		}

	return;
}




	
function poll_server_data(){

	$max_time=275;  //max time is 5 mn 
	$sleep_time=10; 

	//used if no sessions are active 
	$passive_interval = is_null(get_option('passive_polling_interval')) ? 270 : get_option('passive_polling_interval');
	//use during active web sessions for more current data 
	$active_interval = is_null(get_option('active_polling_interval')) ? 30 : get_option('active_polling_interval');

	$start_time=time();
	$last_update = $start_time; 

	//save args to array for callback functions to access 
	$args = array(); 
	$args['start_time'] = $start_time; 
	$args['last_update'] = $last_update; 
	$args['passive_interval'] = $passive_interval;
	$args['active_interval'] = $active_interval; 
	$args['runtime'] = 0;
	$args['first_run'] = 1; 
	$args['session'] = active_session_exists();

	update_tac_data(); 
	$cb = do_callbacks(CALLBACK_POLLING_FUNCTIONS,$args); 
	echo "$cb callbacks run\n"; 
	
	//daemon loop 
	while(1){
	
		$n=0;
	
		// bail if if we're been here too long
		$now=time();
		if(($now-$start_time)>$max_time)
			break;

		$session = active_session_exists(); //boolean 

		//determine intervals 
		$poll_interval = ($session) ? $active_interval : $passive_interval;  //which interval are we using? 
		$interval = $now-$last_update; //how long since the last run?? 
		
		if( $interval >= $poll_interval) {
			echo "***GET DATA!***\n"; 
			echo "RUNTIME: ".($now-$start_time)." INTERVAL: ".$interval." POLL INTERVAL: $poll_interval\n"; 
			$loopstart=time(); 
			
			update_tac_data();

			//push new data into $args to be used by callback functions 
			$args['runtime'] = ($now-$start_time);
			$args['interval'] = $interval; //how long since the last run 
			$args['poll_interval'] = $poll_interval;
			$args['session']  = $session; 
			$args['first_run'] = 0;
				
		  	//run any other registered functions 
			$cb = do_callbacks(CALLBACK_POLLING_FUNCTIONS,$args); 
			echo "$cb callbacks run\n"; 

			$last_update = time(); 
			echo"Loop time: ".($last_update-$loopstart)." seconds\n"; 
			
		}


		update_sysstat(); 

		// sleep for a bit...
		echo ".";
		sleep($sleep_time);
		
	}//end while 
		
	echo "DONE\n";
}
	

function update_tac_data(){

	//get_server_data 
	$servers=get_servers();
	foreach($servers as $sid => $sinfo) {
		
		$xml=false; //handle problem servers 
			
		// get saved credentials
		$sc=get_option("server_credentials");
		if($sc==null)
			$sc=array();
		else
			$sc=unserialize($sc);	
		
		// get credentials
		$username=grab_array_var($sc[$sid],"username");
		$password=grab_array_var($sc[$sid],"password");
		
		if(!have_value($username) || !have_value($password))
			continue; //skip servers without credentials 
		else {	
			$sinfo=get_server_info($sid);
			$error=false;
		
			//fetch the live status info 
			switch($sinfo["type"]){
				case "nagiosxi":
					list($error,$hxml,$sxml,$mxml) = get_nagiosxi_tac_overview_data($sid,$username,$password,$output);
					if(!$error)
						$xml=get_nagiosxi_server_tac_stats($hxml,$sxml,$mxml);
					tacdata_save_to_db($xml,$sid,$error,$output);
				break;
					
				case "nagioscore":	
					list($error,$xml) = get_nagioscore_tac_overview_data($sid,$username,$password,$output);
					tacdata_save_to_db($xml,$sid,$error,$output);					
				break;
				
				/*				
				case 'nagiosvshell': 
					list($error,$xml) = @get_nagiosvshell_tac_overview_data($sid,$username,$password,$output);
					tacdata_save_to_db($xml,$sid,$error,$output);
				break; 
				*/ 
				default:
				break; 
			}//end switch 
		}//end else 

	}//end foreach 

}//end update_tac_data() 

function update_sysstat(){
	// record our run in sysstat table
	$arr=array(
		"last_check" => time(),
		);
	$sdata=serialize($arr);
	update_systat_value("poller",$sdata);
	}	
	

?>