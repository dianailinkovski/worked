<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Violation Overview: <?php echo $product['title'] ;?></strong>
        </h3>       
    </div>
    
    <div class="panel-body">

        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview/violated_products">Violated Products</a> <i class="fa fa-angle-right"></i> <?php echo $product['title'] ;?>
        </div> 
        
        <div id="reports-top-area">
            <div id="report-save-options">
                <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                <div class="clear"></div>            
            </div>
            <div class="clear"></div>
        </div>            
    
					<?php if(isset($violations) && count($violations) > 0) { ?>

						<table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="violated-product-table">
							<thead>
								<tr>
									<th width="10%">Marketplace</th>
									<th width="15%">Merchant</th>
									<th width="7%">Date</th>
									<th width="7%">Time</th>
									<th width="9%">Wholesale</th>
									<th width="6%">Retail</th>
									<th width="6%">MAP</th>
									<th width="6%">Price</th>
									<th>Diff</th>
									<th width="8%">Violation</th>
									<th width="26%">URL</th>
								</tr>
							</thead>
							<tbody>
							<?php for($i=0, $n=sizeof($violations); $i<$n; $i++): ?>
							
							<?php

							$mName = $violations[$i]['original_name'];
							
							/*
								$merchant = $this->merchant_products_m->getMerchantDetailsBySellerId($violations[$i]['merchant_id'], $violations[$i]['marketplace']);
								if ( ! $merchant) {
									$mName = 'Unknown';
								}
								else
									$mName = empty($merchant['original_name']) ? $merchant['merchant_name'] : $merchant['original_name'];
							*/
							
							?>
								<tr>
									<td><?=marketplace_display_name($violations[$i]['marketplace']);?></td>
									<td><?php echo isset($violations[$i]['merchant_id']) ? '<a href="'.base_url().'violationoverview/violator_report/' . $violations[$i]['merchant_id'] . '">' . $mName . '</a>' : 'Unknown' ?></td>
									<td><?=date('m/d/Y', $violations[$i]['timestamp']);?></td>
									<td><?=date('h:i A', $violations[$i]['timestamp']);?></td>
									<td>$<?=number_format($violations[$i]['wholesale'], 2);?></td>
									<td>$<?=number_format($violations[$i]['retail'], 2);?></td>
									<td>$<?=number_format($violations[$i]['map'], 2);?></td>
									<td>$<?=number_format($violations[$i]['price'], 2);?></td>
									<td>
									    <?php if ($violations[$i]['map'] > $violations[$i]['price']): ?>
									        $<?php echo number_format($violations[$i]['map'] - $violations[$i]['price'], 2); ?>
									    <?php else: ?>
									        OK
									    <?php endif; ?>
									</td>
									<td>
									    <?php
										
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
									<td><a href="<?=$violations[$i]['url'];?>" target="_blank"><?=extractDomainByURL($violations[$i]['url']);?></a></td>
								</tr><?php
							endfor;?>
							</tbody>
						</table><?php
					}else{
					?>
						<p>
							<?=$this->config->item('no_record');?>
						</p>
				  <?php
					}
					?>
					
					<!--  
					<p>
					    Check Start Time: <?php echo $crawl_start; ?>
					    Check End Time: <?php echo $crawl_end; ?>
					    Start UNIX: <?php echo strtotime($crawl_start); ?>
					    End UNIX: <?php echo strtotime($crawl_end); ?>					    
				      UPC: <?php echo $product['upc_code']; ?>
					</p>
          -->
    </div>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#violated-product-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/violated-product-table-<?php echo $product['id']; ?>",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
              "url": "/ajax/table_state_load/violated-product-table-<?php echo $product['id']; ?>",
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
	
});

</script>

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>