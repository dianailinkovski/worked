<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Edit Template Settings</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/violationoverview/violation_dashboard">MAP Enforcement</a> <i class="fa fa-angle-right"></i> <a href="/enforcement/templates">Email Templates</a> <i class="fa fa-angle-right"></i> Template Settings
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
            Edit <?php echo convert_number_to_name($template->notification_level); ?> Warning E-mail
        </h2>
        
        <form action="/enforcement/template_settings/<?php echo $template->id; ?>" method="post">
  
            <div class="new-product-row">
                <div class="form-group">
                    <label for="subject">Email Subject:</label>
                    <input name="subject" class="form-control" value="<?php echo set_value('subject', $template->subject); ?>" type="text" />
                </div>
            	  <div class="form-group">
            	    	<label for="notify_after_days">Notify after how many days?</label>
            	    	<div class="row">
                        <div class="col-xs-1">
            	    	        <input name="notify_after_days" class="form-control" value="<?php echo set_value('notify_after_days', $template->notify_after_days); ?>" type="text" /> 	
            	    	    </div>
            	    	</div>        
            	  </div>                    	  
             	  <div class="form-group">
            	    	<label for="no_of_days_to_repeat">Repeat for how many days?</label>
            	    	<div class="row">
                        <div class="col-xs-1">            	    	
            	    	        <input name="no_of_days_to_repeat" class="form-control" value="<?php echo set_value('no_of_days_to_repeat', $template->no_of_days_to_repeat); ?>" type="text" /> 	
            	    	    </div>
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

        window.location = '/enforcement/templates';

    });

});

</script>