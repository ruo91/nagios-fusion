<?php
/**
*   Generates javascript code for a Line graph
*/
function fetch_line_template($div='container',$title='',$subtitle='',$increment,$start,$ylabel='',$series=array(), $height=400, $width=800) {

    // Set a maximum if there is nothing to push the 0 to the bottom of the graphs
    $set_max = "";
    if (empty($series)) {
        $set_max = "max: 1,
                    allowDecimals: false,";
    }

    $output=<<<OUTPUT

<script type="text/javascript">

var date = new Date();

Highcharts.setOptions({
    global: { useUTC: false },
});

$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: '{$div}',
                type: 'line',
                marginRight: 130,
                marginBottom: 25,
                height: $height,
                width: $width
            },
            credits: {
                enabled: false
            },
            title: {
                text: '{$title}',
                x: -20 //center
            },
            xAxis: {
                type: 'datetime',
                maxZoom: {$increment}*1000,  //max zoom is 60 minutes 
                title: {
                    text: null
                }
            },
            yAxis: {
                title: {
                    text: '{$ylabel}'
                },
                min: 0,
                {$set_max} 
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.y +' {$ylabel}';
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },

OUTPUT;
//end heredoc string 

        //build data series 
        $output .=" series: [
    
                "; 
        $count = 0;
        foreach($series as $key => $a)  {
            if($count > 0) $output.=','; 
            $output.= "{
                name: '$key',
                pointInterval: {$increment}*1000,            //time scale, 60mn 
                pointStart: {$start}*1000,    //start time  
                data: [" . implode(',',$a) . "],
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
                name: 'Tokyo',
                data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
            }, {
                name: 'New York',
                data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
            }, {
                name: 'Berlin',
                data: [-0.9, 0.6, 3.5, 8.4, 13.5, 17.0, 18.6, 17.9, 14.3, 9.0, 3.9, 1.0]
            }, {
                name: 'London',
                data: [3.9, 4.2, 5.7, 8.5, 11.9, 15.2, 17.0, 16.6, 14.2, 10.3, 6.6, 4.8]
            }]
        });
    });
    
});
</script>
*/

    return $output; 
} //end function 

?>