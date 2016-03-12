<?php
//
// Copyright (c) 2008-2009 Nagios Enterprises, LLC.  All rights reserved.
//
// $Id: pageparts.inc.php 168 2010-06-15 01:16:23Z egalstad $

include_once(dirname(__FILE__).'/utils.inc.php');
include_once(dirname(__FILE__).'/auth.inc.php');
//include_once(dirname(__FILE__).'/components.inc.php');


function do_page_start($opts=null,$child=false){

    if($opts==null)
        $opts=array();

    // what title should be used for the page?
    $title="";
    if(isset($opts["page_title"]))
        $title=$opts["page_title"];
    $pagetitle=get_product_name();
    if($title!="")
        $pagetitle.=" - $title";
        
    // body id
    $bid="";
    $body_id="";
    if(isset($opts["body_id"]))
        $bid=$opts["body_id"];
    if($bid!="")
        $body_id=" id='$bid'";
    
    // body class
    $bc="";
    $body_class="";
    if(isset($opts["body_class"]))
        $bc=$opts["body_class"];
    if($bc!="")
        $body_class=" class='$bc'";
    
    // body style
    $bs="";
    $body_style="";
    if(isset($opts["body_style"]))
        $bs=$opts["body_style"];
    if($bs!="")
        $body_style=" style='$bs'";
    
    // page id
    $pid="";
    $page_id="";
    if(isset($opts["page_id"]))
        $pid=$opts["page_id"];
    if($pid!="")
        $page_id=" id='$pid'";
        
    // page class
    $page_class="parentpage";
    if($child==true)
        $page_class="childpage";
    $pc="";
    if(isset($opts["page_class"]))
        $pc=$opts["page_class"];
    if($pc!="")
        $page_class.=" $pc";
    
    if($child==false){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<?php
        }
    else{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

        }
?>

<html>
    <!-- Produced by Nagios Fusion.  Copyyright (c) 2008-2010 Nagios Enterprises, LLC (www.nagios.com). All Rights Reserved. -->
    <!-- Powered by the Nagios Synthesis Framework -->
    <head>
    <title><?php echo $pagetitle;?></title>
    <meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?php do_page_head_links();?>
    <?php
        $cbargs=array("child"=>$child);
    ?>
    <?php do_callbacks(CALLBACK_PAGE_HEAD,$cbargs);?>
</head>


<body <?php echo $body_id;?><?php echo $body_class;?> <?php echo $body_style;?>>

    <?php do_callbacks(CALLBACK_BODY_START,$cbargs);?>
    
    <div <?php echo $page_id;?> class="<?php echo $page_class;?>"><!-- page-->

        <div id="header">
<?php
        do_page_header($child);
        if($child==false){
?>
        <div id="throbber"></div>
<?php
        }
?>
        </div><!--header -->


<?php 
    $throbber_image=get_base_url()."images/throbber1.gif";
    if($child==false){
?>
    <div id="mainframe">
    <div id="parentcontentthrobber"><img src='<?php echo $throbber_image;?>' /></div>
<?php
        if(is_authenticated()==true){
            $page=get_current_page();
            if($page!=PAGEFILE_LOGIN && $page!=PAGEFILE_INSTALL && $page!=PAGEFILE_UPGRADE){
?>
    <div id="fullscreen"></div>
<?php
                }
            }
        }
    else{
?>
    <div id="childcontentthrobber"><img src='<?php echo $throbber_image;?>' /></div>
<?php
        }
        
    do_callbacks(CALLBACK_CONTENT_START,$cbargs);
    }
    
    
function do_page_head_links(){

    $base_url=get_base_url();
?>
    <link rel="shortcut icon" href="<?php echo $base_url;?>images/favicon.ico" type="image/ico" />
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url;?>includes/css/jquery.autocomplete.css' />
    
    <script type='text/javascript'>
        var base_url="<?php echo $base_url;?>";
        var backend_url="<?php echo get_backend_url(false);?>";
        var ajax_helper_url="<?php echo get_ajax_helper_url();?>";
        var ajax_proxy_url="<?php echo get_ajax_proxy_url();?>";
        var suggest_url="<?php echo get_suggest_url();?>";
        var request_uri="<?php echo urlencode($_SERVER["REQUEST_URI"]);?>";
        var permalink_base="<?php echo get_permalink_base();?>";
        var demo_mode=<?php echo (in_demo_mode()==true)?1:0;?>;
        var nsp_str="<?php echo get_nagios_session_protector_id();?>";
    </script>

    <?php if (get_option("theme", "xi2014") == "xi2014") { ?>
    <!-- Bootstrap & Font-Awesome for 2014 UI -->
    <link rel="stylesheet" href="<?php echo $base_url;?>includes/css/bootstrap.min.css?<?php echo get_product_version(); ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo $base_url;?>includes/css/font-awesome.min.css?<?php echo get_product_version(); ?>" type="text/css" />
    <?php } // End use 2014 features ?>

    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery-1.8.2.min.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.colorBlend.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.checkboxes.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.autocomplete.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.easydrag.js'></script>
    <!--<script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.timer.js'></script>-->
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.timers-1.1.3.js'></script>

    <!-- this causes problems with sparkline! -->
    <!--<script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.dimensions.pack.js'></script>-->
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.bgiframe.pack.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.tooltip.pack.js'></script>

    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery.sparkline.js'></script>
    
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/jquery/jquery-ui-1.9.0.custom.min.js'></script>
    <link type="text/css" href="<?php echo $base_url;?>includes/js/jquery/css/smoothness/jquery-ui-1.9.0.custom.min.css" rel="stylesheet" />
    
    <!-- colorpicker -->
    <link rel="stylesheet" href="<?php echo $base_url;?>includes/js/jquery/colorpicker/css/colorpicker.css" type="text/css" />

    <script type="text/javascript" src="<?php echo $base_url;?>includes/js/jquery/colorpicker/js/colorpicker.js"></script>

    <!-- Fusion Core Javascript --> 
    <?php if (get_option("theme", "xi2014") == "xi2014") { ?>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/core_2014.js'></script>
    <?php } else { ?>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/core.js'></script>
    <?php } ?>
    
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/commands.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/views.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/dashboards.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/dashlets.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/tables.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/users.js'></script>
    <script type='text/javascript' src='<?php echo $base_url;?>includes/js/api.js'></script>
    
    <!-- Fusion CSS --> 
    <?php if (get_option("theme", "xi2014") == "xi2014") { ?>
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/nagiosfusion_2014.css?<?php echo get_product_version(); ?>' />
    <?php } else { ?>
    <link rel='stylesheet' type='text/css' href='<?php echo $base_url; ?>includes/css/nagiosfusion.css?<?php echo get_product_version(); ?>' />
    <?php } ?>

    <!-- Highcharts Graphing Library -->
    <script type="text/javascript" src="<?php echo $base_url; ?>includes/js/highcharts/highcharts.js?<?php echo get_product_release(); ?>"></script>
    <script type="text/javascript" src="<?php echo $base_url; ?>includes/js/highcharts/modules/exporting.js?<?php echo get_product_release(); ?>"></script>
    <?php if (get_option("hc_theme", "default") == 'gray') { ?>
        <script type="text/javascript" src="<?php echo $base_url; ?>includes/js/highcharts/themes/gray.js?<?php echo get_product_release(); ?>"></script>
    <?php } ?>

<?php
    // include css/js stuff for dashlets
    echo get_dashlets_pagepart_includes();
    }
    

function do_page_header($child){

    $cbargs=array("child"=>$child);

    do_callbacks(CALLBACK_HEADER_START,$cbargs);

    if($child==true)
        include_once(dirname(__FILE__).'/header-child.inc.php');
    else
        include_once(dirname(__FILE__).'/header.inc.php');

    do_callbacks(CALLBACK_HEADER_END,$cbargs);
    }   

    
    
function do_page_end($child=false){

    $cbargs=array("child"=>$child);
?>

    <?php do_callbacks(CALLBACK_CONTENT_END,$cbargs);?>

<?php
    if($child==false){
?>
        </div><!--mainframe-->
<?php
        }
?>
    
        
        <?php do_page_footer($child);?>
    
    </div><!--page-->

<noframes>
<!-- This page requires a web browser which supports frames. --> 
<h2><?php echo get_product_name();?></h2>
<p align="center">
<a href="http://www.nagios.com/">www.nagios.com</a><br>
Copyright (c) 2010 Nagios Enterprises, LLC<br>
</p>
<p>
<i>Note: These pages require a browser which supports frames</i>
</p>
</noframes>

    <?php do_callbacks(CALLBACK_BODY_END,$cbargs);?>

</body>

</html>
<?php
    }
    


function do_page_footer($child){

    $cbargs=array("child"=>$child);

    do_callbacks(CALLBACK_FOOTER_START,$cbargs);
    
    if($child==true)
        include_once(dirname(__FILE__).'/footer-child.inc.php');
    else
        include_once(dirname(__FILE__).'/footer.inc.php');
        
    do_callbacks(CALLBACK_FOOTER_END,$cbargs);
    }   


function display_message($error=true,$info=true,$msg=""){
    echo get_message_text($error,$info,$msg);
    }   


// Get the frame for a fusionwindow override
function get_window_frame_url($default) {
    global $request;
    
    // Default window url may have been overridden with a permalink...
    $fusionwindow = grab_request_var("fusionwindow", "");
    $rawurl = ($fusionwindow == "" ? $default : $fusionwindow);
    
    // Parse url and remove permalink option from base
    $a = parse_url($rawurl);

    // Build base url
    if (isset($a["host"])) {
        if (isset($a['port']) && $a["port"] != "80") {
            $windowurl = $a["scheme"]."://".$a["host"].":".$a["port"].$a["path"]."?";
        } else {
            $windowurl = $a["scheme"]."://".$a["host"].$a["path"]."?";
        }
    } else {
        $windowurl = $a["path"]."?";
    }

    // Query param part
    $q = (isset($a['query']) ? $a['query'] : "");
    $pairs = explode("&", $q);
    foreach ($pairs as $pair) {
        $v = explode("=", $pair);
        $v1 = (isset($v[1]) ? $v[1] : "");
        $windowurl .= "&".urlencode($v[0])."=".urlencode($v1);
    }

    return $windowurl;
}