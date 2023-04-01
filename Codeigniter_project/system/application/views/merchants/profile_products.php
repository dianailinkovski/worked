<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Merchant Profile</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> Market Visibility
            <i class="fa fa-angle-right"></i> <a href="/merchants">Merchant Info</a>
            <i class="fa fa-angle-right"></i> Merchant Profile
        </div>   
        
        <h3 class="large-subtitle">
            <?php echo $merchant_profile_name; ?>
        </h3>
        
        <ul class="nav nav-tabs" style="margin-bottom: 20px;">
            <li>
                <a href="/merchants/profile/<?php echo $merchant_id; ?>">Basics</a>
            </li>
            <li class="active">
                <a href="/merchants/profile_products/<?php echo $merchant_id; ?>">Products</a>
            </li>
            <li>
                <a href="/merchants/profile_violations/<?php echo $merchant_id; ?>">Violations</a>
            </li>
        </ul>
        
        <!--  
        <div class="filters">
            <form action="/merchants/profile_products/<?php echo $merchant_id; ?>" method="post">
            
                <section class="clear select_report" id="date_range">
                    <div class="leftCol">
                        <label>Date Range</label>
                    </div>
                    <div class="rightCol">
                        <div class="inputContainer">
                            <input class="start dateInput" value="<?= $date_from ?>" id="date_from" name="date_from" max="<?= date('Y-m-d'); ?>"/>
                            <img src="<?= frontImageUrl() ?>icons/24/50.png" alt="Start Date" id="date_from_a" width="24" height="24" class="imgIcon" />
                        </div>
                        <div class="inputContainer">
                            <input class="start dateInput" value="<?= $date_to ?>" id="date_to" name="date_to" max="<?= date('Y-m-d'); ?>"/>
                            <img src="<?= frontImageUrl() ?>icons/24/50.png" alt="Stop Date" id="date_to_a" width="24" height="24" class="imgIcon" />
                        </div>
                        <div class="inputContainer">
                            <input class="btn btn-success" type="submit" value="Search Product History" />
                        </div>
                    </div>
                    <div class="clear"></div>
                </section>
                
            </form>
        </div>
        -->
        
        <div id="mp-product-count-chart-area">
            <h3 class="mp-subtitle">
                Products Listed Stats
            </h3>            
            
            <div id="product-count-chart" <?php if (empty($product_data_rows)): ?>style="display:none;"<?php endif; ?>>
                
            </div>
            <?php if (empty($product_data_rows)): ?>
                <p>
                    No product stats available for this merchant.
                </p>
            <?php endif; ?>
        </div>
        
        <div id="mp-product-catalog">
        
            <h3 class="mp-subtitle">
                Products Listed
            </h3> 
                    
            <div id="merchant-products">
            
                <?php if (empty($products)): ?>
                
                    <p>
                        No products are currently listed with this merchant that are being tracked.
                    </p>
                
                <?php else: ?>
       
                    <table class="table table-bordered table-striped table-success table-responsive" id="merchant-products-table">
                        <thead>
                            <tr>
                                <th>
                                    Image
                                </th>  
                                <th>
                                    UPC
                                </th>
                                <th>
                                    Title
                                </th>
                                <th>
                                    SKU
                                </th>
                                <th>
                                    Retail
                                </th>
                                <th>
                                    MAP
                                </th>
                                <th>
                                    Wholesale
                                </th>
                                <th>
                                    Price
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img width="50" class="product-image" src="https://app.trackstreet.com/uploads/images/products/<?php echo $product['upc_code']; ?>.jpg" />
                                    </td>   
                                    <td>
                                        <?php echo $product['upc_code']; ?>
                                    </td>
                                    <td>                                
                                        <?php echo $product['title']; ?> <?php if (intval($product['is_archived']) == 1): ?><b>[Archived]</b><?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo $product['sku']; ?>
                                    </td>
                                    <td>
                                        $<?php echo $product['retail_price']; ?>
                                    </td>
                                    <td>
                                        $<?php echo $product['price_floor']; ?>
                                    </td>
                                    <td>
                                        $<?php echo $product['wholesale_price']; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($product_price_data[$product['upc_code']])): ?>
                                            <?php if ($product_price_data[$product['upc_code']]['low'] == $product_price_data[$product['upc_code']]['high']): ?>
                                                $<?php echo $product_price_data[$product['upc_code']]['low']; ?>
                                            <?php else: ?>
                                                $<?php echo $product_price_data[$product['upc_code']]['low']; ?> - $<?php echo $product_price_data[$product['upc_code']]['high']; ?>
                                            <?php endif; ?>    
                                        <?php else: ?>
                                            N/A     
                                        <?php endif; ?>          
                                    </td>
                                </tr>
                            <?php endforeach; ?>        
                        </tbody>
                    </table> 
                    
                    <script type="text/javascript">
          
                    $(document).ready(function() {
    
                        var table = $('#merchant-products-table').DataTable({
                            "stateSave": true,
                            "order": [[ 2, "desc" ]],
                            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                            
                            "stateSaveCallback": function (settings, data) {
                                $.ajax({
                                    "url": "/catalog/products_table_state_save",
                                    "data": data,
                                    "dataType": "json",
                                    "type": "POST",
                                    "success": function () {}    
                                });
                            },
                            "stateLoadCallback": function (settings) {
                                var o;
                            
                                $.ajax({
                                  "url": "/catalog/products_table_state_load",
                                  "async": false,
                                  "dataType": "json",
                                  "success": function (json) {
                                    o = json;
                                  }
                                });
                             
                                return o;
                            },  
                            
                            "language": {
                                "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                            },          
                            // i = number of results info 
                            // f = search  
                            // dom: https://datatables.net/reference/option/dom
                            <?php if ($this->role_id == 2): ?>
                                "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>',
                            <?php else: ?>
                                "dom": 'R<"top"f<"clear">>rt<"bottom"ilp<"clear">>',
                            <?php endif; ?>        
                            "columnDefs": [ { "orderable": false, "targets": 0 } ]
                        });
    
                    });
    
                    </script>                    
                    
                <?php endif; ?>                       
            
            </div> 

        </div>
                
    </div>
</div> 

<script type="text/javascript">

function showTooltip(x, y, contents) {
    jQuery('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5
    }).appendTo("body").fadeIn(200);
}

$(document).ready(function() {
	
    if ($('#product-count-chart').length) {
    	
        //var series1 = [[0, 10], [1, 6], [2,3], [3, 8], [4, 5], [5, 13], [6, 8]];
        
        
        var series1 = [
            <?php 
                   
            $i = 0; 
            
            $last_month = FALSE;
            
            ?>           
            <?php foreach ($product_data_rows as $data_point): ?>
                <?php if (count($product_data_rows) > 45): ?>
                    <?php
                        
                    $current_month = date('M', strtotime($data_point['select_date']));
                    
                    ?>
                    <?php if ($current_month != $last_month): ?>  
                        [
                            "<?php echo date('M', strtotime($data_point['select_date'])); ?> <?php echo date('j', strtotime($data_point['select_date'])); ?>", 
                            <?php echo $data_point['product_count']; ?>
                        ],
                    <?php else: ?>
                        [
                             "<!-- <?php echo date('M', strtotime($data_point['select_date'])); ?> <br/><?php echo date('j', strtotime($data_point['select_date'])); ?> -->", 
                             <?php echo $data_point['product_count']; ?>
                         ],                        
                    <?php endif; ?>
                <?php else: ?>
                    [
                        "<?php echo date('M', strtotime($data_point['select_date'])); ?><br /><?php echo date('j', strtotime($data_point['select_date'])); ?>", 
                         <?php echo $data_point['product_count']; ?>
                    ],
                <?php endif; ?>    
                <?php 
                
                $i++;
                
                $last_month = $current_month;
                
                ?>
            <?php endforeach; ?>
        ];        
        
        
        /*
        var series1 = [
            <?php foreach ($product_data_rows as $data_row): ?>
                ["<?php echo date('M', strtotime($data_row['select_date'])); ?> <br/><?php echo date('j', strtotime($data_row['select_date'])); ?>", <?php echo $data_row['product_count']; ?>],
            <?php endforeach; ?>
        ];
        */
    
        var plot = $.plot($("#product-count-chart"),
            [ { data: series1,
                //label: "Series 1",
                //color: "#8cc152"
                color: "#00a0d1"
            },
            ],
            {
                canvas: false,
                series: {
                    bars: {
                        show: true,
                        fill: true,
                        lineWidth: 1,
                        fillColor: {
                            colors: [ 
                                { opacity: 0.5 },
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
                    tickDecimals: 0
                },
                xaxis: {
                	mode: "categories",
                    color: '#eee',
                    tickSize: 5
                }
            });
    
        var previousPoint = null;
        
        $("#product-count-chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));
    
            if(item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
    
                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
    
                    showTooltip(item.pageX, item.pageY, y + ' products listed');
                }
    
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
    
        });
    
        $("#product-count-chart").bind("plotclick", function (event, pos, item) {
            if (item) {
                plot.highlight(item.series, item.datapoint);
            }
        });

        $('#date_from').datepicker({
        	dateFormat: 'yy-mm-dd',
        	minDate:'-20y',
        	maxDate:'0d',
        	onSelect:function(dateText, e){
        		$('input[name=time_frame]:radio').each(function(){
        			$(this).attr('checked',false);
        		});
        	},
        	onClose: function(){
        		if(!is_date_range_valid()){
        			$(this).val('');
        			return false;
        		}
        		else
        		{
        			//elaborate_keyword();
        		}
        	}
        });
        
        $('#date_to').datepicker({
        	dateFormat: 'yy-mm-dd',
        	minDate:'-20y',
        	maxDate:'0d',
        	onSelect:function(dateText, e){
        		$('input[name=time_frame]:radio').each(function(){
        			$(this).attr('checked',false);
        		});
        	},
        	onClose: function(dateText, e){
        		if(!is_date_range_valid()){
        			$(this).val('');
        			return false;
        		}
        		else
        		{
        			//elaborate_keyword();
        		}
        	}
        });
        
        $("#date_from_a").click(function(){$('#date_from').click();});
        $("#date_to_a").click(function(){$('#date_to').click();});
        
    }
});
</script>

<?php echo $this->load->view('merchants/parts/interact_embed', '', TRUE); ?>