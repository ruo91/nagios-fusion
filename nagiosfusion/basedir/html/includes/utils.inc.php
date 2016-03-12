<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// Development Started 03/22/2008
// $Id: utils.inc.php 168 2010-06-15 01:16:23Z egalstad $

$thedir=dirname(__FILE__);
//echo "UTILS.INC.PHP DIR:".$thedir."<BR>";
//echo "HI<BR>";

require_once($thedir.'/constants.inc.php');
require_once($thedir.'/errors.inc.php');
//echo "A<BR>";
require_once($thedir.'/utils-backend.inc.php');
//echo "B<BR>";
require_once($thedir.'/utils-commands.inc.php');
//echo "C<BR>";
require_once($thedir.'/utils-components.inc.php');
//require_once($thedir.'/utils-configwizards.inc.php');
require_once($thedir.'/utils-dashboards.inc.php');
require_once($thedir.'/utils-dashlets.inc.php');

require_once($thedir.'/utils-email.inc.php');
require_once($thedir.'/utils-events.inc.php');
//require_once($thedir.'/utils-links.inc.php');
require_once($thedir.'/utils-menu.inc.php');
//require_once($thedir.'/utils-nagioscore.inc.php');
//require_once($thedir.'/utils-notifications.inc.php');
//echo "D<BR>";
//require_once($thedir.'/utils-notificationmethods.inc.php');
//echo "E<BR>";
//require_once($thedir.'/utils-objects.inc.php');
//require_once($thedir.'/utils-perms.inc.php');
//require_once($thedir.'/utils-reports.inc.php');
//require_once($thedir.'/utils-status.inc.php');

// 2012 Additions - MG 
require_once($thedir.'/utils-session.inc.php'); //moved session functions to own utils script -MG 4/2012
require_once($thedir.'/utils-tacdata.inc.php'); //moved status data specific functions to their own include for view/logic separation -MG


require_once($thedir.'/utils-servers.inc.php');
require_once($thedir.'/utils-systat.inc.php');
require_once($thedir.'/utils-tables.inc.php');
require_once($thedir.'/utils-themes.inc.php');
require_once($thedir.'/utils-updatecheck.inc.php');
require_once($thedir.'/utils-users.inc.php');
require_once($thedir.'/utils-views.inc.php');
//require_once($thedir.'/utils-wizards.inc.php');
//require_once($thedir.'/utils-xmlobjects.inc.php');
//require_once($thedir.'/utils-xmlreports.inc.php');
//require_once($thedir.'/utils-xmlstatus.inc.php');
require_once($thedir.'/utils-xmlsysstat.inc.php');
require_once($thedir.'/utils-xmlusers.inc.php');
require_once($thedir.'/utilsl.inc.php');
require_once($thedir.'/utilsx.inc.php');
//echo "F<BR>";

//2012 additions, need to be added after callback functions are initialized -MG 
require_once($thedir.'/utils-recentalerts.inc.php'); //recent alerts polling functions 
require_once($thedir.'/utils-topalertproducers.inc.php');



	
////////////////////////////////////////////////////////////////////////
// REQUEST FUNCTIONS
////////////////////////////////////////////////////////////////////////

$escape_request_vars=true;
$request_vars_decoded=false;

function map_htmlentities($arrval){

	if(is_array($arrval)){
		return array_map('map_htmlentities',$arrval);
		}
	else
		return htmlentities($arrval,ENT_QUOTES);
	}
function map_htmlentitydecode($arrval){

	if(is_array($arrval)){
		return array_map('map_htmlentitydecode',$arrval);
		}
	else
		return html_entity_decode($arrval,ENT_QUOTES);
	}


// grabs POST and GET variables
function grab_request_vars($preprocess=true,$type=""){
	global $escape_request_vars;
	global $request;
	
	// do we need to strip slashes?
	$strip=false;
	if((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) || (ini_get('magic_quotes_sybase') && (strtolower(ini_get('magic_quotes_sybase'))!= "off")))
		$strip=true;
		
	$request=array();

	if($type=="" || $type=="get"){
		foreach ($_GET as $var => $val){
			if($escape_request_vars==true){
				if(is_array($val)){
					$request[$var]=array_map('map_htmlentities',$val);
					}
				else
					$request[$var]=htmlentities(strip_tags($val),ENT_QUOTES);
				}
			else
				$request[$var]=$val;
			//echo "GET: $var = \n";
			//print_r($val);
			//echo "<BR>";
			}
		}
	if($type=="" || $type=="post"){
		foreach ($_POST as $var => $val){
			if($escape_request_vars==true){
				if(is_array($val)){
					//echo "PROCESSING ARRAY $var<BR>";
					$request[$var]=array_map('map_htmlentities',$val);
					}
				else
					$request[$var]=htmlentities($val,ENT_QUOTES);
				}
			else
				$request[$var]=$val;
			//echo "POST: $var = ";
			//print_r($val);
			//echo "<BR>\n";
			//if(is_array($val)){
			//	echo "ARR=>";
			//	print_r($val);
			//	echo "<BR>";
			//	}
			}
		}
		
	// strip slashes - we escape them later in sql queries
	if($strip==true){
		foreach($request as $var => $val)
			$request[$var]=stripslashes($val);
		}
	
		
	if($preprocess==true)
		preprocess_request_vars();
	}

function grab_request_var($varname,$default=""){
	global $request;
	global $escape_request_vars;
	global $request_vars_decoded;
	
	$v=$default;
	if(isset($request[$varname])){
		if($escape_request_vars==true && $request_vars_decoded==false){
			if(is_array($request[$varname])){
				//echo "PROCESSING ARRAY [$varname] =><BR>";
				//print_r($request[$varname]);
				//echo "<BR>";
				$v=array_map('map_htmlentitydecode',$request[$varname]);
				}
			else
				$v=html_entity_decode($request[$varname],ENT_QUOTES);
			}
		else
			$v=$request[$varname];
		}
	//echo "VAR $varname = $v<BR>";
	return $v;
	}
	
function decode_request_vars(){
	global $request;
	global $request_vars_decoded;
	
	$newarr=array();
	foreach($request as $var => $val){
		$newarr[$var]=grab_request_var($var);
		}
		
	$request_vars_decoded=true;
		
	$request=$newarr;
	}

function preprocess_request_vars(){
	global $request;
	
	// set new language
	//if(isset($request['language']))
	//	set_language($request['language']);
	// set new theme
	//if(isset($request['theme']))
	//	set_theme($request['theme']);
	}
	
	
function get_pageopt($default=""){
	global $request;
	
	$popt="";
	$popt=grab_request_var("pageopt","");
	if($popt==""){
		if(count($request)>0){
			foreach($request as $var => $val){
				$popt=$var;
				break;
				}
			}
		else
			$popt=$default;
		}
	return $popt;
	}


function have_value($var){
	if($var==null)
		return false;
	if(!isset($var))
		return false;
	if(empty($var))
		return false;
	if(is_array($var))
		return true;
	if(!strcmp($var,""))
		return false;
	return true;
	}
	



////////////////////////////////////////////////////////////////////////
// LANGUAGE FUNCTIONS
////////////////////////////////////////////////////////////////////////

function set_language($language){

	//echo "SETTING LANG: $language<br />"; 
	
	//only set gettext() locale if we have a language file
	if(!file_exists(dirname(__FILE__).'/lang/locale/'.$language)) {
		//echo "No locale dir"; 
		return; 
	}	
	//else
		//echo "Setting locale!"; 	

	// set session language
	$_SESSION["language"]=$language;
	$_SESSION['encoding'] = get_encoding($language); 
	
	//gettext support 
	setlocale(LC_MESSAGES, $language, $language.'utf-8', $language.'utf8', "en_GB.utf8");	
	putenv("LC_ALL=".$language);
	putenv("LANG=".$language);

	//non-English numeric formats will turn decimals to commas and mess up all kinds of stuff
	setlocale(LC_NUMERIC,'C'); 
	
	//bind text domains
	bindtextdomain($language, dirname(__FILE__).'/lang/locale/');
	bind_textdomain_codeset($language, 'UTF-8');
	textdomain($language); 
	
}

function get_encoding($language) {
	
	$encodings = array(
		'en_EN' => 'UTF-8',
		'de_DE' => 'UTF-8',
		'es_ES' => 'UTF-8',
		'it_IT' => 'UTF-8',
		'fr_FR' => 'UTF-8',
		'pt_PT' => 'UTF-8',
		'ru_RU' => 'KOI8-R',
		'zh_CN' => 'GB2312',
	
	); 
	
	if(isset($encodings[$language]))
		return $encodings[$language];
	else
		return 'UTF-8';

}

/*	
function read_language_file($language){
	
	// make sure language file exists before switching
	$language_file="includes/lang/".$language.".inc.php";
	if(file_exists($language_file)){
		// include language file
//		include_once($language_file);
		include($language_file);
		return true;
		}

		
	return false;
	}
*/
	
function init_language(){
	global $cfg;
		
	// read language file (always read English first in case translators missed something)
	$default_language='en_EN';
	$session_language=$default_language;
	
	// read session language if available
	if(!empty($_SESSION['language'])) {
		$session_language=$_SESSION["language"];
	}		
	else { // no session language yet - determine from defaults
	
		// try user-specific default language from DB
		$udblang=get_user_meta(0,"default_language");
		if(!empty($udlang)){
			$session_language=$udblang;
		}		
		else { // try global default language from DB
			$dblang=get_option("default_language");
			$dblang = empty($dblang) ? $default_language : $dblang; 
			$session_language=$dblang;
		}
	}	
	
	set_language($session_language); 
	
	//add language string now so that locale information will be utilized
	require(dirname(__FILE__).'/lang/en.inc.php');
			
	return true;
}

function get_languages()
{
	global $cfg;
		
	$dirs = scandir(dirname(__FILE__).'/lang/locale'); 

	// Add directories to language options
	$langs = array();
	foreach ($dirs as $dir) {
		if (is_dir(dirname(__FILE__).'/lang/locale/'.$dir) && strpos($dir, '.') === false  && !isset($cfg[$dir])) {
			$newlang = htmlentities(utf8_encode($dir), ENT_QUOTES, 'UTF-8'); 
			$langs[$newlang] = $newlang;
		}
	}

	$nlangs = array('en_EN' => 'en_EN');
	ksort($langs);
	foreach ($langs as $k => $v) {
		if ($k == 'en_EN') continue;
		$nlangs[$k] = $v;
	}
	$cfg['languages'] = $nlangs;

	return $cfg['languages'];
}

function get_proper_language($lang)
{
	$proper_langs = array('en_EN' => gettext('English'),
						  'de_DE' => gettext('German'),
						  'es_ES' => gettext('Spanish'),
						  'fr_FR' => gettext('French'),
						  'it_IT' => gettext('Italian'),
						  'ja_JP' => gettext('Japanese'),
						  'ko_KR' => gettext('Korean'),
						  'pl_PL' => gettext('Polish'),
						  'pt_PT' => gettext('Portuguese'),
						  'ru_RU' => gettext('Russian'),
						  'zh_CN' => gettext('Simplified Chinese'),
						  'zh_TW' => gettext('Traditional Chinese'));

	foreach ($proper_langs as $k => $v) {
		if ($k == $lang) {
			return $v;
		}
	}

	return $lang;
}


////////////////////////////////////////////////////////////////////////
// FORM FUNCTIONS
////////////////////////////////////////////////////////////////////////

function encode_form_val($rawval){
	return htmlentities($rawval, ENT_COMPAT, 'UTF-8');
	}
	
function yes_no($var){
	global $lstr;
	if(isset($var) && ($var==1 || $var==true))
		return $lstr['YesText'];
	return $lstr['NoText'];
	}

function is_selected($var1,$var2){
	if(is_string($var1) || is_string($var2)){
		if(!strcmp($var1,$var2))
			return "SELECTED";
		}
	else{
		if($var1==$var2)
			return "SELECTED";
		}
	return "";
	}

function is_checked($var1,$var2){
	if($var1==$var2)
		return "CHECKED";
	else if(is_string($var1) && $var1=="on")
		return "CHECKED";
	else if(!strcmp($var1,$var2))
		return "CHECKED";
	else
		return "";
	}
	
function checkbox_binary($var1){
	if(is_numeric($var1)){
		if($var1==1)
			return 1;
		else
			return 0;
		}
	else if(is_string($var1) && $var1=="on")
		return 1;
	else
		return 0;
	}


////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
////////////////////////////////////////////////////////////////////////

// gets value from array using default
function grab_array_var($arr,$varname,$default=""){
	global $request;
	
	$v=$default;
	if(is_array($arr)){
		if(array_key_exists($varname,$arr))
			$v=$arr[$varname];
		}
	return $v;
	}

// generates a random alpha-numeric string (password or backend ticket)
function random_string($len=6){
	$chars="023456789abcdefghijklmnopqrstuv";
	$rnd="";
	$charlen=strlen($chars);

	srand((double)microtime()*1000000);
	
	for($x=0;$x<$len;$x++){
		$num=rand()%$charlen;
		$ch=substr($chars,$num,1);
		$rnd.=$ch;
		}
		
	return $rnd;
	}
	

// see if NDOUtils tables exist
function ndoutils_exists(){
	if(!exec_named_sql_query('CheckNDOUtilsInstall',false))
		return false;
	return true;
	}
	
	
// see if installation is needed
function install_needed(){
	global $cfg;
	
	
	//return false;

	$db_version=get_db_version();
	if($db_version==null)
		return true;
		
	$installed_version=get_install_version();
	if($installed_version==null)
		return true;
	
	if(file_exists("/tmp/nagiosfusion.forceinstall"))
		return true;
	
	return false;
	}
	
	
// see if upgrade is needed
function upgrade_needed(){
	global $cfg;
	
	$db_version=get_db_version();
	
	if(strcmp($db_version,$cfg['db_version']))
		return true;
	
	$installed_version=get_install_version();
	if($installed_version!=get_product_version())
		return true;

	return false;
	}
	
	
// get currently install db version
function get_db_version(){
	$db_version=get_option('db_version');
	return $db_version;
	}
	
function set_db_version($version=""){
	global $cfg;
	if($version=="")
		$dbv=$cfg['db_version'];
	else
		$dbv=$version;
	set_option('db_version',$dbv);
	}
	
// get currently installed version
function get_install_version(){
	$db_version=get_option('install_version');
	return $db_version;
	}
	
function set_install_version($version=""){
	global $cfg;
	if($version=="")
		$iv=get_product_version();
	else
		$iv=$version;
	set_option('install_version',$iv);
	}
		

////////////////////////////////////////////////////////////////////////
// URL FUNCTIONS
////////////////////////////////////////////////////////////////////////

// returns base URL used to access product
function get_base_url($usefullpath=true){
	return get_base_uri($usefullpath);
	}

// returns base URI used to access product
function get_base_uri($usefullpath=true){
	global $cfg;
	
	$base_url=$cfg['base_url']."/";
	$url="";
	if($usefullpath==true){
			if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on")
				$proto="https";
			else
				$proto="http";
			if(($proto=="http" && $_SERVER["SERVER_PORT"]!="80") || ($proto=="https" && $_SERVER["SERVER_PORT"]!="443"))
				$port=":".$_SERVER["SERVER_PORT"];
			else
				$port="";

		$url=$proto."://".$_SERVER['SERVER_NAME'].$port.$base_url;
		}
	else
		$url=$base_url;

	return $url;
	}

	
// returns URL to ajax helper
function get_ajax_helper_url(){

	// determine base url to access ajax helper
	$url=get_base_url(true);
	$url.=PAGEFILE_AJAXHELPER;

	return $url;
	}
	

// returns URL to ajax proxy
function get_ajax_proxy_url(){

	// determine base url to access ajax helper
	$url=get_base_url(true);
	$url.=PAGEFILE_AJAXPROXY;

	return $url;
	}
	

// returns URL to suggest
function get_suggest_url(){

	// determine base url to access ajax helper
	$url=get_base_url(true);
	$url.=PAGEFILE_SUGGEST;

	return $url;
	}
	
	
// returns URL to update check page
function get_update_check_url(){

	$url="http://www.nagios.com/checkforupdates/?product=".get_product_name(true)."&version=".get_product_version()."&build=".get_product_build();
	
	return $url;
	}
	
	
	
// returns URL used to access current page
function get_current_url($baseonly=false,$fulluri=false){
	if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=="on")
		$proto="https";
	else
		$proto="http";
	if(($proto=="http" && $_SERVER["SERVER_PORT"]!="80") || ($proto=="https" && $_SERVER["SERVER_PORT"]!="443"))
		$port=":".$_SERVER["SERVER_PORT"];
	else
		$port="";
		
	if($fulluri==true){
		$uri="";
		$uri=$_SERVER["REQUEST_URI"];
		$url=$proto."://".$_SERVER['SERVER_NAME'].$port.$uri;
		}
	else{
		$page=$_SERVER['PHP_SELF'];
		if($baseonly==true && ($last_slash=strrpos($page,"/")))
			$page=substr($page,0,$last_slash+1);
		$url=$proto."://".$_SERVER['SERVER_NAME'].$port.$page;
		}

	return $url;
	}


// returns current page (used for online help and feedback submissions)
function get_current_page($baseonly=false){

	$page=$_SERVER['PHP_SELF'];
	
	if($last_slash=strrpos($page,"/")){
		$page_name=substr($page,$last_slash+1);
		}
	else{
		$page_name=$page;
		}
	
	// get rid of the 'backend/'
	if(defined("BACKEND") && BACKEND==true){
		}

	return $page_name;
	}
	
	
function build_url_from_current($args){
	global $request;

	//$url=$GLOBALS["HTTP_SERVER_VARS"]["REQUEST_URI"];
	$url=get_current_url();

	// possible override original request variables
	$r=$request;
	foreach($args as $var => $val){
		$r[$var]=$val;
		}

	// generate query string
	$url.="?";
	foreach($r as $var => $val){
		$url.="&".$var."=".$val;
		}

	return $url;
	}

	
function get_permalink_base(){
	global $request;

	$base="";
	
	if(!isset($request))
		grab_request_vars();

	// get current url
	$url=get_current_url(false,true);
	
	// parse url and remove permalink option from base
	$a=parse_url($url);

	// build base url
	$base=$a["scheme"]."://".$a["host"].$a["path"]."?";
	foreach($request as $var => $val){
		if($var=="fusionwindow")
			continue;
		$base.="&".urlencode($var)."=";
		if(is_array($val))
			$base.=urlencode(serialize($val)); // doesn't work, but doesn't matter for now...
		else
			$base.=urlencode($val);
		}
		
	return $base;
	}

////////////////////////////////////////////////////////////////////////
// TIMING FUNCTIONS
////////////////////////////////////////////////////////////////////////

function get_timer(){
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime; 
	return $starttime;
	}

function get_timer_diff($starttime,$endtime){
	$totaltime = ($endtime - $starttime);
	return number_format($totaltime,5);
	}
	
	





////////////////////////////////////////////////////////////////////////
// OPTION FUNCTIONS
////////////////////////////////////////////////////////////////////////

function get_option($name, $default=null){
	global $db_tables;

	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["options"]." WHERE name='".escape_sql_param($name,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql,false))){
		if($rs->MoveFirst()){
			return stripslashes($rs->fields["value"]);
			}
		}
	return $default;
	}


function set_option($name,$value){
	global $db_tables;

	// see if data exists already
	$key_exists=false;
	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["options"]." WHERE name='".escape_sql_param($name,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql))){
		if($rs->RecordCount()>0)
			$key_exists=true;
		}

	// insert new key
	if($key_exists==false){
		$sql="INSERT INTO ".$db_tables[DB_NAGIOSFUSION]["options"]." (name,value) VALUES ('".escape_sql_param($name,DB_NAGIOSFUSION)."','".escape_sql_param($value,DB_NAGIOSFUSION)."')";
		return exec_sql_query(DB_NAGIOSFUSION,$sql);
		}

	// update existing key
	else{
		$sql="UPDATE ".$db_tables[DB_NAGIOSFUSION]["options"]." SET value='".escape_sql_param($value,DB_NAGIOSFUSION)."' WHERE name='".escape_sql_param($name,DB_NAGIOSFUSION)."'";
		return exec_sql_query(DB_NAGIOSFUSION,$sql);
		}
	}

	
function delete_option($name){
	global $db_tables;

	$sql="DELETE FROM ".$db_tables[DB_NAGIOSFUSION]["options"]." WHERE name='".escape_sql_param($name,DB_NAGIOSFUSION)."'";
	return exec_sql_query(DB_NAGIOSFUSION,$sql);
	}



////////////////////////////////////////////////////////////////////////
// MISC  FUNCTIONS
////////////////////////////////////////////////////////////////////////

function in_demo_mode(){
	global $cfg;
	
	if(isset($cfg['demo_mode']) && $cfg['demo_mode']==true)
		return true;
		
	return false;
	}


// returns attribute value of a simplexml object
function get_xml_attribute($obj,$att){
	foreach($obj->attributes() as $a => $b){
		if($a==$att)
			return $b;
		}
	return "";
	} 

	
function valid_ip($address){
	if(!have_value($address))
		return false;
	return true;
 	}
	
function valid_email($email){
	$email_array = explode("@", $email);
	if(count($email_array)!=2)
		return false;
	return true;
	}
	
	
function get_component_credential($component,$cname){
	global $cfg;

	$optname=$component."_".$cname;
	
	$optval=get_option($optname);
	if($optval==null || have_value($optval)==false){
		// default to config file value if we didn't find it in the database
		$optval=$cfg['component_info'][$component][$cname];
		set_option($optname,$optval);
		}
		
	return $optval;
	}
	
function set_component_credential($component,$cname,$val){
	$optname=$component."_".$cname;
	set_option($optname,$val);
	return true;
	}
	
function get_throbber_html(){
	$html="<img src='".theme_image("throbber.gif")."'>";
	return $html;
	}
	
	
////////////////////////////////////////////////////////////////////////
// DIRECTORY FUNCTIONS
////////////////////////////////////////////////////////////////////////

function get_current_dir() {
	global $argv;
	$cur_dir = realpath($argv[0]);
	return $cur_dir;
}
	
function get_root_dir() {
	global $cfg;
	
	$root_dir = "/usr/local/nagiosfusion";

	if (array_key_exists("root_dir", $cfg)) {
		$root_dir = $cfg["root_dir"];
	}
		
	return $root_dir;
}
	
function get_base_dir() {
	global $cfg;

	$base_dir = get_root_dir()."/html";
	
	if (defined("BACKEND") && BACKEND == true) {
		$base_dir = substr($base_dir, 0 ,-8);
	}

	return $base_dir;
}
	
function get_tmp_dir() {
	$tmp_dir = get_root_dir()."/tmp";
	return $tmp_dir;
}
	
function get_backend_dir(){

	/*
	if(defined("BACKEND") && BACKEND==true)
		$backend_dir=get_current_dir();
	else
		$backend_dir=get_base_dir()."/backend";
	*/
	
	$backend_dir = get_base_dir()."/backend";

	return $backend_dir;
}

	
function get_subsystem_ticket(){
	$ticket=get_option("subsystem_ticket");
	if($ticket==null || have_value($ticket)==false){
		$ticket=random_string(8);
		set_option("subsystem_ticket",$ticket);
		}
	return $ticket;
	}
	
	

////////////////////////////////////////////////////////////////////////
// XML DB FUNCTIONS
////////////////////////////////////////////////////////////////////////

function get_xml_db_field($level, $rs, $fieldname, $nodename=""){
	if($nodename=="")
		$nodename=$fieldname;
	return get_xml_field($level,$nodename,get_xml_db_field_val($rs,$fieldname));
	}

function get_xml_db_field_val($rs, $fieldname){
	if(isset($rs->fields[$fieldname]))
		return xmlentities($rs->fields[$fieldname]);
	else
		return "";
	}
	
function get_xml_field($level, $nodename, $nodevalue){
	$output="";
	for($x=0;$x<$level;$x++)
		$output.="  ";
	$output.="<".$nodename.">".$nodevalue."</".$nodename.">\n";
	return $output;
	}
	
	

////////////////////////////////////////////////////////////////////////
// MISSING FEATURE FUNCTIONS :-)
////////////////////////////////////////////////////////////////////////
	
function do_missing_feature_page($fullhtml=true){
	global $lstr;
	
	if($fullhtml==true){
?>
<html>
<head>
	<title><?php echo $lstr['MissingFeaturePageTitle'];?></title>
	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php do_page_head_links();?>
</head>
<body>
<?php
		}
?>
<h1><?php echo $lstr['MissingFeaturePageHeader'];?></h1>
<p>
<?php echo $lstr['MissingFeatureText'];?>
</p>

<?php
	if($fullhtml==true){
?>
</body>
</html>
<?php
		}
	}
	

////////////////////////////////////////////////////////////////////////
// META DATA FUNCTIONS
////////////////////////////////////////////////////////////////////////

function get_meta($type_id,$obj_id,$key){
	global $db_tables;

	$sql="SELECT * FROM ".$db_tables[DB_NAGIOSFUSION]["meta"]." WHERE metatype_id='".escape_sql_param($type_id,DB_NAGIOSFUSION)."' AND metaobj_id='".escape_sql_param($obj_id,DB_NAGIOSFUSION)."' AND keyname='".escape_sql_param($key,DB_NAGIOSFUSION)."'";
	if(($rs=exec_sql_query(DB_NAGIOSFUSION,$sql))){
		if($rs->MoveFirst()){
			return $rs->fields["keyvalue"];
			}
		}
	return null;
	}
	

function set_meta($type_id,$obj_id,$key,$value){
	global $db_tables;
	
	// see if data exists already
	$key_exists=false;
	if(get_meta($type_id,$obj_id,$key)!=null)
		$key_exists=true;

	// insert new key
	if($key_exists==false){
		$sql="INSERT INTO ".$db_tables[DB_NAGIOSFUSION]["meta"]." (metatype_id,metaobj_id,keyname,keyvalue) VALUES ('".escape_sql_param($type_id,DB_NAGIOSFUSION)."','".escape_sql_param($obj_id,DB_NAGIOSFUSION)."','".escape_sql_param($key,DB_NAGIOSFUSION)."','".escape_sql_param($value,DB_NAGIOSFUSION)."')";
		return exec_sql_query(DB_NAGIOSFUSION,$sql);
		}

	// update existing key
	else{
		$sql="UPDATE ".$db_tables[DB_NAGIOSFUSION]["meta"]." SET keyvalue='".escape_sql_param($value,DB_NAGIOSFUSION)."' WHERE metatype_id='".escape_sql_param($type_id,DB_NAGIOSFUSION)."' AND metaobj_id='".escape_sql_param($obj_id,DB_NAGIOSFUSION)."' AND keyname='".escape_sql_param($key,DB_NAGIOSFUSION)."'";
		return exec_sql_query(DB_NAGIOSFUSION,$sql);
		}
		
	}

	
function delete_meta($type_id,$obj_id,$key){
	global $db_tables;

	$sql="DELETE FROM ".$db_tables[DB_NAGIOSFUSION]["meta"]." WHERE metatype_id='".escape_sql_param($type_id,DB_NAGIOSFUSION)."' AND metaobj_id='".escape_sql_param($obj_id,DB_NAGIOSFUSION)."' AND keyname='".escape_sql_param($key,DB_NAGIOSFUSION)."'";
	return exec_sql_query(DB_NAGIOSFUSION,$sql);
	}


////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
////////////////////////////////////////////////////////////////////////

// used to generate alert/info message boxes used in form pages...	
function get_message_text($error=true,$info=true,$msg=""){
	$output="";
	
	if(have_value($msg)){
		if($error==true)
			$divclass="errorMessage";
		else if($info==true)
			$divclass="infoMessage";
		else
			$divclass="actionMessage";

		$output.='
		<div class="message">
		<ul class="'.$divclass.'">
		';
		
		if(is_array($msg)){
			foreach($msg as $m)
				$output.="<li>".$m."</li>";
			}
		else
			$output.="<li>".$msg."</li>";
			
		$output.='
		</ul>
		</div>
		';
		}	
		
	return $output;
	}
	

//debug function
function array_dump($array) {

	print "<pre>".print_r($array,true)."</pre>"; 

}	
	
	
?>