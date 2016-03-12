<?php 
/**
*   Generates javascript code for a Bar column graph
*/
function fetch_column_template($div='container',$type='host',$names=array(),$states=array(),$height=300, $width=800) {

    // Set default 0 line to be on bottom of graph
    $set_max = "";
    if (all_states_empty($states)) {
        $set_max = "max: 1,
                    allowDecimals: false,";
    }

    $namestring  = ''; 
    foreach($names as $name)
        $namestring.="'$name',";
        
    //array_dump($states);  
    $ucType = ucfirst($type); 

    //color scheme 
    if($type=='host') $colors = "['#b2ff5f', '#FF795F', '#FEFF5F','#fed56b', '#FFE4AF']"; //green,red,orange,pale yellow,red-orange
    elseif($type=='service') $colors = "['#b2ff5f','#FEFF5F','#FF795F', '#FFC45F', '#FFE4AF','#ff4c00']"; //green,yellow,red,orange
    else $colors = "['#4572A7', '#AA4643', '#89A54E', '#80699B', '#3D96AE', '#DB843D', '#92A8CD', '#ffc387', '#ff4c00']"; //default

    //main template string in heredoc syntax 
    $output=<<<OUTPUT
<script type="text/javascript">
$(function () {
    var chart;
    var theme = 'gray';

    Highcharts.setOptions({
        colors: {$colors} 
    });

    $(document).ready(function() {
        chart = new Highcharts.Chart({
    
            chart: {
                renderTo: '{$div}',  //div #id container  
                type: 'column',
                height: $height,
                width: $width
            },
            credits: {
                enabled: false
            },
            title: {
                //top label 
                text: '{$ucType} Health Summary by Nagios Server'
            },
            xAxis: { //categories  = nagios servers
                categories: [{$namestring}]
            },
            yAxis: {
                allowDecimals: false,
                min: 0,
                {$set_max}
                title: {
                    text: 'Counts by state' //y axis label 
                }
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
OUTPUT;
//end heredoc string 

    //build data series 
    $output .=" series: [\n"; 
    $count = 0;
    foreach($states as $key => $a)  {
        if($count > 0) $output.=','; 
        $stack = (in_array($key,array('Problems','Unhandled'))) ? 'pseudo' : 'state';
        $output.= "{
            name: '$key',
            data: [" . implode(',',$a) . "],
            stack: '$stack'
        }"; 
        $count++;
    }           

    $output .="]
            });
        });
        
    }); 
    </script>"; 
        /*
                series: [{
                    name: 'Up', //state code 
                    data: [5, 3, 4,],  //value for each column 
                    stack: 'state'   //each stack has a named id 
                }, {
                    name: 'Down',
                    data: [3, 4, 4,],
                    stack: 'state'
                }, {
                    name: 'Unreachable', //state 
                    data: [2, 5, 6,],
                    stack: 'state' //pseudo state column 
                }, {
                    name: 'Problems', //state 
                    data: [3, 0, 4,],
                    stack: 'pseudo'
                }, {
                    name: 'Unhandled', //state 
                    data: [3, 0, 4,],
                    stack: 'pseudo'
                }]
            });
        });
        
    });

    */

    return $output;

} //end php function 

function all_states_empty($states) {
    foreach ($states as $state) {
        if (!empty($state[0])) {
            return false;
        }
    }
    return true;
}
?>