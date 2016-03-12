<?php
//
// perfgraphs.php 
//

require_once('../componenthelper.inc.php');

// Initialization stuff
pre_init();
init_session(true);

// Grab GET or POST variables 
grab_request_vars();

// Check prereqs
check_prereqs();

// Check authentication
check_authentication();

route_request();

//ajax handler 
function route_request()
{
    $cmd = grab_request_var('cmd', '');

    switch($cmd) {
        case 'gethosts':
            $sid = grab_request_var('sid', false);
            if($sid) {
                $hostxml = get_xiserver_host_list($sid);
                display_host_xml($hostxml);
            }
        break;

        case 'getservices':
            $sid = grab_request_var('sid', false);
            $host = grab_request_var('host', false); 
            if ($sid) {
                $xml = get_xiserver_service_list($sid, $host);
                display_service_xml($xml);
            }
        break;

        // Get a graph explorer graph!
        case 'getgraph':
            load_graphexplorer_json();
        break;

        case 'gethostgroups':
            $sid = grab_request_var('sid', false);
            if($sid) {
                $hgxml = get_xiserver_hostgroup_list($sid);
                display_hostgroup_xml($hgxml);
            }
        break;

        case 'getservicegroups':
            $sid = grab_request_var('sid', false);
            if ($sid) {
                $sgxml = get_xiserver_servicegroup_list($sid);
                display_servicegroup_xml($sgxml);
            }
            break;

        default: 
            echo "Unknown command";
            break;
    }
}

///////////////////////////////SWITCH FUNCTIONS//////////////////////////

function load_graphexplorer_json()
{
    global $cfg;

    $verify = grab_request_var('verify', false);
    $timeout = grab_array_var('default_timeout', 30);
    $id = grab_request_var('div', 'container');
    $host = doClean(grab_request_var('host', 'localhost'));
    $service = doClean(grab_request_var('service', ''));
    $sid = grab_request_var('sid');
    $type = grab_request_var('type', 'timeline');
    $opt = grab_request_var('opt', '');
    $height = grab_request_var('height', 300);

    $raw_url = get_nagiosxi_backend_url($sid);
    $url = str_replace('/backend/?', 'includes/components/graphexplorer/visApi.php?', $raw_url);

    // Add graph args
    $url .= "&type={$type}&host={$host}&div={$id}&height={$height}";
    $url .= (empty($service) ? "" : "&service={$service}");
    // $url .= "&from=fusion";

    if (!empty($opt)) { $url .= "&opt=" . $opt; }

    $page = get_server_data_from_url($url, $timeout);
    if ($verify) {
        $http_code = $result['info']['http_code'];
        $size = $result['info']['size_download'];
        if($size > 1000 && $http_code > 199 && $http_code < 400 ) { //validate graph is reachable
            print "true"; //JSON encode
        } else {
            print "false";
        }
        return; 
    }

    print $page['body'];
}


function display_host_xml($xml) {

    if(!$xml) return; 
    echo "<option value=''>".gettext("Select a Host")."</option>"; 
    foreach($xml->host as $host) 
        echo "<option value='{$host->host_name}'>{$host->host_name}</option>\n"; 

}

function display_service_xml($xml) {
    
    echo "<option value='_HOST_'>_HOST_</option>"; 

    if(!$xml) return; 

    foreach($xml->service as $service)
        echo "<option value='".strval($service->service_description)."'>{$service->service_description}</option>\n"; 

}


function display_hostgroup_xml($xml) {

    if(!$xml) return; 
    echo "<option value=''>".gettext("Select a Hostgroup")."</option>"; 
    foreach($xml->hostgroup as $hostgroup) 
        echo "<option value='{$hostgroup->hostgroup_name}'>{$hostgroup->hostgroup_name}</option>\n"; 

}


function display_servicegroup_xml($xml) {

    if(!$xml) return; 
    echo "<option value=''>".gettext("Select a Servicegroup")."</option>"; 
    foreach($xml->servicegroup as $servicegroup) 
        echo "<option value='{$servicegroup->servicegroup_name}'>{$servicegroup->servicegroup_name}</option>\n"; 

}



function doClean($string) {
    $string = preg_replace('/[ :\/\\\\]/', "_", $string);
    $string = rawurldecode($string);
    return $string;
}
?>