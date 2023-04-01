<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Violations by Retailers</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> Violation Reports 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview/violations_by_retailers">Violations by Retailers</a>
        </div>
            
        <?php if (!empty($retailers)): ?>
        
            <div id="reports-top-area">
                <div id="report-save-options">
                    <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                    <div class="clear"></div>            
                </div>
                <div class="clear"></div>
            </div>        
        
            <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="violations-by-retailers">
                <thead>
                    <tr>
                        <th width="40%">Retailer</th>
                        <th width="20%">Products Violated</th>
                        <th width="20%">Last Tracking</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($retailers as $merchant_id => $data): ?>
                        <tr>
                            <td>
                                <a href="/merchants/profile/<?php echo $merchant_id; ?>"><?php echo $data['merchant_name']; ?></a>
                            </td>
                            <td>
                                <a href="/violationoverview/violations_by_retailer/<?php echo $data['merchant_name_short']; ?>"><?php echo $data['product_violation_count']; ?></a>
                            </td>
                            <td>
                                <?php echo date('m/d/Y g:i a', $data['last_track_date']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <script type="text/javascript">
                  
            $(document).ready(function() {
            
                $('#violations-by-retailers').DataTable({
                    "stateSave": true,
                    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]], 
                    "stateSaveCallback": function (settings, data) {
                        $.ajax({
                            "url": "/ajax/table_state_save/violations-by-retailers",
                            "data": data,
                            "dataType": "json",
                            "type": "POST",
                            "success": function () {}    
                        });
                    },
                    "stateLoadCallback": function (settings) {
                        var o;
                    
                        $.ajax({
                          "url": "/ajax/table_state_load/violations-by-retailers",
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
                No retailer violations found.
            </p>
            
        <?php endif; ?>
      
        <?php if (!empty($crawl_range)): ?>
            <!--  
            <p>
                Period Start: <?php echo $crawl_range['from']; ?> (<?php echo strtotime($crawl_range['from']); ?>)<br/>
                Period End: <?php echo $crawl_range['to']; ?> (<?php echo strtotime($crawl_range['to']); ?>)
            </p>
            -->
        <?php endif; ?>      
        
    </div>
</div>

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>   