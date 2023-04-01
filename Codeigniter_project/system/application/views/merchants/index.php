<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Merchants</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> Market Visibility
            <i class="fa fa-angle-right"></i> <a href="/merchants">Merchant Info</a>
        </div>   
        
        <?php if ($this->success_msg != ''): ?>
            <div class="alert alert-success" role="alert">
            	<?php echo $this->success_msg; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($this->error_msg != ''): ?>
        	<div class="alert alert-danger" role="alert">
        		<?php echo $this->error_msg; ?>
        	</div>
        <?php endif; ?> 
    
        <!-- merchant_type: <?php echo $merchant_type; ?> -->
    
        <form action="/merchants" method="post" id="merchants-form">
            <section class="clear select_report">
    					<fieldset>
    						<div class="leftCol"><label>Display</label></div>
    						<div class="rightCol">
    							<div class="inputContainer reports_radio">
    							  <input type="radio" id="x" value="all" name="merchant_type" class="radio" <?php if ($merchant_type == 'all'): ?>checked="checked"<?php endif; ?> />
    								<label for="x">All</label>
    								<input type="radio" id="y" value="merchants" name="merchant_type" class="radio" <?php if ($merchant_type == 'merchants'): ?>checked="checked"<?php endif; ?> />
    								<label for="y">Marketplace Merchants</label>
    								<input type="radio" id="z" value="retailers" name="merchant_type" class="radio" <?php if ($merchant_type == 'retailers'): ?>checked="checked"<?php endif; ?> />
    								<label for="z">Websites</label>
    								
    							</div>
    						</div>
    					</fieldset>
    				</section>  
    				
    				<section id="date_range" class="clear select_report">
                <div class="leftCol">
                    <label>Date Range</label>
                </div>
                <div class="rightCol">
                    <div class="inputContainer reports_radio">
                        <input type="radio" value="24" id="tf24" name="time_frame" <?php if ($time_frame == 24): ?>checked="checked"<?php endif; ?> />
                        <label for="tf24">Last 24 Hours</label>
                        <input type="radio" value="7" id="tf7" name="time_frame" <?php if ($time_frame == 7): ?>checked="checked"<?php endif; ?> />
                        <label for="tf7">Last 7 Days</label>
                        <input type="radio" value="30" id="tf30" name="time_frame" <?php if ($time_frame == 30): ?>checked="checked"<?php endif; ?> />
                        <label for="tf30">Last 30 Days</label>
                    </div>
                    <div class="inputContainer inputDivider"><b>or</b></div>
                    <div class="inputContainer">
                        <input max="<?= date('Y-m-d'); ?>" name="date_from" id="date_from" value="<?php echo $date_from; ?>" class="start dateInput">
                        <img width="24" height="24" class="imgIcon" id="date_from_a" alt="Start Date" src="/images/icons/24/50.png">
                    </div>
                    <div class="inputContainer">
                        <input max="<?= date('Y-m-d'); ?>" name="date_to" id="date_to" value="<?php echo $date_to; ?>" class="start dateInput">
                        <img width="24" height="24" class="imgIcon" id="date_to_a" alt="Stop Date" src="/images/icons/24/50.png">
                    </div>
                    <div class="inputContainer">
                        <input class="btn btn-success" type="submit" value="Search" />
                    </div>      
                </div>
            </section>
              
        </form>
         
        <?php if (!empty($merchants)): ?>
        
            <div id="reports-top-area">
                <div id="report-save-options">
                    <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                    <div class="clear"></div>            
                </div>
                <div class="clear"></div>
            </div>        
        
            <table class="table table-bordered table-striped table-success table-responsive reportTable exportable" id="merchants-table">
                <thead>
                    <tr>
                        <th>
							<input id="select-all" type="checkbox" />
                        </th>
						<th>Merchant Name</th>
                        <th>Website</th>
                        <th>Marketplace</th>
                        <th>Date Tracking Started</th>
                        <th># of Products Listed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($merchants as $merchant): ?>
                        <tr>
                            <td>
                                <input class="checkbox" name="select_merchant_id[]" value="<?php echo $merchant['id']; ?>" type="checkbox" />
                            </td>
							<td>
                                <a href="/merchants/profile/<?php echo $merchant['id']; ?>"><?php echo $merchant['human_name']; ?></a>
                            </td>
                            <td>
                                <?php if ($merchant['original_name'] == $merchant['marketplace'] || $merchant['seller_id'] == $merchant['marketplace']): ?>
                                    <a href="<?php echo $merchant['merchant_url']; ?>" target="_blank"><?php echo $merchant['merchant_url']; ?></a>
                                <?php else: ?>
                                    <a href="<?php echo $merchant['marketplace_url']; ?>" target="_blank"><?php echo ucfirst($merchant['marketplace']); ?> Seller Page</a>
                                <?php endif; ?>                        
                            </td>
                            <td>
                                <?php if ($merchant['original_name'] != $merchant['marketplace']): ?>
                                    <?php echo ucfirst($merchant['marketplace']); ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($merchant['tracking_started']) && $merchant['tracking_started'] != FALSE): ?>
                                    <?php echo date('m/d/Y', strtotime($merchant['tracking_started'])); ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>                             
                                <!--  
                                <?php if ($merchant['created_at'] != '0000-00-00 00:00:00'): ?>
                                    <?php echo date('m/d/Y', strtotime($merchant['created_at'])); ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?> 
                                -->   
                            </td> 
                            <td>
                                <?php echo $merchant['product_count']; ?>
                            </td>                                                                                                                                            
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table> 
            
            <script type="text/javascript">
            
            $(document).ready(function() {
            
                var table = $('#merchants-table').DataTable({
                    "stateSave": true,
                    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    "stateSaveCallback": function (settings, data) {
                        $.ajax({
                            "url": "/ajax/table_state_save/merchants-table",
                            "data": data,
                            "dataType": "json",
                            "type": "POST",
                            "success": function () {}    
                        });
                    },
                    "stateLoadCallback": function (settings) {
                        var o;
                    
                        $.ajax({
                          "url": "/ajax/table_state_load/merchants-table",
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
				
				$("div.bulk-actions").html('<?php echo $this->load->view('merchants/parts/merchants_bulk_actions_select'); ?>');
	
            	$('#merchants-bulk-action-select').change(function(){
					//$('#merchants-form').action("/merchants/bulk_action");
					//$('#merchants-form').submit();
				});

            });
            
            </script>            
            
        <?php else: ?>
            <p>
                No merchant data was found with the given search criteria.
            </p>    
        <?php endif; ?>            
    
    </div>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

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
    
    $("#date_from_a").click(function(){
        $('#date_from').click();
    });
    
    $("#date_to_a").click(function(){
        $('#date_to').click();
    });

});

</script>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script src="//interact.juststicky.com/js/embed/client.js/2028/interact_5627db6ad2782" id="interact_5627db6ad2782" data-text="Discuss this with Sticky Interact" data-unique="5627db6ad2782"></script>
<?php endif; ?>