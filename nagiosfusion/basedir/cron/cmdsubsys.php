#!/usr/bin/php -q
<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: cmdsubsys.php 172 2010-06-19 11:17:30Z egalstad $

define("SUBSYSTEM",1);
//define("BACKEND",1);

require_once(dirname(__FILE__).'/../html/config.inc.php');
require_once(dirname(__FILE__).'/../html/includes/utils.inc.php');

$max_time=59;
$logging = true;

init_cmdsubsys();
do_cmdsubsys_jobs();



function init_cmdsubsys(){

	// make database connections
	$dbok=db_connect_all();
	if($dbok==false){
		echo "ERROR CONNECTING TO DATABASES!\n";
		exit();
		}

	return;
	}

function do_cmdsubsys_jobs(){
	global $max_time;
    global $logging;
		
	//enable logging?  
	$logging = is_null(get_option('enable_subsystem_logging')) ? true : get_option("enable_subsystem_logging");

	$start_time=time();
	$t=0;

	while(1){
	
		$n=0;
	
		// bail if if we're been here too long
		$now=time();
		if(($now-$start_time)>$max_time)
			break;
	
		$n+=process_commands();
		$t+=$n;
		
		// sleep for 1 second if we didn't do anything...
		if($n==0){
			update_sysstat();
			if($logging)
				echo ".";
			//usleep(1000000);
			sleep(5);
			}
		}
		
	update_sysstat();
	echo "\n";
	echo "PROCESSED $t COMMANDS\n";
	}
	
	
function update_sysstat(){
	// record our run in sysstat table
	$arr=array(
		"last_check" => time(),
		);
	$sdata=serialize($arr);
	update_systat_value("cmdsubsys",$sdata);
	}
	
	
function process_commands(){
	global $db_tables;
	global $cfg;

	// get the next queued command
	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["commands"]." WHERE status_code='0' AND event_time<=NOW() ORDER BY submission_time ASC";
	$args=array(
		"sql" => $sql,
		"useropts" => array(
			"records" => 1,
			),
		);
	$sql=limit_sql_query_records($args,$cfg['db_info'][DB_NAGIOSFUSION]['dbtype']);
	//echo "SQL: $sql\n";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql))){
		if(!$rs->EOF){
			process_command_record($rs);
			return 1;
			}
		}
	return 0;
	}
	
function process_command_record($rs){
	global $db_tables;
	
	echo "PROCESSING COMMAND ID ".$rs->fields["command_id"]."...\n";
	
	$command_id=$rs->fields["command_id"];
	$command=intval($rs->fields["command"]);
	$command_data=$rs->fields["command_data"];
	
	// immediately update the command as being processed
	$sql="UPDATE ".$db_tables[DB_NAGIOSFUSION]["commands"]." SET status_code='".escape_sql_param(COMMAND_STATUS_PROCESSING,DB_NAGIOSFUSION)."', processing_time=NOW() WHERE command_id='".escape_sql_param($command_id,DB_NAGIOSFUSION)."'";
	exec_sql_query(DB_NAGIOSFUSION,$sql);

	// process the command
	$result_code=process_command($command,$command_data,$result);

	// mark the command as being completed
	$sql="UPDATE ".$db_tables[DB_NAGIOSFUSION]["commands"]." SET status_code='".escape_sql_param(COMMAND_STATUS_COMPLETED,DB_NAGIOSFUSION)."', result_code='".escape_sql_param($result_code,DB_NAGIOSFUSION)."', result='".escape_sql_param($result,DB_NAGIOSFUSION)."', processing_time=NOW() WHERE command_id='".escape_sql_param($command_id,DB_NAGIOSFUSION)."'";
	exec_sql_query(DB_NAGIOSFUSION,$sql);
	}
	

function process_command($command,$command_data,&$output){
	global $cfg;
    global $logging;
	
    //don't reveal password data for certain commands
	if($logging && ($command!=1100 && $command!=2881) )
        echo "PROCESS COMMAND: CMD=$command, DATA=$command_data\n";
	
	$output="";
	$return_code=0;
	
	// get the base dir for scripts
	//$base_dir=$cfg['root_dir'];
	$base_dir=grab_array_var($cfg,"root_dir","/usr/local/nagiosfusion/");
	$script_dir=grab_array_var($cfg,"script_dir","/usr/local/nagiosfusion/scripts/");
	
	// default to no command data
	$cmdline="";
	$script_name="";
	$script_data="";
	
	// post-command function call
	$post_func="";
	$post_func_args=array();
	
	
	switch($command){
	
		case COMMAND_DELETE_DASHLET:
			$dir=$command_data;	
			$dir=str_replace("..","",$dir);
			$dir=str_replace("/","",$dir);
			$dir=str_replace("\\","",$dir);
			if($dir=="")
				return COMMAND_RESULT_ERROR;
			$cmdline="rm -rf /usr/local/nagiosfusion/html/includes/dashlets/".$dir;
			break;

		case COMMAND_INSTALL_DASHLET:
			/* $file=$command_data;	
			$file=str_replace("..","",$file);
			$file=str_replace("/","",$file);
			$file=str_replace("\\","",$file);
			if($file=="")
				return COMMAND_RESULT_ERROR;
			$cmdline="cd /usr/local/nagiosfusion/html/includes/dashlets && unzip -o /usr/local/nagiosfusion/tmp/dashlet-".$file; */
            $file=$command_data;	
			$file=str_replace("..","",$file);
			$file=str_replace("/","",$file);
			$file=str_replace("\\","",$file);
			if($file=="")
				return COMMAND_RESULT_ERROR;

			// create a new temp directory for holding the unzipped dashlet
			$tmpname=random_string(5);
			if($logging)
				echo "TMPNAME: $tmpname\n";
			$tmpdir="/usr/local/nagiosfusion/tmp/".$tmpname;
			system("rm -rf ".$tmpdir);
			mkdir($tmpdir);
			
			// unzip dashlet to temp directory
			$cmdline="cd ".$tmpdir." && unzip -o /usr/local/nagiosfusion/tmp/dashlet-".$file;
			system($cmdline);
			
			// determine dashlet directory/file name
			$cdir=system("ls -1 ".$tmpdir."/");
			$cname=$cdir;
			
			// make sure this is a dashlet
			$isdashlet=true;

			// check for register_dashlet...
			$cmdline="grep register_dashlet ".$tmpdir."/".$cdir."/".$cname.".inc.php | wc -l";
			if($logging)
				echo "CMD=$cmdline";
			$out=system($cmdline,$rc);
			if($logging)
				echo "OUT=$out";		
			if($out=="0")
				$isdashlet=false;
			
			// check to make sure its not a component...
			$cmdline="grep register_component ".$tmpdir."/".$cdir."/".$cname.".inc.php | wc -l";
			if($logging)
				echo "CMD=$cmdline";
			$out=system($cmdline,$rc);
			if($logging)
				echo "OUT=$out";		
			if($out!="0")
				$isdashlet=false;

			if($isdashlet==false){
			
				// delete temp directory
				system("rm -rf ".$tmpdir);

				$output="Uploaded zip file is not a dashlet.";
				echo $output."\n";
				return COMMAND_RESULT_ERROR;
				}
			if($logging)	
				echo "Dashlet looks ok...";
			
			
			// make new dashlet directory (might exist already)
			@mkdir("/usr/local/nagiosfusion/html/includes/dashlets/".$cname);
			
			// move dashlet to production directory and delete temp directory
			$cmdline="chmod -R 755 ".$tmpdir." && chown -R nagios.nagios ".$tmpdir." && cp -rf ".$tmpdir."/".$cdir." /usr/local/nagiosfusion/html/includes/dashlets/ && rm -rf ".$tmpdir;

			break;

		case COMMAND_PACKAGE_DASHLET:
			$dir=$command_data;	
			$dir=str_replace("..","",$dir);
			$dir=str_replace("/","",$dir);
			$dir=str_replace("\\","",$dir);
			if($dir=="")
				return COMMAND_RESULT_ERROR;
			$cmdline="cd /usr/local/nagiosfusion/html/includes/dashlets && zip -r /usr/local/nagiosfusion/tmp/dashlet-".$dir.".zip ".$dir;
			break;

		case COMMAND_DELETE_COMPONENT:
			$dir=$command_data;	
			$dir=str_replace("..","",$dir);
			$dir=str_replace("/","",$dir);
			$dir=str_replace("\\","",$dir);
			if($dir=="")
				return COMMAND_RESULT_ERROR;
			$cmdline="rm -rf /usr/local/nagiosfusion/html/includes/components/".$dir;
			break;

		case COMMAND_INSTALL_COMPONENT:
			/* $file=$command_data;	
			$file=str_replace("..","",$file);
			$file=str_replace("/","",$file);
			$file=str_replace("\\","",$file);
			if($file=="")
				return COMMAND_RESULT_ERROR;
			$cmdline="cd /usr/local/nagiosfusion/html/includes/components && unzip -o /usr/local/nagiosfusion/tmp/component-".$file;
			$component_name=substr($file,0,strlen($file)-4);
			$post_func="install_component";
			$post_func_args=array(
				"component_name" => $component_name,
				"component_dir" => "/usr/local/nagiosfusion/html/includes/components/".$component_name,
				); */
            $file=$command_data;	
			$file=str_replace("..","",$file);
			$file=str_replace("/","",$file);
			$file=str_replace("\\","",$file);
			if($file=="")
				return COMMAND_RESULT_ERROR;

			// create a new temp directory for holding the unzipped component
			$tmpname=random_string(5);
			if($logging)
				echo "TMPNAME: $tmpname\n";
			$tmpdir="/usr/local/nagiosfusion/tmp/".$tmpname;
			system("rm -rf ".$tmpdir);
			mkdir($tmpdir);
			
			// unzip component to temp directory
			$cmdline="cd ".$tmpdir." && unzip -o /usr/local/nagiosfusion/tmp/component-".$file;
			system($cmdline);
			
			// determine component directory/file name
			$cdir=system("ls -1 ".$tmpdir."/");
			$cname=$cdir;
			
			// make sure this is a component
			$cmdline="grep register_component ".$tmpdir."/".$cdir."/".$cname.".inc.php | wc -l";
			if($logging)
				echo "CMD=$cmdline";
			$out=system($cmdline,$rc);
			if($logging)
				echo "OUT=$out";
			if($out=="0"){
			
				// delete temp directory
				system("rm -rf ".$tmpdir);

				$output="Uploaded zip file is not a component.";
				echo $output."\n";
				return COMMAND_RESULT_ERROR;
				}
				
			if($logging)	
				echo "Component looks ok...";
			
			// null-op
			$cmdline="/bin/true";
			
			// make new component directory (might exist already)
			@mkdir("/usr/local/nagiosfusion/html/includes/components/".$cname);
			
			// move component to production directory and delete temp directory
			//added permissions fix to make sure all new components are executable
			$cmdline="chmod -R 755 ".$tmpdir." && chown -R nagios.nagios ".$tmpdir." && cp -rf ".$tmpdir."/".$cdir." /usr/local/nagiosfusion/html/includes/components/ && rm -rf ".$tmpdir;

				
			$component_name=$cname;
			$post_func="install_component";
			$post_func_args=array(
				"component_name" => $component_name,
				"component_dir" => "/usr/local/nagiosfusion/html/includes/components/".$component_name,
				);
			
			break;

		case COMMAND_PACKAGE_COMPONENT:
			$dir=$command_data;	
			$dir=str_replace("..","",$dir);
			$dir=str_replace("/","",$dir);
			$dir=str_replace("\\","",$dir);
			if($dir=="")
				return COMMAND_RESULT_ERROR;
			$cmdline="cd /usr/local/nagiosfusion/html/includes/components && zip -r /usr/local/nagiosfusion/tmp/component-".$dir.".zip ".$dir;
			break;

		case COMMAND_CHANGE_TIMEZONE:
			$timezone = $command_data;
			$cmdline = "sudo ".get_root_dir()."/scripts/change_timezone.sh -z '$timezone'";
			break;

		case COMMAND_UPDATE_FUSION_TO_LATEST:
			$data = unserialize($command_data);
			$file = $data[0];
			$cmdline = "sudo ".get_root_dir()."/scripts/upgrade_to_latest.sh -f '$file'";
			break;

		case COMMAND_DELETE_UPGRADE_LOG:
			$file = $command_data;
			$cmdline = "rm -f " . $file;
			break;

		default:
			echo "INVALID COMMAND ($command)!\n";
			return COMMAND_RESULT_ERROR;
			break;
		}
	
	// we're running a script, so generate the command line to execute
	if($script_name!=""){
		if($script_data!="")
			$cmdline=sprintf("cd %s && ./%s %s",$script_dir,$script_name,$script_data);
		else
			$cmdline=sprintf("cd %s && ./%s",$script_dir,$script_name);
		}
		
	// run the system command
	echo "CMDLINE=$cmdline\n";
	$return_code=127;
	$output="";
	if($cmdline!="")
		$output=system($cmdline,$return_code);
	
	echo "OUTPUT=$output\n";
	echo "RETURNCODE=$return_code\n";
	
	// run the post function call
	if($return_code==0 && $post_func!=""){
		echo "RUNNING POST FUNCTION CALL: $post_func\n";
		$return_code=$post_func($post_func_args);
		echo "POST FUNCTION CALL RETURNCODE=$return_code\n";
		}
	
	if($return_code!=0)
		return COMMAND_RESULT_ERROR;
	return COMMAND_RESULT_OK;
	}

?>