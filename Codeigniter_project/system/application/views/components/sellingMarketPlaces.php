<?php if (isset($marketplaces[0])): ?>
    <table class="table table-bordered table-striped table-success table-responsive exportable reportTable" id="whois-marketplaces">
        <thead>
            <tr>
                <th>Marketplace</th>
                <th>Merchants</th>
                <th>Products</th>
                <th>Violated Products</th>
                <th>Last Tracked</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($marketplaces as $data): ?>
                <tr>
                    <td>
                        <a href="<?php echo base_url() . 'whois/report_marketplace/' . $data['marketplace'] ?>"><span class="squareKey" style="background-color: #<?= marketplace_graph_color($data['marketplace']) ?>"></span><?php echo $data['display_name'] ?></a>
                    </td>
                    <td>
                        <?php echo number_format($data['total_listing']); ?>
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
    
        $('#whois-marketplaces').DataTable({
            "stateSave": true,
            "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
            "stateSaveCallback": function (settings, data) {
                $.ajax({
                    "url": "/ajax/table_state_save/whois-marketplaces",
                    "data": data,
                    "dataType": "json",
                    "type": "POST",
                    "success": function () {}    
                });
            },
            "stateLoadCallback": function (settings) {
                var o;
            
                $.ajax({
                  "url": "/ajax/table_state_load/whois-marketplaces",
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