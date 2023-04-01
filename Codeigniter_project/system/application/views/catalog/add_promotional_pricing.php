<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Add Promotional Pricing</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/catalog">Product Catalog</a> <i class="fa fa-angle-right"></i> Add Promotional Pricing
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

        <h2>
            Pricing Details
        </h2>        
        
        <form action="/catalog/add_promotional_pricing" method="post">
        		<div class="form-group">
          	    <label for="firstname">Product:</label><br />
                <input id="product-id-field"  type="text" value="" name="product_name" class="form-control product product_name ui-autocomplete-input" autocomplete="off">
                <input type="hidden" value="" id="product-id-hidden-field" name="product_id" />
            </div>
        	  <div class="form-group">
        	    	<label for="price">Promotional Price:</label>        	    	
    	    	    <div class="row">
    	    	        <div class="col-xs-2">  
        	    	        <div class="input-group">
        	    	            <span class="input-group-addon">$</span>
                    	    	<input name="price" class="form-control" value="<?php echo set_value('price', ''); ?>" type="text" />
                    	  </div>
                    </div> 	    	
            	  </div>  	
        	  </div> 
        		<div class="form-group">
        		    <div class="row">
    	    	        <div class="col-xs-2">
                  	    <label for="start_date">Start Date:</label>
                        <input class="form-control" name="start_date" id="start-date" value="<?php echo set_value('start_date', ''); ?>" type="text" />
                    </div>
                </div>        
            </div>    
        		<div class="form-group">
          	    <label for="end_date">End Date:</label>
          	    <div class="row">
    	    	        <div class="col-xs-2">
                        <input class="form-control" name="end_date" id="end-date" value="<?php echo set_value('end_date', ''); ?>" type="text" />
                    </div>
                </div>        
            </div>                 	        		
            <input class="btn btn-success" type="submit" value="Add Promotional Pricing" />
        </form>
        
    </div>
</div>

<script type="text/javascript">
              
$(document).ready(function() {

	$('#product-id-field').autocomplete({
		source: function( request, response ) {
			$.ajax({
							url: "/schedule/get_products_names/"+request.term,
							dataType: "json",
							data: {},
							success: function(items) {
								if(!items || items.length == 0) {
									return false;
								}
								response($.map( items, function( item ) {
									return {
													value: item.title,
													id: item.id
													}
								}));
							}
						});
		},
		minLength: 1,
		deferRequestBy: 0,
		select: function( event, ui ) {
			$(this).val(ui.item.value);
			$('#product-id-hidden-field').val(ui.item.id);
		}
	});

	$('#start-date').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate:'yy-mm-dd'
	});

	$('#end-date').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate:'yy-mm-dd'
	});

});

</script>