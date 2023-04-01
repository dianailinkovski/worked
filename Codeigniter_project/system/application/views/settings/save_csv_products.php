<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Imported Products</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/catalog">Product Catalog</a>
            <i class="fa fa-angle-right"></i> Imported Products
        </div>
    
        <?php if ($this->success_msg != ''): ?>
        	<div class="alert alert-success" role="alert">
        		<?php echo $this->success_msg; ?>
        	</div>
        <?php endif; ?>
        
        <?php if ($success_msg != ''): ?>
        	<div class="alert alert-success" role="alert">
        		<?php echo $success_msg; ?>
        	</div>
        <?php endif; ?>        
        
        <?php if ($this->error_msg != ''): ?>
        	<div class="alert alert-danger" role="alert">
        		<?php echo $this->error_msg; ?>
        	</div>
        <?php endif; ?>
        
        <div>
            <a href="/catalog" class="btn btn-success">View Product Catalog</a>
        </div>
        
        <hr />     
    
        <h3>
            New Products Added
        </h3>        
        
        <?php if (empty($added_products)): ?>
            <p>
                The CSV file did not contain any new products that were not already in the system.
            </p>
        <?php else: ?>   
            
            <p>
                A total of <?php echo count($added_products); ?> products were added to your brand's catalog.
            </p>

            <table class="table table-bordered table-striped table-success table-responsive" id="added-products-table">
                <thead>
                    <tr>
                        <th>
                            Title
                        </th>
                        <th>
                            UPC
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($added_products as $product): ?>
                        <tr> 
                            <td>                                
                                <?php echo $product['title']; ?>
                            </td>
                            <td>
                                <?php echo $product['upc_code']; ?>
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
                        </tr>
                    <?php endforeach; ?>        
                </tbody>
            </table> 
            
        <?php endif; ?>
        
        <hr />
        
        <h3>
            Updated Existing Products 
        </h3>        

        <?php if (empty($updated_products)): ?>
            <p>
                No existing products were updated.
            </p>        
        <?php else: ?>
            
            <p>
                A total of <?php echo count($updated_products); ?> products were updated with new data for your brand's catalog.
            </p>
    
            <table class="table table-bordered table-striped table-success table-responsive" id="updated-products-table">
                <thead>
                    <tr>
                        <th>
                            Title
                        </th>
                        <th>
                            UPC
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($updated_products as $product): ?>
                        <tr> 
                            <td>                                
                                <?php echo $product['title']; ?>
                            </td>
                            <td>
                                <?php echo $product['upc_code']; ?>
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
                        </tr>
                    <?php endforeach; ?>        
                </tbody>
            </table>         
        
        <?php endif; ?>
        
        <?php if ($debug_mode): ?>
            <div>
                <p>
                    Products in CSV file:
                </p>
                <p>
                    <?php var_dump($csv_file_product_ids); ?>
                </p>    
            </div>
            <div>
                <p>
                    Products archived:
                </p>
                <p>
                    <?php var_dump($archive_product_ids); ?>
                </p>    
            </div>
        <?php endif; ?>
        
    </div>        
</div>

