<?php @"SourceGuardian"; //v9.0 ?><?php // Copyright (c) 2008-2010 Nagios Enterprises, LLC.  All rights reserved. ?><?php
if(!function_exists('sg_load')){$__v=phpversion();$__x=explode('.',$__v);$__v2=$__x[0].'.'.(int)$__x[1];$__u=strtolower(substr(php_uname(),0,3));$__ts=(@constant('PHP_ZTS') || @constant('ZEND_THREAD_SAFE')?'ts':'');$__f=$__f0='ixed.'.$__v2.$__ts.'.'.$__u;$__ff=$__ff0='ixed.'.$__v2.'.'.(int)$__x[2].$__ts.'.'.$__u;$__ed=@ini_get('extension_dir');$__e=$__e0=@realpath($__ed);$__dl=function_exists('dl') && function_exists('file_exists') && @ini_get('enable_dl') && !@ini_get('safe_mode');if($__dl && $__e && version_compare($__v,'5.2.5','<') && function_exists('getcwd') && function_exists('dirname')){$__d=$__d0=getcwd();if(@$__d[1]==':') {$__d=str_replace('\\','/',substr($__d,2));$__e=str_replace('\\','/',substr($__e,2));}$__e.=($__h=str_repeat('/..',substr_count($__e,'/')));$__f='/ixed/'.$__f0;$__ff='/ixed/'.$__ff0;while(!file_exists($__e.$__d.$__ff) && !file_exists($__e.$__d.$__f) && strlen($__d)>1){$__d=dirname($__d);}if(file_exists($__e.$__d.$__ff)) dl($__h.$__d.$__ff); else if(file_exists($__e.$__d.$__f)) dl($__h.$__d.$__f);}if(!function_exists('sg_load') && $__dl && $__e0){if(file_exists($__e0.'/'.$__ff0)) dl($__ff0); else if(file_exists($__e0.'/'.$__f0)) dl($__f0);}if(!function_exists('sg_load')){$__ixedurl='http://www.sourceguardian.com/loaders/download.php?php_v='.urlencode($__v).'&php_ts='.($__ts?'1':'0').'&php_is='.@constant('PHP_INT_SIZE').'&os_s='.urlencode(php_uname('s')).'&os_r='.urlencode(php_uname('r')).'&os_m='.urlencode(php_uname('m'));$__sapi=php_sapi_name();if(!$__e0) $__e0=$__ed;if(function_exists('php_ini_loaded_file')) $__ini=php_ini_loaded_file(); else $__ini='php.ini';if((substr($__sapi,0,3)=='cgi')||($__sapi=='cli')||($__sapi=='embed')){$__msg="\nPHP script '".__FILE__."' is protected by SourceGuardian and requires a SourceGuardian loader '".$__f0."' to be installed.\n\n1) Download the required loader '".$__f0."' from the SourceGuardian site: ".$__ixedurl."\n2) Install the loader to ";if(isset($__d0)){$__msg.=$__d0.DIRECTORY_SEPARATOR.'ixed';}else{$__msg.=$__e0;if(!$__dl){$__msg.="\n3) Edit ".$__ini." and add 'extension=".$__f0."' directive";}}$__msg.="\n\n";}else{$__msg="<html><body>PHP script '".__FILE__."' is protected by <a href=\"http://www.sourceguardian.com/\">SourceGuardian</a> and requires a SourceGuardian loader '".$__f0."' to be installed.<br><br>1) <a href=\"".$__ixedurl."\" target=\"_blank\">Click here</a> to download the required '".$__f0."' loader from the SourceGuardian site<br>2) Install the loader to ";if(isset($__d0)){$__msg.=$__d0.DIRECTORY_SEPARATOR.'ixed';}else{$__msg.=$__e0;if(!$__dl){$__msg.="<br>3) Edit ".$__ini." and add 'extension=".$__f0."' directive<br>4) Restart the web server";}}$msg.="</body></html>";}	die($__msg);exit();}}return sg_load('100590ECE15C5C2CAAQAAAASAAAABHAAAACABAAAAAAAAAD/A8ea4eZlZNd5squeg9jnzdm+8a6wvwjKX6EV97yqbd7pLyxhP5qGBWZ0gk2UJuimT2tXBuxL6IRqmD6gT7QwOJa8iOq+TcybM1NWsjei6H+Xii/joglGcVAICko49wwHgo5DeHMjqg2+oedisdUC2gUAAAD4AAAAy4fAm/AGTwm6ifnXlTyoaKaeMfPls4qjSJBBFwTGnDjEYPZQpnt34Tkb1iLVCV21ZrmZnOlCqKHV0gl5pE9PhlXlGBsttbepyK483+weOi65AZpM6LqNMH2X/z12QAM5UQRhttTcNFSEjxjtDMPLdLalC+31niAoUVWbV4AWi7nJbpc+OdRMMlldeDnImtExPDVHA/BkKUKlD+TD/U3qjQ8jjWn1RIZzNN1DngXRDxSGt7aPcbdAL5yBKCjMKCwOSYF9GtGadbRPHN61juBZXrxJlZY6aFfxnNcJKeNS3qCKizMiV71gMXbHTeE7ec+BKkFsw+Xem+00AAAAAAEAAG+046W3Yv84EU7rk6NeY9hsVxiwRe35d976+XQ2Yitgbwy3g3q0YeXzJMH+QBQRr1pN/yUpPxRnRsL2icTaV4iUt4lY/dQpfuW0zMiv/8eBCFFb8YbEbQpgHsS8oS4cTH6BmR2hQNSjwlDpI7BInM6Z/nj4EKFyW2qMH0DTNdwgYfhJLW8Ukr3grgUhVyQxvdfKb+eNmKWp5kyTuIKR+5+cs8uBVLXgWV0jN5vgcC38NRWRQpXzq9YSFXeu+4uoAKi8k/GVcUwQUOlvR8mKn95eieMVzjCcVC/rQfFa3tp8fVdSo1KCNm50tUht+KaZtZ2X5O5dXoQ0qx0T/v0M6sc1AAAA+AAAAHxD/Zugyr4NhEKOmCTx2FLnzIBME+Wpvxj8fpZZGgC84p5JNinZPrejSmo/pvA0elWqj+lcQFsKltlbCFZKlYs2zlJIsTK8YF+iEmOZpm5DEih/1JclHC17Vwe7hUIdrhMw+B7xR2Xd+sprZmdL3m5vDFQNRhjlqm3nhHhWOLCChS+5Bapek6TzSCzOX3IUKK3kXyT5eLLiDZBgugAua6BKmPQ6JOGOpDUuUba0yh7aqVmlWqpaPBtLl5xrgO9GoX70LrVgJw+2/Y9liOV6OkcjFLer1DGNgP1y3ML5felQZqtXZZtR3+F+skOkVn4pQQGPi11k5Q90NgAAAAABAABF2vefHJ6onjolAWL82DRNqeEsAZnLUvyxK9X8mYWtyO7rnoUpqrrib8/Ucj0X4MqMEP6/YCRp6P7p0FSR52iUAtM4o7Swo19yKJigxd7E7Wq4fZ6YUqmcZPSBPlaszmlNKxbrT9qYUcJRbjMpw11RSknSfa8n7z4rbE40D+DXJ+BnNCX9fr3GrwuulN+oRL1V1LOU47SnBODYhWTb3hAGRBiQ2OosqBLNpXgiiEnAAMJTYpsOE7/vsIdcF+hF3eADwyHvyG9xptHwkHjGtJWrkRea4jKHUIQRs2m3FGGYvlMw6pI8SErbn1kM7o4jQXaxElEH1mPgjpJs1s7z/WFYAAAAAA==');
?>