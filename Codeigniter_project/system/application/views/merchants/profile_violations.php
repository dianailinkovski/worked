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
        
        <!-- 
        
        StoreID: <?php echo $store_id; ?>
        
         --> 
        
        <h3 class="large-subtitle">
            <?php echo $merchant_profile_name; ?>
        </h3>
        
        <ul class="nav nav-tabs" style="margin-bottom: 20px;">
            <li>
                <a href="/merchants/profile/<?php echo $merchant_id; ?>">Basics</a>
            </li>
            <li>
                <a href="/merchants/profile_products/<?php echo $merchant_id; ?>">Products</a>
            </li>
            <li class="active">
                <a href="/merchants/profile_violations/<?php echo $merchant_id; ?>">Violations</a>
            </li>
        </ul>         
        
        <div id="merchant-details" style="margin: 0px 0px 20px 0px;">
        
            <h3 class="mp-subtitle">
                Overview
            </h3>
            
            <div class="row">
                <div class="col-xs-12">
                    <div class="merchant-detail-lists">
                    
                        <dl class="dl-horizontal" style="width:50%;">
                            <dt>
                                Current Violation Status:
                            </dt>
                            <dd>
                                <?php echo $violation_status; ?> &nbsp;<a id="change-level-button" data-modal-url="/merchants/change_level_modal/<?php echo $merchant_id; ?>" href="#">[Change]</a>
                            </dd>                
                            <dt>
                                Last Violation Date:
                            </dt>
                            <dd>
                                <?php echo $last_violation_date; ?>
                            </dd>
                        </dl>    
                        <dl class="dl-horizontal" style="width:50%;">    
                            <dt>
                                # of Current Violations:
                            </dt>
                            <dd>
                                <?php echo $current_violation_count; ?>
                            </dd>
                            <dt>
                                Last Notice Sent:
                            </dt>
                            <dd>
                                <?php echo $last_notice_date; ?>
                            </dd>
                        </dl>
                        
                        <div class="clear"></div>
                                   
                    </div> 
                </div>
            </div>
                    
        </div>
        
        <div id="violation-count-chart-area">
            <h3 class="mp-subtitle">
                Product Violations
            </h3>
            <div id="flot-basic-chart" <?php if (empty($viol_data_rows)): ?>style="display:none;"<?php endif; ?>>
                
            </div>
            <?php if (empty($viol_data_rows)): ?>
                <p>
                    Violation stats are not available on this merchant.
                </p>
            <?php endif; ?>
        </div>
        
        <div id="mp-violations">
        
            <h3 class="mp-subtitle">
                Violation History
            </h3>
            
            <div class="filters">
                <form action="/merchants/profile_violations/<?php echo $merchant_id; ?>" method="post">
                
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
                                <input class="btn btn-success" type="submit" value="Search Violation History" />
                            </div>
                        </div>
                        <div class="clear"></div>
                    </section>
                    
                </form>
            </div>             
        
            <?php if (!empty($violations)): ?>
                            
    					<div id="reports-top-area">
                <div id="report-save-options">
                    <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                    <div class="clear"></div>            
                </div>
                <div class="clear"></div>
              </div>                   
    										
    					<table class="table table-bordered table-striped table-success table-responsive reportTable exportable" id="mp-violations-by-merchant-table-<?php echo $merchant_id; ?>">
    						<thead>
    							<tr>
    								<th>Product</th>
    								<th>UPC</th>
    								<th>Date</th>
    								<th>Time</th>
    								<th>Wholesale</th>
    								<th>Retail</th>
    								<th>MAP</th>
    								<th>Price</th>
    								<th>Violation</th>
    								<th>URL</th>
    							</tr>
    						</thead>
    						<tbody>
                    <?php $_mNames = array(); ?>
                    <?php for ($i = 0, $n = sizeof($violations); $i < $n; $i++): ?>
        								<tr>
        									<td><?=ucfirst($violations[$i]['title']);?></td>
        									<td><?=ucfirst($violations[$i]['upc_code']);?></td>
        									<td><?=date('m/d/Y', $violations[$i]['timestamp']);?></td>
        									<td><?=date('h:i A', $violations[$i]['timestamp']);?></td>
        									<td>$<?=isset($violations[$i]['wholesale']) ? number_format($violations[$i]['wholesale'], 2) : '';?></td>
        									<td>$<?=$violations[$i]['retail'] ? number_format($violations[$i]['retail'], 2) : '';?></td>
        									<td>
                            <?php if (!empty($violations[$i]['map']) && $violations[$i]['map'] != 0 && $violations[$i]['map'] != 0.00):?>
                                $<?=number_format($violations[$i]['map'], 2);?>
                            <?php endif;?>
                          </td>
        									<td>$<?=number_format($violations[$i]['price'], 2);?></td>
        									<td>
                            <?php
                            
                            // NEW CODE - Christophe
                            $violation = '<img src="'.frontImageUrl().'icons/arrow-orange-down.png" alt="MAP Violation"> LOW';
                            
                            //for non-violation reports we - don't really have the shot value from the violations table
                            $shot = get_violation_image($violations[$i]);
                            
                            if (!empty($shot))
                            {
                            	$violation = '<a href="' . $shot . '" target="_blank">' . $violation . '</a>';
                            }
                            
                            echo $violation;
                            
                            ?>
        									</td>
        									<td>
        									    <?php 
        									    
        									    // for some reason some URLs missing colon - Christophe
        									    $violations[$i]['url'] = str_replace('http://https://', 'https://', $violations[$i]['url']);
        									    
        									    ?>
        									    <a href="<?php echo $violations[$i]['url'];?>" target="_blank"><img src="<?=frontImageUrl()?>icons/link.png"></a>
        									</td>
        								</tr>
                    <?php endfor;?>
    						</tbody>
    					</table>
    					
            <?php else: ?>
                <p>
                    No violation records found for specified date range.
                </p>
            <?php endif; ?>        
        
        <!-- end #mp-violations -->
        </div>
        
        <div id="mp-violations">
        
            <h3 class="mp-subtitle">
                Notice History
            </h3>
            
            <?php if (!empty($notices)): ?>
                <table class="table table-bordered table-striped table-success table-responsive exportable" id="sent-notices-table-<?php echo $merchant_id; ?>">
                    <thead>
                        <tr>
                    	      <th align="left">Date</th>  
                    		    <th align="left">Sent To</th>
                    		    <th align="left">Level</th>
                    		    <th align="left">Title</th>
                    		    <th align="left">Message</th>
                        </tr>
                    </thead>
    							  <tbody>
                        <?php foreach ($notices as $notice): ?>
                            <tr>
                                <td>
                                    <?php echo date('M j, Y', strtotime($notice['date'])); ?>
                                </td>
                                <td>
                                    <?php if ($notice['email_to'] == ''): ?>
                                        Sent to Marketplace Seller
                                    <?php else: ?>
                                        <?php echo $notice['email_to']; ?>
                                    <?php endif; ?>    
                                </td>
                                <td>
                                    <?php echo $notice['email_level']; ?>
                                </td>    
                                <td>
                                    <?php echo $notice['title']; ?>
                                </td>
                                <td>
                                
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#message-<?php echo $notice['id']; ?>">
                                      View
                                    </button>
                                    
                                    <div class="modal fade" id="message-<?php echo $notice['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                      <div class="modal-dialog">
                                        <div class="modal-content">
                                    
                                          <div class="modal-header">
                                            <div class="hidden-xs">
                                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                              <h4 class="modal-title" id="myModalLabel">Notice: <?php echo $notice['title']; ?></h4>
                                            </div>
                                            <div class="hidden-sm hidden-md hidden-lg sml-header">
                                              <button type="button" class="close sml-txt" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                              <h4 class="modal-title sml-txt" id="myModalLabel">Notice: <?php echo $notice['title']; ?></h4>
                                            </div>
                                          </div>
                                          
                                          <div class="modal-body">
                                              <?php if ($notice['full_message'] != ''): ?>
                                                  <iframe src="/violationoverview/sent_notice_message/<?php echo $notice['id']; ?>" width="100%" height="600"></iframe>
                                              <?php else: ?>
                                                  <div class="alert alert-warning" role="alert">
                                                      With older notifications, we could only log a portion of the full message that was sent to the violator. 
                                                      A full message was sent even though a partial version is only shown here.
                                                  </div>
                                                  <div class="modal-body" style="overflow-y: scroll; height: 400px;">
                                                      <?php echo nl2br($notice['regarding']); ?>
                                                  </div>
                                              <?php endif; ?>
                                          </div>
                                          
                                        </div>
                                      </div>
                                    </div>
                                    
                                </td>
                            </tr>
        								<?php endforeach; ?>
    						    </tbody>
    						</table>
    						
                <script type="text/javascript">
                      
                $(document).ready(function() {   						
                	
                    var table = $('#sent-notices-table').DataTable({
                        "stateSave": true,
                        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                        "order": [[ 0, "desc" ]],
                        "stateSaveCallback": function (settings, data) {
                            $.ajax({
                                "url": "/ajax/table_state_save/sent-notices-table-<?php echo $merchant_id; ?>",
                                "data": data,
                                "dataType": "json",
                                "type": "POST",
                                "success": function () {}    
                            });
                        },
                        "stateLoadCallback": function (settings) {
                            var o;
                        
                            $.ajax({
                              "url": "/ajax/table_state_load/sent-notices-table-<?php echo $merchant_id; ?>",
                              "async": false,
                              "dataType": "json",
                              "success": function (json) {
                                o = json;
                              }
                            });
                         
                            return o;
                        },  
                        "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>'
                    });    						
                 
                });
                
                </script>    						
    						
            <?php else: ?>
                <p>
                    No notices have been sent yet to this merchant.
                </p>
            <?php endif; ?>            
            
        </div>
        
        <div id="mp-change-history-area">
   
            <h3 class="mp-subtitle">
                Change History
            </h3>
            
            <?php if (!empty($history_changes)): ?>
                <table class="table table-bordered table-striped table-success table-responsive exportable" id="merchant-change-history-table-<?php echo $merchant_id; ?>">
                    <thead>
                        <tr>
                    	      <th align="left">Date</th>  
                    		    <th align="left">Made By</th>
                    		    <th align="left">Details</th>
                        </tr>
                    </thead>
    							  <tbody>
                    <?php foreach ($history_changes as $change): ?>
                        <tr>
                            <td>
                                <?php echo date('M j, Y', strtotime($change['created'])); ?><br />
                                <?php echo date('g:i A', strtotime($change['created'])); ?>
                            </td>
                            <td>
                                <?php if (empty($change['user'])): ?>
                                    TrackStreet
                                <?php else: ?>
                                    <?php echo $change['user']['first_name']; ?> <?php echo $change['user']['last_name']; ?>
                                <?php endif; ?>    
                            </td>
                            <td>
                                <p>
                                    <b><?php echo $change['action_title']; ?></b>
                                </p>
                                <?php if ($change['action_text'] != ''): ?>
                                    <p>
                                        Note: <?php echo $change['action_text']; ?>
                                    </p>
                                <?php endif; ?>            
                            </td>
                        </tr>
    								<?php endforeach; ?>
    							</tbody>
    						</table>
    						
                <script type="text/javascript">
                      
                $(document).ready(function() {   						
                	
                    var table = $('#sent-notices-table').DataTable({
                        "stateSave": true,
                        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                        "order": [[ 0, "desc" ]],
                        "stateSaveCallback": function (settings, data) {
                            $.ajax({
                                "url": "/ajax/table_state_save/merchant-change-history-table-<?php echo $merchant_id; ?>",
                                "data": data,
                                "dataType": "json",
                                "type": "POST",
                                "success": function () {}    
                            });
                        },
                        "stateLoadCallback": function (settings) {
                            var o;
                        
                            $.ajax({
                              "url": "/ajax/table_state_load/merchant-change-history-table-<?php echo $merchant_id; ?>",
                              "async": false,
                              "dataType": "json",
                              "success": function (json) {
                                o = json;
                              }
                            });
                         
                            return o;
                        },  
                        "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>'
                    });    						
                 
                });
                
                </script>    						
    						
            <?php else: ?>
                <p>
                    No change history is available for this merchant.
                </p>
            <?php endif; ?>   
        
        </div>
        
    </div>
</div> 

<!-- start modal -->
<div class="modal fade" id="dynamic-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <div class="hidden-xs">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title" id="myModalLabel">Modal title</h4>
        </div>
        <div class="hidden-sm hidden-md hidden-lg sml-header">
          <button type="button" class="close sml-txt" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
          <h4 class="modal-title sml-txt" id="myModalLabel">Modal title</h4>
        </div>
      </div>
      
      <div class="modal-body">
          <iframe width="100%" height="100%" style="width: 100%; height: 100%;" id="modal-iframe" src=""></iframe>   
      </div>
      
    </div>
  </div>
</div>
<!-- end modal -->

<script type="text/javascript">

var data_changed = false;
                
function set_data_change_to_true()
{
    // if data on page was changed via modal
    data_changed = true;
}

function showTooltip(x, y, contents) {
    jQuery('<div id="tooltip" class="tooltipflot">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 5,
        left: x + 5
    }).appendTo("body").fadeIn(200);
}

$(document).ready(function() {

    $('#dynamic-modal').on('hidden.bs.modal', function () {
        
        //alert(data_changed);
        
        if (data_changed)
        {
            window.location.reload(true);
        }
    });

    $('#change-level-button').click(function(){

        data_changed = false;

        $('#dynamic-modal').modal('show');

        var modal_iframe_src = $(this).attr('data-modal-url');

        $('#modal-iframe').attr('src', modal_iframe_src);

        $('.modal-title').html('Edit Violation Level');

        return false;
    	
    });	
	
    if ($('#flot-basic-chart').length) {
    	
        //var series1 = [[0, 10], [1, 6], [2,3], [3, 8], [4, 5], [5, 13], [6, 8]];
   
        var series1 = [
            <?php 
                   
            $i = 0; 
            
            $last_month = FALSE;
            
            ?>           
            <?php foreach ($viol_data_rows as $data_point): ?>
                <?php if (count($viol_data_rows) > 45): ?>
                    <?php
                        
                    $current_month = date('M', strtotime($data_point['select_date']));
                    
                    ?>
                    <?php if ($current_month != $last_month): ?>  
                        [
                            "<?php echo date('M', strtotime($data_point['select_date'])); ?> <?php echo date('j', strtotime($data_point['select_date'])); ?>", 
                            <?php echo $data_point['violation_count']; ?>
                        ],
                    <?php else: ?>
                        [
                             "<!-- <?php echo date('M', strtotime($data_point['select_date'])); ?> <br/><?php echo date('j', strtotime($data_point['select_date'])); ?> -->", 
                             <?php echo $data_point['violation_count']; ?>
                         ],                        
                    <?php endif; ?>
                <?php else: ?>
                    [
                        "<?php echo date('M', strtotime($data_point['select_date'])); ?><br /><?php echo date('j', strtotime($data_point['select_date'])); ?>", 
                         <?php echo $data_point['violation_count']; ?>
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
            <?php foreach ($viol_data_rows as $data_row): ?>
                ["<?php echo date('M', strtotime($data_row['select_date'])); ?> <br/><?php echo date('j', strtotime($data_row['select_date'])); ?>", <?php echo $data_row['violation_count']; ?>],
            <?php endforeach; ?>
        ];
        */
    
        var plot = $.plot($("#flot-basic-chart"),
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
                    tickDecimals: 0
                },
                xaxis: {
                	mode: "categories",
                    color: '#eee',
                    tickSize: 5
                }
            });
    
        var previousPoint = null;
        
        $("#flot-basic-chart").bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));
    
            if(item) {
                if (previousPoint != item.dataIndex) {
                    previousPoint = item.dataIndex;
    
                    $("#tooltip").remove();
                    var x = item.datapoint[0],
                        y = item.datapoint[1];
    
                    showTooltip(item.pageX, item.pageY, y + ' violations');
                }
    
            } else {
                $("#tooltip").remove();
                previousPoint = null;
            }
    
        });
    
        $("#flot-basic-chart").bind("plotclick", function (event, pos, item) {
            if (item) {
                plot.highlight(item.series, item.datapoint);
            }
        });
    }

    // --------- violations table
   
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

    <?php if (!empty($violations)): ?>
        
        var table = $('#mp-violations-by-merchant-table-<?php echo $merchant_id; ?>').DataTable({
            "stateSave": true,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "stateSaveCallback": function (settings, data) {
                $.ajax({
                    "url": "/ajax/table_state_save/mp-violations-by-merchant-table-<?php echo $merchant_id; ?>",
                    "data": data,
                    "dataType": "json",
                    "type": "POST",
                    "success": function () {}    
                });
            },
            "stateLoadCallback": function (settings) {
                var o;
            
                $.ajax({
                  "url": "/ajax/table_state_load/mp-violations-by-merchant-table-<?php echo $merchant_id; ?>",
                  "async": false,
                  "dataType": "json",
                  "success": function (json) {
                    o = json;
                  }
                });
             
                return o;
            },         
            // i = number of results info 
            // f = search  
            "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>'
        });
    
        // see http://www.datatables.net/extensions/fixedheader/options
        new $.fn.dataTable.FixedHeader(table, {
            "offsetTop": 55
        });    

    <?php endif; ?>        
  
});

</script>

<?php echo $this->load->view('merchants/parts/interact_embed', '', TRUE); ?>
        