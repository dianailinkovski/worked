
<div id="modal-content">

    <h3 class="large-subtitle">
        <?php echo $merchant['original_name']; ?><?php if ($merchant['original_name'] == $merchant['marketplace']): ?>.com<?php endif; ?>
    </h3>
    
    <?php if ($this->success_msg != ''): ?>
        <div class="alert alert-success" role="alert">
        	<?php echo $this->success_msg; ?>
        </div>
        <script type="text/javascript">

        self.parent.set_data_change_to_true();
	
        </script>    	
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
    
    <p>
        Use the following form to add a new merchant association to the existing merchant: 
        <b><?php echo $merchant['original_name']; ?><?php if ($merchant['original_name'] == $merchant['marketplace']): ?>.com<?php else: ?> (<?php echo ucfirst($merchant['marketplace']); ?> Seller)<?php endif; ?></b>
    </p>
    
    <p>
        By adding a new association, you'll create a link between these sales locations in our database, and you'll be able to easily move from one of these profiles to another.
    </p>
    
    <h3>
        Merchant Details
    </h3>
    
    <form action="/merchants/add_merchant_link/<?php echo $merchant_id; ?>" method="post">
    
        <div class="new-product-row">
            <div class="form-group">
                <label for="merchant_name">Merchant Name</label>
                <input autocomplete="off" id="merchant-name" name="merchant_name" class="form-control" value="<?php echo set_value('merchant_name', ''); ?>" type="text" />
            </div>
            
            <div id="existing-merchants-area">
            
            </div>
            
            <hr />
            
            <div id="new-merchant-details">           
                <h3>
                    Add Completely New Merchant to System
                </h3>
                <div class="form-group">
                    <label for="marketplace">Location</label><br/>
                    <?php echo $marketplace_dropdown; ?>
                </div>                
            		<div class="form-group" id="merchant-url-field">
                		<label for="merchant_url">Website URL</label><br />
                		<label for="merchant_url">Example: http://www.merchant.com</label><br />
                		<input name="merchant_url" class="form-control" value="<?php echo set_value('merchant_url', ''); ?>" type="text" />
              	</div>  
            		<div class="form-group" id="seller-id-field" style="display: none;">
                		<label for="seller_id">Marketplace Seller ID</label>
                		<input name="seller_id" class="form-control" value="<?php echo set_value('seller_id', ''); ?>" type="text" />
              	</div> 
            </div>
                  	               	          	  
        </div>    
        		
        <p>                
            <input class="btn btn-success" type="submit" value="Add Merchant Association" />
            <!--  <input id="cancel-button" class="btn btn-primary" type="button" value="Cancel" /> -->
        </p>	
    
    </form>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#merchant-name').on('input', function() {
        //alert($('#merchant-name').val());

        var search_str = $('#merchant-name').val();
        
        if (search_str.length > 2)
        {
            var form_data = "search_str=" + search_str + "&current_merchant_id=<?php echo $merchant_id; ?>";
            
            // call AJAX to do search
            $.ajax(
            		{
            			type: "POST",
            			data : form_data,
            			url: "/merchants/merchant_search_auto_complete",
            			success: function(html) {
            				
            				$('#existing-merchants-area').html(html);
            				
            			}
            		}
            );
        }
    });

    $('#marketplace-dropdown').change(function() {    

        var item = $(this);
        
        if (item.val() != '')
        {
            $('#merchant-url-field').hide();
            $('#seller-id-field').show();
        }
        else
        {
            $('#merchant-url-field').show();
            $('#seller-id-field').hide();
        }
    });	

    $('#cancel-button').click(function(event) {

        window.location = '/merchants/profile/<?php echo $merchant_id; ?>';

    });

});

</script>