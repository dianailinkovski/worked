<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Edit Product</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/catalog">Product Catalog</a> 
            <i class="fa fa-angle-right"></i> Edit Product
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
        
        <?php if (validation_errors() != ''): ?>    
            <div class="alert alert-danger" role="alert">
                <?php echo validation_errors(); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($form_error_msgs)): ?>
            <div class="alert alert-danger" role="alert">
            	<?php foreach ($form_error_msgs as $error_msg): ?>
                    <p><?php echo $error_msg; ?></p>
            	<?php endforeach; ?>
            </div>    
        <?php endif; ?>
        
        <form action="/catalog/edit_product/<?php echo $product['id']; ?>" method="post">
  
            <div class="new-product-row">
                <div class="form-group">
                    <label for="title">Product Title:</label>
                    <input name="title" class="form-control" value="<?php echo set_value('title', $product['title']); ?>" type="text" />
                </div>
            		<div class="form-group">
                		<label for="upc_code">UPC Code:</label>
                		<input name="upc_code" class="form-control" value="<?php echo set_value('upc_code', $product['upc_code']); ?>" type="text" />
              	</div>
            	  <div class="form-group">
            	    	<label for="sku">SKU:</label>
            	    	<input name="sku" class="form-control" value="<?php echo set_value('sku', $product['sku']); ?>" type="text" />
            	  </div>
            	  <div class="checkbox">
                    <label>
                        <?php $is_tracked_checkbox_value = intval(set_value('is_tracked', $product['is_tracked'])) == 1 ? TRUE : FALSE; ?>
                        <?php echo form_checkbox('is_tracked_checkbox', 'yes', $is_tracked_checkbox_value); ?> Tracked
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        <?php $is_archived_checkbox_value = intval(set_value('is_archived', $product['is_archived'])) == 1 ? TRUE : FALSE; ?>
                        <?php echo form_checkbox('is_archived_checkbox', 'yes', $is_archived_checkbox_value); ?> Archived
                    </label>
                </div>
            	  <div class="form-group">
            	    	<label for="retail_price">Retail Price:</label>
            	    	<div class="input-group">
            	    	    <span class="input-group-addon">$</span>
                	    	<input name="retail_price" class="form-control" value="<?php echo set_value('retail_price', $product['retail_price']); ?>" type="text" />
                	  </div>  	
            	  </div> 
            	  <div class="form-group">
            	      <label for="wholesale_price">Wholesale Price:</label>
            	      <div class="input-group">
        	              <span class="input-group-addon">$</span>
                	      <input name="wholesale_price" class="form-control" value="<?php echo set_value('wholesale_price', $product['wholesale_price']); ?>" type="text" />
                	  </div>    
            	  </div>   	  
            	  <div class="form-group">
            	      <label for="price_floor">MAP:</label>
        	          <div class="input-group">
        	              <span class="input-group-addon">$</span>
        	              <input name="price_floor" class="form-control" value="<?php echo set_value('price_floor', $product['price_floor']); ?>" type="text" />
        	          </div>
            	  </div>

            	  
            </div>    
            		
            <p>                
                <input class="btn btn-success" type="submit" value="Save Changes" /> 
                <input id="cancel-button" class="btn btn-primary" type="button" value="Cancel" /> 
            </p>	
        
        </form>

    </div>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#cancel-button').click(function(event) {

        window.location = '/catalog';

    });

});

</script>