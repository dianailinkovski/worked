<div id="modal-content">
    
    <h3 class="large-subtitle">
        Request Removal of Merchant Associaion    
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
        Remove link association between:
        <b><?php echo $parent_merchant['merchant_name']; ?></b> &amp; <b><?php echo $child_merchant['merchant_name']; ?></b>
    </p>
                
    <form action="/merchants/link_removal_request/<?php echo $parent_merchant_id; ?>/<?php echo $child_merchant_id; ?>" method="post">
        <p>
            Please provide a brief description for why you would like the association between these
            2 merchants to be removed.
        </p>
        <p>
            <textarea style="width: 80%; height: 100px;" name="reason"><?php echo set_value('reason', ''); ?></textarea>
        </p>
        <p>                
            <input class="btn btn-success" type="submit" value="Submit Request" /> 
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