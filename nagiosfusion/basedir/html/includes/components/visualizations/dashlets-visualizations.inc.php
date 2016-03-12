<?php
//
//  Dashlet visualization functions
//

// Host health dashlet functions 
function host_health_dashlet_func($mode=DASHLET_MODE_PREVIEW, $id="", $args=null) {
    $output = "";
    $args['objecttype'] = 'host';
    $args['graphtype'] = 'bar'; 

    switch($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            break;
        case DASHLET_MODE_OUTBOARD:
            $output = visualization_dashlet($args);
            break;
        case DASHLET_MODE_INBOARD:
            $output = visualization_dashlet($args, 1);
            break;
        case DASHLET_MODE_PREVIEW:
            $output = "<img src='".visualizations_component_get_baseurl()."images/hosthealth.jpg' height='100' />";
            break;
    }
    return $output;
}

// Service health dashlet functions 
function service_health_dashlet_func($mode=DASHLET_MODE_PREVIEW, $id="", $args=null) {
    $output = "";
    $args['objecttype'] = 'service';
    $args['graphtype'] = 'bar'; 

    switch($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            break;
        case DASHLET_MODE_OUTBOARD:
            $output = visualization_dashlet($args);
            break;
        case DASHLET_MODE_INBOARD:
            $output = visualization_dashlet($args, 1);
            break;
        case DASHLET_MODE_PREVIEW:
            $output = "<img src='".visualizations_component_get_baseurl()."images/servicehealth.jpg' height='100' />";
            break;
    }
    return $output;
}

// Service health dashlet functions 
function alert_histogram_dashlet_func($mode=DASHLET_MODE_PREVIEW, $id="", $args=null) {
    $output = "";
    $args['graphtype'] = 'line'; 

    switch($mode) {
        case DASHLET_MODE_GETCONFIGHTML:
            break;
        case DASHLET_MODE_OUTBOARD:
            $output = alert_histogram_dashlet($args);
            break;
        case DASHLET_MODE_INBOARD:
            $output = alert_histogram_dashlet($args, 1);
            break;
        case DASHLET_MODE_PREVIEW:
            $output = "<img src='".visualizations_component_get_baseurl()."images/alerthistogram.jpg' height='100' />";
            break;
    }
    return $output;
}

// Dashlet for vizualizations, must define graphtype and objecttype 
function visualization_dashlet($args, $inboard=0)
{
    $height = grab_array_var($args, 'iframe_height', 300); 
    $type = grab_array_var($args, 'objecttype', 'host');    
    $graphtype = grab_array_var($args, 'graphtype', 'bar');
    $id = "visualization_{$type}health_".random_string(6);
    $url = visualizations_component_get_baseurl().'index.php?mode=api&objecttype='.$type.'&graphtype='.$graphtype.'&div='.$id.'_graph'; 

    // Start output
    $output = '<div class="infotable_title">'.ucfirst($type).' Health</div>';
    $output .= '
    <div class="visualization_'.$type.'health_dashlet" id="'.$id.'">
        <img src="'.theme_image("throbber.gif").'">
    </div> <!-- visualization_'.$type.'health_dashlet -->

    <div class="visualization_'.$type.'health_dashlet" id="'.$id.'_graph"></div>

    <script type="text/javascript">
    $(document).ready(function() {

        get_'.$id.'_content();
            
        $("#'.$id.'").everyTime(60*1000, "timer-'.$id.'", function(i) {
            get_'.$id.'_content();
        });

        $("#'.$id.'").closest(".ui-resizable").on("resizestop", function(e, ui) {
            var height = ui.size.height - 17;
            var width = ui.size.width;
            get_'.$id.'_content(height, width);
        });
        
        function get_'.$id.'_content(height, width) {

            if (height == undefined) { var height = ($("#'.$id.'").parent(".visualizations_map_inboard").height() - 30); }
            if (width == undefined) { var width = $("#'.$id.'").parent(".visualizations_map_inboard").width(); }

            var url = "'.$url.'";
            if ('.$inboard.') {
                url = url + "&width=" + width + "&height=" + height;
            }
            $("#'.$id.'").load(url);
        }
    });
    </script>';

    return $output;
}

function alert_histogram_dashlet($args=array(), $inboard=0)
{
    $height = grab_array_var($args, 'iframe_height', 300); 
    $id = "visualization_alert_histogram_".random_string(6);
    $url = visualizations_component_get_baseurl().'index.php?mode=api&graphtype=line&div='.$id.'_graph'; 
        
    $output = '<div class="infotable_title">Today\'s Alerts By Hour</div>';
    $output .= '
    <div class="visualization_alert_histogram_dashlet" id="'.$id.'">
        <img src="'.theme_image("throbber.gif").'">
    </div> <!-- visualization_alert_histogram_dashlet -->

    <div class="visualization_alert_histogram_dashlet" id="'.$id.'_graph"></div>

    <script type="text/javascript">
    $(document).ready(function(){

        get_'.$id.'_content();
            
        $("#'.$id.'").everyTime(60*1000, "timer-'.$id.'", function(i) {
            get_'.$id.'_content();
        });
        
        $("#'.$id.'").closest(".ui-resizable").on("resizestop", function(e, ui) {
            var height = ui.size.height - 17;
            var width = ui.size.width;
            get_'.$id.'_content(height, width);
        });

        function get_'.$id.'_content(height, width) {

            if (height == undefined) { var height = ($("#'.$id.'").parent(".visualizations_map_inboard").height() - 30); }
            if (width == undefined) { var width = $("#'.$id.'").parent(".visualizations_map_inboard").width(); }

            var url = "'.$url.'";
            if ('.$inboard.') {
                url = url + "&width=" + width + "&height=" + height;
            }
            $("#'.$id.'").load(url);
        }
    });
    </script>';

    return $output; 
}

//////////////////////GRAPH FUNCTIONS /////////////////////////////////

function fetch_bar() {
    $height = grab_request_var('height', 300);
    $width = grab_request_var('width', 800);
    
    $object_type = grab_request_var('objecttype','host'); 
    $div = grab_request_var('div','container'); 

    $servers = get_servers(); 
    //array_dump($servers); 
    $names = array();
    $h_states = array(  'Up'    => array(),
                        'Down'  => array(),
                        'Unreachable' => array(),
                        'Problems' => array(),
                        'Unhandled'=>array(),
                    );
    $s_states = array(  'Ok' => array(),
                        'Warning' => array(),
                        'Critical' => array(),
                        'Unknown' => array(),
                        'Problems' => array(),
                        'Unhandled' => array(),
                        );              
                    
    foreach($servers as $sid => $s) {
        
        $xml = get_tacdata_xml_from_db($sid,$error,$output); 
        if($xml) {
            $names[] = $s['name'];
            $h_states['Up'][] = intval($xml->hoststatustotals->up->total);
            $h_states['Down'][] = intval($xml->hoststatustotals->down->total);
            $h_states['Unreachable'][] = intval($xml->hoststatustotals->unreachable->total);
            $h_states['Problems'][] = ( intval($xml->hoststatustotals->down->total) + intval($xml->hoststatustotals->unreachable->total) );
            $h_states['Unhandled'][] = ( intval($xml->hoststatustotals->down->unhandled) + intval($xml->hoststatustotals->unreachable->unhandled) ); 
            
            $s_states['Ok'][] = intval($xml->servicestatustotals->ok->total);
            $s_states['Warning'][] = intval($xml->servicestatustotals->warning->total);
            $s_states['Critical'][] = intval($xml->servicestatustotals->critical->total);
            $s_states['Unknown'][] = intval($xml->servicestatustotals->unknown->total);
            $s_states['Problems'][] = ( intval($xml->servicestatustotals->warning->total) + intval($xml->servicestatustotals->unknown->total) +intval($xml->servicestatustotals->critical->total) );
            $s_states['Unhandled'][] = ( intval($xml->servicestatustotals->warning->unhandled) + intval($xml->servicestatustotals->critical->unhandled) + intval($xml->servicestatustotals->unknown->unhandled) );          

        }
    }

    $states = $object_type=='host' ? $h_states : $s_states;         

    return fetch_column_template($div, $object_type, $names, $states, $height, $width);     
}   

function fetch_line() {

    $div = grab_request_var('div','container');
    $height = grab_request_var('height', 400);
    $width = grab_request_var('width', 800); 

    $title="Today\'s Alerts By Hour"; 
    $ylabel="Alerts";
    $categories = array(); 
    $series = array();
    $servers = get_servers(); 

                                        
    //hours of the day -> make this more abstract or allow for today OR yesterday?? 
    $increment = 60*60; // 1 hour increment 
    $today = strtotime("00:00:00");
    $start = $today;
    //$yesterday = strtotime('-1 day', $today);
    
    foreach($servers as $sid => $array) {
        //get xml data for today's alerts

        $xml = get_alert_histogram_xml($sid,$array['type']);
        if(!$xml) continue; //skip if there's no data 
                    
        $thisname = $array['name']; 
        //echo $thisname."<br />"; 
        $series[$thisname] = array();   
        
        if(isset($xml->histogramelement->total)) { //requires XI 2011r1.5 or greater 
            foreach($xml->histogramelement as $element) {
                //echo "{$element->total}<br />"; 
                $series[$thisname][] = intval($element->total);                 
            }   
        }   
        //array_dump($series);          
    }   
        
    return fetch_line_template($div, $title, '', $increment, $start, $ylabel, $series, $height, $width);
}