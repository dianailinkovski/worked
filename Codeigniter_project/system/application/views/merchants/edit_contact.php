<div id="modal-content">
    
    <h3 class="large-subtitle">
        <?php if ($action == 'create'): ?>
            Add <?php echo $contact_type; ?> to <?php echo $merchant['original_name']; ?><?php if ($merchant['original_name'] == $merchant['marketplace']): ?>.com<?php endif; ?>
        <?php else: ?>
            Edit Merchant Contact
        <?php endif; ?>    
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
    
    <?php if ($action == 'create'): ?>
        <p>
          Please use the following fields to enter in the details for your new contact for this merchant.
        </p>
    <?php else: ?>    
        <p>
            Edit the details for this saved merchant contact.
        </p>
    <?php endif; ?>  
                
    <form action="<?php echo $form_action; ?>" method="post">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input name="first_name" class="form-control" value="<?php echo set_value('first_name', $contact['first_name']); ?>" type="text" />
        </div>
        <div class="form-group">
        		<label for="last_name">Last Name</label>
        		<input name="last_name" class="form-control" value="<?php echo set_value('last_name', $contact['last_name']); ?>" type="text" />
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input name="email" class="form-control" value="<?php echo set_value('email', $contact['email']); ?>" type="text" />
        </div>          
        <div class="form-group">
            <label for="phone">Phone</label>
            <input name="phone" class="form-control" value="<?php echo set_value('phone', $contact['phone']); ?>" type="text" />
        </div>  
        <p>                
            <input class="btn btn-success" type="submit" value="Save" /> 
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