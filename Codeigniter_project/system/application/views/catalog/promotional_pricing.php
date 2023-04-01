<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Promotional Pricing</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/catalog">Product Catalog</a> <i class="fa fa-angle-right"></i> Promotional Pricing
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
    
        <div id="catalog-top-area">
            <div id="product-buttons">
                <ul>
                    <li>
                        <a href="/catalog/add_promotional_pricing" id="promotional-pricing-button">Add Promotional Pricing</a>
                    </li>
                </ul>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div> 
        
        <?php if (!empty($products)): ?>   
        
            <form action="/catalog/bulk_action" method="post" id="products-form">
        
                <table class="table table-bordered table-striped table-success table-responsive" id="products-table">
                    <thead>
                        <tr>
                            <th>
                                <input id="select-all" type="checkbox" />
                            </th>
                            <th>
                                UPC
                            </th>
                            <th>
                                Title
                            </th>
                            <th>
                                Start
                            </th>
                            <th>
                                End
                            </th>
                            <th>
                                Promotional Price
                            </th>
                            <th>
                                MAP
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <input class="checkbox" name="select_product_id[]" value="<?php echo $product['id']; ?>" type="checkbox" />
                                </td>
                                <td>
                                    <?php echo $product['upc_code']; ?>
                                </td>
                                <td>                                
                                    <?php echo $product['title']; ?>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($product['period_start'])); ?>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($product['period_end'])); ?>
                                </td>
                                <td>
                                    $<?php echo $product['price']; ?>
                                </td>
                                <td>
                                    $<?php echo $product['price_floor']; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lg fa-fw fa-gear"></i> <span class="caret"></span></button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="/catalog/delete_promotional/<?php echo $product['uuid']; ?>">Delete</a>  
                                            </li>
                                        </ul>                           
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>        
                    </tbody>
                </table>
            
            </form>
            
            <script type="text/javascript">
                  
            $(document).ready(function() {
    
                $('#select-all').click(function(event) {
                    
                    if (this.checked) { // check select status
                    	
                        $('.checkbox').each(function() { //loop through each checkbox
                            this.checked = true;  //select all checkboxes with class "checkbox1"               
                        });
                    
                    } else {
                      
                        $('.checkbox').each(function() { //loop through each checkbox
                            this.checked = false; //deselect all checkboxes with class "checkbox1"                       
                        });   
            		              
            		    }
                    
                });
    
                var table = $('#products-table').DataTable({
                    "stateSave": true,
                    "order": [[ 2, "desc" ]],
                    "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]], // All = -1, breaks with stateLoadCallback
                    
                    "stateSaveCallback": function (settings, data) {
                        $.ajax({
                            "url": "/ajax/table_state_save/promotional_pricing_table_state_save",
                            "data": data,
                            "dataType": "json",
                            "type": "POST",
                            "success": function () {}    
                        });
                    },
                    "stateLoadCallback": function (settings) {
                        var o;
                    
                        $.ajax({
                          "url": "/ajax/table_state_save/promotional_pricing_table_state_load",
                          "async": false,
                          "dataType": "json",
                          "success": function (json) {
                            o = json;
                          }
                        });
                     
                        return o;
                    },  
                    
                    "language": {
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                    },          
                    // i = number of results info 
                    // f = search  
                    // dom: https://datatables.net/reference/option/dom
                    "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>',
                    "columnDefs": [ { "orderable": false, "targets": 0 } ]
                });
    
                // see http://www.datatables.net/extensions/fixedheader/options
                new $.fn.dataTable.FixedHeader(table, {
                    "offsetTop": 55
                });
    
            });
            
            </script>            
        
        <?php else: ?> 
            <p>
                No promotional pricing has been added yet.
            </p>
        <?php endif; ?>       
        
    </div>        
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce7c394e4ab" data-unique="55ce7c394e4ab" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7c394e4ab"></script>
<?php endif; ?>    