<?php if (isset($retailers[0])): ?>

    <div id="reports-top-area">
        <div id="report-save-options">
            <?php echo $this->load->view('components/save_options', '', TRUE); ?>
            <div class="clear"></div>            
        </div>
        <div class="clear"></div>
    </div> 

    <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="whois-retailers">
        <thead>
            <tr>
                <th>Retailer</th>
                <th>Products</th>
                <th>Violated Products</th>
                <th>Last Tracked</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($retailers as $data): ?>
                <tr>
                    <td>
                        <a href="<?php echo base_url() . 'whois/report_merchant/' . $data['marketplace'] . '/' . $data['id']; ?>"><span class="squareKey" style="background-color: #<?= marketplace_graph_color($data['marketplace']) ?>"></span><?php echo $data['display_name'] ?></a>
                    </td>
                    <td>
                        <?php echo number_format($data['total_products']); ?>
                    </td>
                    <td>
                        <?php echo number_format($data['violated_products']); ?>
                    </td>
                    <td>
                        <?php echo $data['last_tracked']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script type="text/javascript">
          
    $(document).ready(function() {
    
        $('#whois-retailers').DataTable({
            "stateSave": true,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "stateSaveCallback": function (settings, data) {
                $.ajax({
                    "url": "/ajax/table_state_save/whois-retailers",
                    "data": data,
                    "dataType": "json",
                    "type": "POST",
                    "success": function () {}    
                });
            },
            "stateLoadCallback": function (settings) {
                var o;
            
                $.ajax({
                  "url": "/ajax/table_state_load/whois-retailers",
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
        No records found.
    </p>
<?php endif; ?>