<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: components.inc.php 75 2010-04-01 19:40:08Z egalstad $

if(!isset($components))
	$components=array();
	
//echo "COMPONENTS\n";
	
if(defined("SKIPCOMPONENTS")==false){

	// include all components
	$p=dirname(__FILE__)."/components/";
	$subdirs=scandir($p);
	foreach($subdirs as $sd){
		if($sd=="." || $sd=="..")
			continue;
		$d=$p.$sd;
		if(is_dir($d)){
			$cf=$d."/$sd.inc.php";
			if(file_exists($cf)){

			//echo "REGISTERING COMPONENT: $cf<BR>";
				
				//echo "COMPONENTS-ORIG<BR>";
				//print_r($components);

				$components_temp=$components;
				reset($components);
				
				include_once($cf);
				
				//echo "COMPONENTS<BR>";
				//print_r($components);
				
				$name="";
				foreach($components as $name => $carray){
					//echo "PROCESSING [$name]=$carray<BR>";
					$components[$name][COMPONENT_DIRECTORY]=basename($d);
					}
				if($name!=""){
					$components_temp[$name]=$components[$name];
					$components=$components_temp;
					}
				}
			}
		}
	}

?>