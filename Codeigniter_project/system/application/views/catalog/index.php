<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Product Catalog</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> Product Catalog
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
            <?php if ($this->role_id != 2): ?>
            
            <?php else: ?>
                <div id="product-buttons">
                    <ul>
                        <li>
                            <a href="/settings/products" id="add-products-button">Add Product(s)</a>
                        </li>
                        <li>
                            <a href="/settings/add_store" id="add-brand-button">Add Brand</a>
                        </li>
                    </ul>
                    <div class="clear"></div>
                </div>
            <?php endif; ?>  
            
            
            <div id="catalog-export">
                <ul>
                    <li>
                        Export: 
                    </li>
                    <li>
                        <a target="_blank" href="/catalog/export_catalog/excel" class="excel-export-button"><img title="Export to Excel" src="/images/icons/24/128.png" /></a>
                    </li>
                    <li>
                        <a target="_blank" href="/catalog/export_catalog/pdf" class="pdf-export-button"><img title="Export to PDF" src="/images/icons/pdf.png" /></a>
                    </li>
                </ul>
                <div class="clear"></div>            
            </div>
            
            
            <div class="clear"></div>
        </div>
        
        <form action="/catalog/bulk_action" method="post" id="products-form">
    
            <table class="table table-bordered table-striped table-success table-responsive reportTable exportable" id="products-table">
                <thead>
                    <tr>
                        <?php if ($this->role_id == 2): ?>
                            <th>
                                <input id="select-all" type="checkbox" />
                            </th>
                        <?php endif; ?>  
                        <th>
                            Image
                        </th>  
                        <th>
                            UPC
                        </th>
                        <th>
                            Title
                        </th>
                        <th>
                            Tracked
                        </th>
                        <th>
                            SKU
                        </th>
                        <th>
                            Retail
                        </th>
                        <th>
                            MAP
                        </th>
                        <th>
                            Wholesale
                        </th>
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <?php if ($this->role_id == 2): ?>
                                <td>
                                    <input class="checkbox" name="select_product_id[]" value="<?php echo $product['id']; ?>" type="checkbox" />
                                </td>
                            <?php endif; ?> 
                            <td>
                                <!--  <img width="100" class="product-image" src="http://stickyvision.juststicky.com/uploads/images/products/<?php echo $product['upc_code']; ?>.jpg" /> -->
                                <img width="100" class="product-image" src="https://app.trackstreet.com/uploads/images/products/<?php echo $product['upc_code']; ?>.jpg" />
                            </td>   
                            <td>
                                <?php echo $product['upc_code']; ?>
                            </td>
                            <td>                                
                                <?php echo $product['title']; ?> <?php if (intval($product['is_archived']) == 1): ?><b>[Archived]</b><?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <?php if ($product['is_tracked'] == '1'): ?>
                                    <!-- <a class="untrack-button" href="/catalog/untrack_product/<?php echo $product['id']; ?>">Un-track</a> -->
                                <?php else: ?>
                                    <!-- <a class="track-button" href="/catalog/track_product/<?php echo $product['id']; ?>">Track</a> -->
                                <?php endif; ?>
                                <a class="toggle-track-button <?php if ($product['is_tracked'] == '1'): ?>untrack-button<?php else: ?>track-button<?php endif; ?>" href="/catalog/toggle_track_product/<?php echo $product['id']; ?>">Track/Untrack Product</a>
                            </td>
                            <td>
                                <?php echo $product['sku']; ?>
                            </td>
                            <td>
                                $<?php echo $product['retail_price']; ?>
                            </td>
                            <td>
                                $<?php echo $product['price_floor']; ?>
                            </td>
                            <td>
                                $<?php echo $product['wholesale_price']; ?>
                            </td>
                            <td>
                                <?php if ($this->role_id != 2): ?>
                                
                                <?php else: ?>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lg fa-fw fa-gear"></i> <span class="caret"></span></button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="/catalog/edit_product/<?php echo $product['id']; ?>">Edit Product</a>
                                            </li>
                                            <li>
                                                <a href="/catalog/archive_product/<?php echo $product['id']; ?>">Archive</a>  
                                            </li>
                                        </ul>                           
                                    </div>
                                <?php endif; ?>        
                            </td>
                        </tr>
                    <?php endforeach; ?>        
                </tbody>
            </table>
        
        </form>
        
        <script type="text/javascript">
              
        $(document).ready(function() {

        	  /*
        	  $('img.product-image').error(function(){
                $(this).attr('src', 'product-no-image.png');
            });
            */

        	  $("img.product-image").error(function(){
        		    $(this).hide();
        	  });

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

            $('.toggle-track-button').click(function(){
                
                $.ajax({
                    "url": $(this).attr("href"),
                    "type": "POST",
                    "success": function () {}    
                });    

                $(this).toggleClass('track-button');
                $(this).toggleClass('untrack-button'); 

                return false;
            });

            /*
            $('.track-button').click(function(){
                $.ajax({
                    "url": $(this).attr("href"),
                    "type": "POST",
                    "success": function () {}    
                });    

                $(this).removeClass('track-button');
                $(this).addClass('untrack-button'); 

                return false;
            });

            $('.untrack-button').click(function(){
                $.ajax({
                    "url": $(this).attr("href"),
                    "type": "POST",
                    "success": function () {}    
                });   

                $(this).removeClass('untrack-button');
                $(this).addClass('track-button'); 

                return false;
            });
            */

            var table = $('#products-table').DataTable({
                "stateSave": true,
                "order": [[ 2, "desc" ]],
                "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
                
                "stateSaveCallback": function (settings, data) {
                    $.ajax({
                        "url": "/catalog/products_table_state_save",
                        "data": data,
                        "dataType": "json",
                        "type": "POST",
                        "success": function () {}    
                    });
                },
                "stateLoadCallback": function (settings) {
                    var o;
                
                    $.ajax({
                      "url": "/catalog/products_table_state_load",
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
                <?php if ($this->role_id == 2): ?>
                    "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>',
                <?php else: ?>
                    "dom": 'R<"top"f<"clear">>rt<"bottom"ilp<"clear">>',
                <?php endif; ?>        
                "columnDefs": [ { "orderable": false, "targets": 0 } ]
            });

            // see http://www.datatables.net/extensions/fixedheader/options
            new $.fn.dataTable.FixedHeader(table, {
                "offsetTop": 55
            });

            $("div.bulk-actions").html('<?php echo $this->load->view('catalog/parts/products_bulk_actions_select'); ?>');

            $('#products-bulk-action-select').change(function(){
                $('#products-form').submit();
            });

            $('#show-archived-products').change(function() {
                window.location = '/catalog/set_show_archive_cookie';
            });
        });
        
        </script>        
        
    </div>        
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce7b985ebc9" data-unique="55ce7b985ebc9" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7b985ebc9"></script>
<?php endif; ?>