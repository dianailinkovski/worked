<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Violations by Retailers</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> <i class="fa fa-angle-right"></i> Violation Reports <i class="fa fa-angle-right"></i> <a href="/violationoverview/violations_by_retailers">Violations by Retailers</a>
        </div>
        
        <div id="reports-top-area">
            <div id="report-save-options">
                <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                <div class="clear"></div>            
            </div>
            <div class="clear"></div>
        </div>        
    
        <?php if (!empty($violatedRetailers)): ?>
            <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="violations-by-retailers">
                <thead>
                    <tr>
                        <th width="40%">Retailer</th>
                        <th width="20%">Products</th>
                        <th width="20%">Violations</th>
                        <th width="20%">Last Tracking</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($retailers as $data): ?>
                        
                        <?php $marketIndex = strtolower($data['marketplace']); ?>
                        
                        <?php if (!empty($violatedRetailers[$marketIndex])): ?>
                            <?php $crawl_info = !empty($last_crawl[$marketIndex]) ? $last_crawl[$marketIndex] : FALSE; ?>
                            <?php $crawl_start = !empty($crawl_info->start_datetime) ? date('m/d/Y g:i a', strtotime($crawl_info->start_datetime)) : ''; ?>   
                            <tr>
                                <td>
                                    <!-- old: <a href="<?php echo base_url() . 'violationoverview/report_marketplace/' . $marketIndex; ?>"><?php echo $data['display_name'] ?></a> -->
                                    <a href="/violationoverview/violations_by_retailer/<?php echo $marketIndex; ?>"><?php echo $data['display_name'] ?></a>
                                </td>
                                <td>
                                    <?php echo number_format($data['total_products']); ?>
                                </td>
                                <td>
                                    <?php echo (isset($market_violations[$marketIndex])) ? $market_violations[$marketIndex] : 0; ?>
                                </td>
                                <td>
                                    <?php echo $crawl_start ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                            
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No retailer violations found.</p>
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

<?php echo $this->load->view('violationoverview/components/interact_widget_script', '', TRUE); ?>   