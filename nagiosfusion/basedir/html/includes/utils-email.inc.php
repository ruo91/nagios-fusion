<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// Development Started 03/22/2008
// $Id: utils-email.inc.php 75 2010-04-01 19:40:08Z egalstad $

//require_once(dirname(__FILE__).'/common.inc.php');
require_once(dirname(__FILE__).'/phpmailer/class.phpmailer.php');

////////////////////////////////////////////////////////////////////////
// EMAIL FUNCTIONS
////////////////////////////////////////////////////////////////////////

// sends a sime text email message
function send_email($opts,&$debug=null){

	// make sure we have what we need
	if(!isset($opts["from"]))
		return false;
	if(!isset($opts["to"]))
		return false;
	if(!isset($opts["subject"]))
		return false;
	if(!isset($opts["message"]))
		return false;
	
	// OLD CODE - Replaced with phpmailer 11/14/2009
	/*
	$headers="";
	if(isset($opts["headers"]))
		$header=$opts["headers"];
	$headers.="From: ".$opts["from"]."\r\n";
	
	$sendmailopts="-f".$opts["from"];
	
	//print_r($opts);

	mail($opts["to"],$opts["subject"],$opts["message"],$headers,$sendmailopts);
	*/
	
	// get mail options
	$mailmethod=get_option("mail_method");
	$fromaddress=get_option("mail_from_address");
	$smtphost=get_option("smtp_host");
	$smtpport=get_option("smtp_port");
	$smtpusername=get_option("smtp_username");
	$smtppassword=get_option("smtp_password");
	$smtpsecurity=get_option("smtp_security");
	
	$debuginfo="";
	
	// instantiate phpmailer
	$mail=new PHPMailer();
	
	// use global from address instead of one specified by user
	$address_parts=parse_email_address($fromaddress);
	$mail->SetFrom($address_parts["email"],$address_parts["name"]);
	$mail->AddReplyTo($address_parts["email"],$address_parts["name"]);
	
	// to address
	$address_parts=parse_email_address($opts["to"]);
	$mail->AddAddress($address_parts["email"],$address_parts["name"]);
	
	$mail->Subject=$opts["subject"];
	
	// add html <br> tags to newlines for mail readers in HTML mode
	$opts["message"]=nl2br($opts["message"]);
	
	// text body
	$mail->MsgHTML($opts["message"]);
	$mail->IsHTML(false);
	
	// timeout
	//$mail->Timeout(10);
	
	// use SMTP method?
	if($mailmethod=="smtp"){
	
		$debuginfo="method=smtp";

		$mail->IsSMTP();
		$mail->Host=$smtphost;
		$mail->Port=intval($smtpport);
		
		$debuginfo.=";host=$smtphost";
		$debuginfo.=";port=$smtpport";
		
		// use SMTP Auth
		if(have_value($smtpusername)==true){
			$debuginfo.=";smtpauth=true";
			$mail->SMTPAuth=true;
			$mail->Username=$smtpusername;
			$mail->Password=$smtppassword;
			}
			
		// optionally use TLS or SSL
		if($smtpsecurity=="tls"){
			$mail->SMTPSecure="tls";
			$debuginfo.=";security=tls";
			}
		else if($smtpsecurity=="ssl"){
			$mail->SMTPSecure="ssl";
			$debuginfo.=";security=ssl";
			}
		else{
			$debuginfo.=";security=none";
			}
		}
	// mail method
	else{
		$debuginfo="method=sendmail";
		}
	
	// send it!
	if(!$mail->Send()){
		if(!isset($opts["debug"]))	
			$debug=$mail->ErrorInfo." (".$debuginfo.")";
		return false;
		}
	else{
		if(!isset($opts["debug"]))	
			$debug="Message sent! (".$debuginfo.")";
		return true;
		}	

	return true;
	}
	
	
function parse_email_address($a){

	$arr=array(
		"name" => "",
		"email" => "",
		);
		
	$parts=explode("<",$a);
	
	// just the address
	if(count($parts)==1){
		$arr["email"]=trim($parts[0]);
		}
	else{
		$arr["name"]=trim($parts[0]);
		$parts=explode(">",$parts[1]);
		$arr["email"]=trim($parts[0]);
		}
		
	//echo "$a=";
	//print_r($arr);
	//echo "<BR>";

	return $arr;
	}

?>