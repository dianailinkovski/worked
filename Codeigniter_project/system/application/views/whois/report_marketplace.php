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
            <i class="fa fa-angle-right"></i> <a href="<?php echo base_url().'whois'; ?>" id="breadMarket">Marketplaces</a>
						<i class="fa fa-angle-right"></i> <?php echo marketplace_display_name($marketplace); ?>
        </div>

				<?php if (count($merchantList) > 0): ?>
					
            <div id="repChartContainer"></div>
	
            <div id="reports-top-area">
                <div id="report-save-options">
                    <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                    <div class="clear"></div>            
                </div>
                <div class="clear"></div>
            </div> 

            <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="who-is-selling-marketplace-table">
                <thead>
							      <tr class="row_title">
								        <th class="header" align="left" width="50%">Merchant</th>
								        <th class="header" align="left" width="50%">Merchant Profile Page</th>
								        <th class="header" align="left" width="50%">Products</th>
							      </tr>
						    </thead>
						    <tbody>
    						    <?php foreach ($merchantList as $key => $data): ?>
    							      <tr>
    								        <td>
    								            <a href="<?php echo base_url().'whois/report_merchant/'.$marketplace.'/'.$data['id'];?>" class="merchant-name"><?php echo ucfirst($data['original_name']); ?></a>
    								        </td>
    								        <td>
    								            <a target="_blank" href="<?php echo $data['marketplace_seller_url']; ?>">View Profile on Marketplace</a>
    								        </td>
    								        <td>
    								            <?php echo number_format($data['total_products']); ?>
    								        </td>
    							      </tr>
    							 <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
				    <p>
					      No records found.
					  </p>
				<?php endif; ?>

    </div>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#who-is-selling-marketplace-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/who-is-selling-marketplace-table-<?php echo $marketplace; ?>",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
                "url": "/ajax/table_state_load/who-is-selling-marketplace-table-<?php echo $marketplace; ?>",
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