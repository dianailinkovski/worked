<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Merchants</strong>
        </h3>
    </div>
    
    <div class="panel-body">
 
        <table class="table table-bordered table-striped table-success table-responsive reportTable exportable" id="merchants-table">
            <thead>
                <tr>
                    <th>id</th>
                    <th>profile</th>
                    <th>merchant_name</th>
                    <th>original_name</th>
                    <th>marketplace</th>
                    <th>seller_id</th>
                    <th>merchant_url</th>
                    <th>created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($merchants as $merchant): ?>
                    <tr>
                        <td>
                            <?php echo $merchant['id']; ?>
                        </td>
                        <td>
                            <a target="_blank" href="/merchants/profile/<?php echo $merchant['id']; ?>">Profile</a>
                        </td>
                        <td>
                            <?php echo $merchant['merchant_name']; ?>
                        </td>
                        <td>
                            <?php echo $merchant['original_name']; ?>
                        </td>
                        <td>
                            <?php echo $merchant['marketplace']; ?>
                        </td>   
                        <td>
                            <?php echo $merchant['seller_id']; ?>
                        </td>
                        <td>
                            <?php echo $merchant['merchant_url']; ?>
                        </td>
                        <td>
                            <?php echo $merchant['created']; ?>
                        </td>                                                                                                                                             
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table> 
    
    </div>
    
</div>

<script type="text/javascript">

$(document).ready(function() {

    var table = $('#merchants-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>'
    });

    new $.fn.dataTable.FixedHeader(table, {
        "offsetTop": 55
    }); 	
	
});

</script>