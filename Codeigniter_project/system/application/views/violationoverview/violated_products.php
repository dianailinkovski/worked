<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Violated Products</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> Violation Reports 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview/violated_products">Violated Products</a>
        </div>
        
        <div id="reports-top-area">
            <div id="report-save-options">
                <?php echo $this->load->view('components/save_options', '', TRUE); ?>
                <div class="clear"></div>            
            </div>
            <div class="clear"></div>
        </div> 

        <?php if (count($violatedProducts) > 0): ?>
            <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="violated-products-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>UPC</th>
                        <th>Retail</th>
                        <th>Wholesale</th>
                        <th>MAP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($violatedProducts as $key => $data): ?>
                        <tr>
                            <td>
                                <a href="<?= base_url() . 'violationoverview/violated_product/' . $data['id']; ?>"><?= html_entity_decode($data['title']); ?></a></td>
                            <td>
                                <?= $data['upc_code']; ?>
                            </td>
                            <td>
                                <?= '$' . $data['retail_price']; ?>
                            </td>
                            <td>
                                <?= '$' . $data['wholesale_price']; ?>
                            </td>
                            <td>
                                <?= '$' . $data['price_floor']; ?>
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

    $('#violated-products-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/violated-products-table",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
              "url": "/ajax/table_state_load/violated-products-table",
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