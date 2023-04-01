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
            <i class="fa fa-angle-right"></i> <a href="/violationoverview/violations_by_retailers">Violations by Retailer</a> 
            <i class="fa fa-angle-right"></i> <?= $report_name; ?>
        </div>    				
					
        <?php if (!empty($violations)): ?>
          
          <div id="filters">
            <form action="/violationoverview/violations_by_retailer/<?php echo $market_shortname; ?>" method="post">
            
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
                    </div>
                    <div class="clear"></div>
                    <div>
                        <?php echo form_checkbox('show_most_recent', 'yes', $show_most_recent); ?> Show Only Most Recent Violations
                    </div>
                </section>                
                
                <p>
                    <input class="btn btn-success" type="submit" value="Search Violations" />
                </p>
            </form>
          </div>  
          
					<div id="reports-top-area">
            <div id="report-save-options">
                <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                <div class="clear"></div>            
            </div>
            <div class="clear"></div>
          </div>                   
										
					<table class="table table-bordered table-striped table-success table-responsive reportTable exportable" id="violations-by-retailer-table">
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
                        
                            /*
                            // DEPRECATED CODE:
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
                            */
                        
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
                No records were found.
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
    
    $("#date_from_a").click(function(){$('#date_from').click();});
    $("#date_to_a").click(function(){$('#date_to').click();});		

    var table = $('#violations-by-retailer-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/violations-by-retailer-table-<?php echo $market['id']; ?>",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
              "url": "/ajax/table_state_load/violations-by-retailer-table-<?php echo $market['id']; ?>",
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

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>   
    
