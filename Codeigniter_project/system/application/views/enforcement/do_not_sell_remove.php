<div id="modal-content">
    
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
    
    <h3>
        Remove <?php echo $merchant['profile_name']; ?> from Do Not Sell List
    </h3>
    
    <form action="/enforcement/do_not_sell_remove/<?php echo $merchant_id; ?>" method="post">
        
        <p>
            Note About Removal
        </p>
        
        <p>
            <textarea style="width: 60%; height: 100px;" name="change_note"></textarea>
        </p>        
        
        <input type="hidden" name="redirect_to" value="/enforcement/do_not_sell_remove/<?php echo $merchant_id; ?>" />
         		
        <p style="margin-top: 20px;">                
            <input class="btn btn-success" type="submit" value="Remove Merchant" />
        </p>	
                
    </form>        
</div>



