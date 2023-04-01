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
            <i class="fa fa-angle-right"></i> <a href="/violationoverview/violations_by_marketplace">Violations by Marketplace</a>
            <i class="fa fa-angle-right"></i> <?= $report_name; ?>
        </div>		
        
        <!-- <?php echo $merchant_ids_str; ?> -->		
        
        <!-- 
        
        <?php echo nl2br($violation_query); ?>
        
        -->

				<?php if (isset($violations) && count($violations) > 0): ?>
					
            <div id="reports-top-area">
                <div id="report-save-options">
                    <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                    <div class="clear"></div>            
                </div>    
                <div class="clear"></div>
            </div> 
					
            <table class="table table-bordered table-striped table-success table-responsive reportTable exportable" id="price-violators-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Merchant</th>
                        <th>UPC</th>
                        <th>Date / Time</th>
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
                        	      <a href="/merchants/profile/<?php echo $violations[$i]['merchant']['id']; ?>"><?php echo $mName; ?></a>
                        	  </td>
                            <td>
                                <?=ucfirst($violations[$i]['upc_code']);?>
                            </td>
                            <td>
                                <?=date('m/d/Y', $violations[$i]['timestamp']);?><br />
                                <?=date('h:i A', $violations[$i]['timestamp']);?>
                            </td>
                            <td>
                                $<?=isset($violations[$i]['wholesale']) ? number_format($violations[$i]['wholesale'], 2) : '';?>
                            </td>
                            <td>
                                $<?=$violations[$i]['retail'] ? number_format($violations[$i]['retail'], 2) : '';?>
                            </td>
                            <td>
                                <?php if (!empty($violations[$i]['map']) && $violations[$i]['map'] != 0 && $violations[$i]['map'] != 0.00):?>
                                    $<?=number_format($violations[$i]['map'], 2);?>
                                <?php endif;?>
                            </td>
                        		<td>
                        		    $<?=number_format($violations[$i]['price'], 2);?>
                        		</td>
                        	  <td>
                                <?php
                                
                                $violation = '<img src="'.frontImageUrl().'icons/arrow-orange-down.png" alt="MAP Violation"> LOW';
                                
                                // for non-violation reports we - don't really have the shot value from the violations table
                                $shot = get_violation_image($violations[$i]);
                                
                                if (!empty($shot))
                                {
                                	$violation = '<a href="' . $shot . '" target="_blank">' . $violation . '</a>';
                                }
                                
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
            
            <script type="text/javascript">
                  
            $(document).ready(function() {
            
                var table = $('#price-violators-table').DataTable({
                    "stateSave": true,
                    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    "stateSaveCallback": function (settings, data) {
                        $.ajax({
                            "url": "/ajax/table_state_save/report-marketplace-<?php echo $market_name; ?>",
                            "data": data,
                            "dataType": "json",
                            "type": "POST",
                            "success": function () {}    
                        });
                    },
                    "stateLoadCallback": function (settings) {
                        var o;
                    
                        $.ajax({
                          "url": "/ajax/table_state_load/report-marketplace-<?php echo $market_name; ?>",
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
            
        <?php else: ?>
            <p>
                No records were found.
            </p>
        <?php endif; ?>
        
        <!--  
        <p>
            Start: <?php echo $crawl_start; ?><br />
            End: <?php echo $crawl_end; ?>
        </p>  
        --> 

    </div>
</div>	

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>