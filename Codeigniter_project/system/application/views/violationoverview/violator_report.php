<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Violator Report: <?=$merchant['original_name'];?></strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> Violation Reports 
            <i class="fa fa-angle-right"></i> Violator Report
        </div>    
    
        <?php
						
        if (isset($violations) && count($violations) > 0):  
        
        ?>
						
						<div id="reports-top-area">
                <div id="report-save-options">
                    <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                    <div class="clear"></div>            
                </div>
                <div class="clear"></div>
            </div> 

						<table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="violator-report-table">
							<thead>
								<tr>
									<th align="left" width="11%">Product Name</th>
									<th align="left" width="9%">UPC</th>
									<th align="left" width="8%">Date</th>
									<th align="left" width="7%">Time</th>
									<th align="left" width="9%">Wholesale</th>
									<th align="left" width="6%">Retail</th>
									<th align="left" width="6%">MAP</th>
									<th align="left" width="6%">Price</th>
									<th align="left" width="8%">Violation</th>
									<th align="left" width="20%">URL</th>
								</tr>
							</thead>

							<tbody>
							<?php for($i=0, $n=sizeof($violations); $i<$n; $i++): ?>
								<tr>
									<td><?=ucfirst($violations[$i]['title']);?></td>
									<td><?=ucfirst($violations[$i]['upc_code']);?></td>
									<td><?=date('m/d/Y', strtotime($violations[$i]['date']));?></td>
									<td><?=date('h:i A', strtotime($violations[$i]['date']));?></td>
									<td>$<?=number_format($violations[$i]['wholesale'], 2);?></td>
									<td>$<?=number_format($violations[$i]['retail'], 2);?></td>
									<td>$<?=number_format($violations[$i]['map'], 2);?></td>
									<td>$<?=number_format($violations[$i]['price'], 2);?></td>
									<td><?php
										$violation = '<img src="'.frontImageUrl().'icons/arrow-orange-down.png" alt="MAP Violation"><br/>LOW';
										//for non-violation reports we - don't really have the shot value from the violations table
										$shot = get_violation_image($violations[$i]);
										if (!empty($shot)) $violation = '<a href="'.$shot.'" target="_blank">'.$violation.'</a>';
										echo $violation;
										?>
									</td>
									<td><a href="<?=$violations[$i]['url'];?>" target="_blank"><?=extractDomainByURL($violations[$i]['url']);?></a></td>
								</tr>
							<?php endfor; ?>
							</tbody>
						</table>
						
            <script type="text/javascript">
                  
            $(document).ready(function() {
            
                $('#violator-report-table').DataTable({
                    "stateSave": true,
                    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                    "stateSaveCallback": function (settings, data) {
                        $.ajax({
                            "url": "/ajax/table_state_save/violator-report-table",
                            "data": data,
                            "dataType": "json",
                            "type": "POST",
                            "success": function () {}    
                        });
                    },
                    "stateLoadCallback": function (settings) {
                        var o;
                    
                        $.ajax({
                          "url": "/ajax/table_state_load/violator-report-table",
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
						
        <?php else: ?>
        
            <p>
                No records are currently available.
            </p>
            
        <?php endif; ?>
        
    </div>
</div>

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>