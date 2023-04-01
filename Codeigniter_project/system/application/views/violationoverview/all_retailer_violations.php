<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong><?= $report_name; ?></strong>
        </h3>        
    </div>
    
    <div class="panel-body">	
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> Violation Reports 
            <i class="fa fa-angle-right"></i> All Current Retailer Violations
        </div>    				
				
        <?php if (isset($violations) && count($violations) > 0): ?>
					
            <div id="reports-top-area">
                <div id="report-save-options">
                    <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                    <div class="clear"></div>            
                </div>
                <div class="clear"></div>
            </div> 
						
            <table class="table table-bordered table-striped table-success table-responsive" id="all-retailer-violations-table">
                <thead>
                    <tr>
                        <th>Site Title</th>
                        <th>Catalog Title</th>
                        <th>Merchant</th>
                        <th>UPC</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Whsle</th>
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
                        <?php $mName = marketplace_display_name($violations[$i]['original_name']); ?>
                        <tr>
                            <td>
                                <?=ucfirst($violations[$i]['title']);?>
                            </td>
                            <td>
                                <?=ucfirst($violations[$i]['title2']);?>
                            </td>
                            <td>
                                <a href="/merchants/profile/<?php echo $violations[$i]['cmn_id']; ?>"><?= $mName; ?></a>
                            </td>
                            <td>
                                <?=ucfirst($violations[$i]['upc_code']); ?>
                            </td>
                            <td>
                                <?=date('m/d/Y', $violations[$i]['timestamp']); ?>
                            </td>
                            <td>
                                <?=date('h:i A', $violations[$i]['timestamp']); ?>
                            </td>
                            <td>
                                $<?=isset($violations[$i]['wholesale']) ? number_format($violations[$i]['wholesale'], 2) : ''; ?>
                            </td>
                            <td>
                                $<?=$violations[$i]['retail'] ? number_format($violations[$i]['retail'], 2) : ''; ?>
                            </td>
                            <td>
                                <?php if (!empty($violations[$i]['map']) && $violations[$i]['map'] != 0 && $violations[$i]['map'] != 0.00):?>
                                    $<?=number_format($violations[$i]['map'], 2);?>
                                <?php endif;?>
                            </td>
                            <td>
                                $<?=number_format($violations[$i]['price'], 2); ?>
                            </td>
                            <td>
                                <?php
                                    $violation = '<img src="' . frontImageUrl() . 'icons/arrow-orange-down.png" alt="MAP Violation"> LOW';
                                    if ( strlen($violations[$i]['shot']) > 0 ) {
                                        $full_path = 'stickyvision/violations/' . $violations[$i]['shot'];
                                        $full_url = $this->config->item('s3_cname') . $full_path;
                                        if (@fopen($full_url, 'r')) {
                                            $shot = $full_url;
                                        }
                                    } else {
                                        //for non-violation reports we - don't really have the shot value from the violations table
                                        $shot = get_violation_image($violations[$i]);
                                    }
                                    if (!empty($shot))
                                        $violation = '<a href="' . $shot . '" target="_blank">' . $violation . '</a>';
                                    echo $violation;
                                ?>
                            </td>
                            <td>
                                <a href="<?=$violations[$i]['url'];?>" target="_blank"><img src="<?=frontImageUrl()?>icons/link.png"></a>
                            </td>
                        </tr>
                    <?php endfor;?>
                </tbody>
            </table>
					
            <!--  
            <p>
                Check Start Time: <?php echo $crawl_start; ?> <br /> 
                Check End Time: <?php echo $crawl_end; ?>
            </p>	
            -->						
      
            <script type="text/javascript">
                  
            $(document).ready(function() {
            
                var table = $('#all-retailer-violations-table').DataTable({
                    "stateSave": true,
                    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    "stateSaveCallback": function (settings, data) {
                        $.ajax({
                            "url": "/ajax/table_state_save/all-retailer-violations-table",
                            "data": data,
                            "dataType": "json",
                            "type": "POST",
                            "success": function () {}    
                        });
                    },
                    "stateLoadCallback": function (settings) {
                        var o;
                    
                        $.ajax({
                          "url": "/ajax/table_state_load/all-retailer-violations-table",
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
            	
            });
            
            </script>      
                    

          <table style="display:none;" cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable exportable" style="font-size: 11px;line-height: 1.3em;">
						<thead>
							<tr>
								<th>Product Name</th>
								<th>Merchant</th>
								<th>UPC</th>
								<th>Date</th>
								<th>Time</th>
								<th>Whsle</th>
								<th>Retail</th>
								<th>MAP</th>
								<th>Price</th>
							</tr>
						</thead>
						<tbody>
              <?php for ($i = 0, $n = sizeof($violations); $i < $n; $i++): ?>
                <tr>
									<td><?=ucfirst($violations[$i]['title']);?></td>
									<td><?= $_mNames[$i]?></td>
									<td><?=ucfirst($violations[$i]['upc_code']);?></td>
									<td><?=date('m/d/Y', $violations[$i]['timestamp']);?></td>
									<td><?=date('h:i A', $violations[$i]['timestamp']);?></td>
									<td>$<?=$violations[$i]['wholesale'] ? number_format($violations[$i]['wholesale'], 2) : '';?></td>
									<td>$<?=$violations[$i]['retail'] ? number_format($violations[$i]['retail'], 2) : '';?></td>
									<td>
                                    <?php if (!empty($violations[$i]['map']) && $violations[$i]['map'] != 0 && $violations[$i]['map'] != 0.00):?>
                                        $<?=number_format($violations[$i]['map'], 2);?>
                                    <?php endif;?>
                                    </td>
									<td>
									    $<?php echo number_format($violations[$i]['price'], 2); ?>
									</td>
								</tr>
              <?php endfor;?>
						</tbody>
					</table>
					
        <?php else: ?>
        
            <p>
                No records found.
            </p>
            
        <?php endif; ?>			

    </div>
</div>

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>