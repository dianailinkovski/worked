<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Price Violators</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> <i class="fa fa-angle-right"></i> Violation Reports <i class="fa fa-angle-right"></i> <a href="/violationoverview/price_violators">Price Violators</a>
        </div>
        
        <div id="reports-top-area">
            <div id="report-save-options">
                <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                <div class="clear"></div>            
            </div>
            <div class="clear"></div>
        </div> 

        <?php if (count($priceViolators) > 0): ?>
            <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="price-violators-table">
                <thead>
                    <tr>
                        <th class="overviewtitleTh">Merchant</th>
                        <th class="overviewtitleTh">Location</th>
                        <th class="overviewtitleTh">Product<br />Violations</th>
                        <th class="overviewtitleTh">Notification<br />Level</th>
                        <th class="overviewtitleTh">Repeat Violator</th>
                        <th class="overviewtitleTh">Last Violation<br />Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($priceViolators as $key => $data) : ?>
                    <?php 
                        $data2 = $data['crowl_merchant'];
                        if ( !isset($data2['merchant_name']) ) continue;
                        
                        $name = (!empty($data2['original_name']) && $data2['original_name'] != NULL) ? $data2['original_name'] : $data2['merchant_name'];
                        ?>
                        <tr>
                            <td>
                                <a href="<?= base_url() . 'violationoverview/violator_report/' . $data2['id']; ?>"><?php echo $name; ?></a>
                            </td>
                            <td>
                                <?php echo (isset($data['crowl_merchant']['merchant_url']) ? $data['crowl_merchant']['merchant_url']:"") ?>
                            </td>
                            <td>
                                <?php echo (isset($data['total_violations']) ? $data['total_violations']:"") ?>
                            </td>
                            <td>
                                <?php echo (isset($data['violation_status']) ? $data['violation_status']:"") ?>
                            </td>
                            <td>
                                <?php echo (isset($data['repeat_vioaltor']) ? $data['repeat_vioaltor']:"") ?>
                            </td>
                            <td>
                                <?php echo (isset($data['last_violator']) ? $data['last_violator']:"") ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found.</p>
        <?php endif; ?>
        
    </div>
</div>  

<script type="text/javascript">
      
$(document).ready(function() {

    $('#price-violators-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 100, -1], [10, 100, "All"]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/price-violators-table",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
              "url": "/ajax/table_state_load/price-violators-table",
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