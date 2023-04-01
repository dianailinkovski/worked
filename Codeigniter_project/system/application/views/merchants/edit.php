<div id="modal-content">
    
    <h3 class="large-subtitle">
        Edit Merchant Details    
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
        Please use the following form to edit details about <?php echo $merchant_name; ?>.
    </p>
              
    <form action="/merchants/edit/<?php echo $merchant_id; ?>" method="post">
        <div class="form-group">
            <label for="contact_email">Contact Email</label>
            <input name="contact_email" class="form-control" value="<?php echo set_value('contact_email', $merchant['contact_email']); ?>" type="text" />
        </div>          
        <div class="form-group">
            <label for="phone">Phone</label>
            <input name="phone" class="form-control" value="<?php echo set_value('phone', $merchant['phone']); ?>" type="text" />
        </div>
        <div class="form-group">
            <label for="Fax">Fax</label>
            <input name="fax" class="form-control" value="<?php echo set_value('fax', $merchant['fax']); ?>" type="text" />
        </div>          
        <div class="form-group">
            <label for="address_1">Address Line 1</label>
            <input name="address_1" class="form-control" value="<?php echo set_value('address_1', $merchant['address_1']); ?>" type="text" />
        </div>
        <div class="form-group">
            <label for="address_2">Address Line 2</label>
            <input name="address_2" class="form-control" value="<?php echo set_value('address_2', $merchant['address_2']); ?>" type="text" />
        </div> 
        <div class="form-group">
            <label for="city">City</label>
            <input name="city" class="form-control" value="<?php echo set_value('city', $merchant['city']); ?>" type="text" />
        </div>   
        <div class="form-group">
            <label for="state">State/Provence</label>
            <input name="state" class="form-control" value="<?php echo set_value('state', $merchant['state']); ?>" type="text" />
        </div> 
        <div class="form-group">
            <label for="zip">ZIP/Postal Code</label>
            <input name="zip" class="form-control" value="<?php echo set_value('zip', $merchant['zip']); ?>" type="text" />
        </div>                                   
        <p>                
            <input class="btn btn-success" type="submit" value="Save Changes" /> 
            <!-- <input id="cancel-button" class="btn btn-primary" type="button" value="Cancel" />  -->
        </p>               
    </form>      

</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#cancel-button').click(function(event) {

        window.location = '/merchants/profile/<?php echo $merchant_id; ?>';

    });

});

</script>