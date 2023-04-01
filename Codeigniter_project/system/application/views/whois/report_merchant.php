<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong><?=$report_name;?></strong>
        </h3>        
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> Market Visibility 
            <i class="fa fa-angle-right"></i> Who's Selling My Products
            <i class="fa fa-angle-right"></i> <a href="<?php echo base_url().'whois';?><?=($retailer ? '/retailers' : '');?>"><?=($retailer ? 'Retailers' : 'Marketplace');?></a>
						<i class="fa fa-angle-right"></i> <a href="<?=base_url().'whois/report_marketplace/'. $marketplace;?>"><?php echo marketplace_display_name($marketplace) ?></a>
						<i class="fa fa-angle-right"></i> <?php if (!$retailer): ?><?=$merchant;?><?php endif; ?>
        </div>
								
        <?php if(!empty($Data)): ?>
        
            <div id="repChartContainer"></div>
        
    				<div id="reports-top-area">
                <div id="report-save-options">
                    <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                    <div class="clear"></div>            
                </div>
                <div class="clear"></div>
            </div>
                    
            <table class="table table-bordered table-striped table-success table-responsive exportable reportTable rptTable" id="who-is-selling-merchant-table">
    						<thead>
    							<tr>
    								<th>Site Title</th>
    								<th>Catalog Title</th>
    								<th>Date</th>
    								<th>Time</th>
    								<th>UPC</th>
    								<th>SKU</th>
    								<th>WHLS</th>
    								<th>Retail</th>
    								<th>MAP</th>
    								<th>Price</th>
    								<th>Violation</th>
    								<th>URL</th>
    							</tr>
    						</thead>
    						<tbody>						    
						        <?php foreach ($Data as $prodId => $data): ?>
						            <?php
						             
								        // temporary - for reporting only - this will correct itself with the crawl data
								        if (!isset($data['timestamp'])) 
								        {    
								            continue;
								        }
								        
							          ?>
                        <tr>
                        	<td><?=ucfirst($data['title']);?></td>
                        	<td><?=$data['title2'];?></td>
                        	<td><?=date('m/d/Y', $data['timestamp']);?></td>
                        	<td><?=date('h:i A', $data['timestamp']);?></td>
                        	<td><?=$data['upc_code'];?></td>
                        	<td><?=$data['sku'];?></td>
                        	<td>$<?=number_format($data['wholesale'], 2);?></td>
                        	<td>$<?=number_format($data['retail'], 2);?></td>
                        	<td><?php if(!empty($data['map']) && $data['map'] != 0 && $data['map'] != 0.00):?>$<?=number_format($data['map'], 2); endif;?></td>
                        	<td>$<?=number_format($data['price'], 2);?></td>
                        	<td><?php
                        	$violation = $image = '';
                        	if ($data['price'] < $data['map']) {
                        		$image = get_violation_image($data);
                        		if(trim($image)){
                        			$violation = '<a href="'.$image.'" target="_blank"><img src="'.frontImageUrl() . 'icons/arrow-orange-down.png" alt="" style="margin-right:5px;"/> LOW</a>';
                        		} else {
                        			$violation = '<img src="'.frontImageUrl() . 'icons/arrow-orange-down.png" alt="" style="margin-right:5px;"/> LOW';
                        		}
                        		echo $violation;
                        	} ?>
                        	</td>
                        	<td><a href="<?=$data['url'];?>" target="_blank"><img src="<?=frontImageUrl()?>icons/link.png"></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
            </table>
        <?php else: ?>
            <p>
				        No records found.
            </p>
				<?php endif; ?>
						
				<?php $this->load->view('components/violator_notifications_form', $this->data); ?>

    </div>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#who-is-selling-merchant-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/who-is-selling-merchant-table-<?php echo $merchant_id; ?>",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
              "url": "/ajax/table_state_load/who-is-selling-merchant-table-<?php echo $merchant_id; ?>",
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