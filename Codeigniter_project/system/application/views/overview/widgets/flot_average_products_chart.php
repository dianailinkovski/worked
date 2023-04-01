<div class="panel panel-default">
    <div class="panel-heading">
        Average # of Products Sold per Merchant - Last 180 Days
    </div>
    <div class="panel-body">
        <div id="flot-average-products-chart">
            <?php if (empty($data_points)): ?>
                <p>
                    No product stats are available at this time.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 

<?php var_dump($average_product_data_points); ?>

-->

<script type="text/javascript">

function showTooltip(x, y, contents) {
    jQuery('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5
    }).appendTo("body").fadeIn(200);
}

<?php if (!empty($average_product_data_points)): ?>

    if ($('#flot-average-products-chart').length) {
    	
        var series1 = [
           <?php 
           
           $i = 0; 
           $mod_amount = count($average_product_data_points) / 20;
           $mod_amount = intval($mod_amount);
           
           $last_month = FALSE;
           
           ?>           
           <?php foreach ($average_product_data_points as $key => $value): ?>
               <?php if (count($average_product_data_points) > 45): ?>
                   <?php
                   
                   $current_month = date('M', strtotime($key));
                   
                   ?>
                   <?php if ($current_month != $last_month): ?>           
                       [ 
                           "<?php echo date('M', strtotime($key)); ?> <?php echo date('j', strtotime($key)); ?>",
                           <?php echo $value; ?>
                       ],
                   <?php else: ?>
                       [ 
                        "<!-- <?php echo date('M', strtotime($key)); ?> <br/><?php echo date('j', strtotime($key)); ?> -->",
                        <?php echo $value; ?>
                        ],
                   <?php endif; ?>  
               <?php else: ?>
                   [
                       "<?php echo date('M', strtotime($key)); ?><br /><?php echo date('j', strtotime($key)); ?>", 
                        <?php echo $value; ?>
                   ],
               <?php endif; ?>                         
               <?php 
               
               $last_month = $current_month;
               
               $i++; 
               
               ?>
           <?php endforeach; ?>
       ];
        
    
        var plot = $.plot($("#flot-average-products-chart"),
            [ { data: series1,
                //label: "Series 1",
                //color: "#8cc152"
                color: "#00a0d1"
            },
            ],
            {
                canvas: false,
                series: {
                    lines: {
                        show: true,
                        fill: true,
                        lineWidth: 1,
                        fillColor: {
                            colors: [ { opacity: 0.5 },
                                { opacity: 0.5 }
                            ]
                        }
                    },
                    points: {
                        show: false
                    },
                    shadowSize: 0
                },
                legend: {
                    position: 'nw'
                },
                grid: {
                    hoverable: true,
                    clickable: true,
                    borderColor: '#ddd',
                    borderWidth: 1,
                    labelMargin: 10,
                    backgroundColor: '#fff'
                },
                yaxis: {
                    //min: 0,
                    //max: 15,
                    color: '#eee',
                    tickDecimals:0
                },
                xaxis: {
                	mode: "categories",
                    color: '#eee',
                    //tickSize:5
                }
            });
    
        var previousPoint = null;
        
        $("#flot-average-products-chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));
    
            if(item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
    
                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];

                    var data_date = item.series.data[x][0].replace('<br/>','');

                    console.log(item);
                    console.log(x)
    
                    showTooltip(item.pageX, item.pageY, y + ' products on ' + data_date);
                }
    
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
    
        });
    
        $("#flot-average-products-chart").bind("plotclick", function (event, pos, item) {
            if (item) {
                plot.highlight(item.series, item.datapoint);
            }
        });
    }

<?php endif; ?>    

</script>