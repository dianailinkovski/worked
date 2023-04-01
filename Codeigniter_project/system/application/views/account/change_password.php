<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Change Password</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> Account <i class="fa fa-angle-right"></i> <a href="/account/change_password">Change Password</a>
        </div> 

        <?php if ($success_msg != ''): ?>
        	<div class="success-msg">
        		<?php echo $success_msg; ?>
        	</div>
        <?php endif; ?>
        
        <?php if ($error_msg != ''): ?>
        	<div class="error-msg">
        		<?php echo $error_msg; ?>
        	</div>
        <?php endif; ?>
        
        <?php if (validation_errors() != ''): ?>	
        	<div class="error-msg">
        		<?php echo validation_errors(); ?>
        	</div>
        <?php endif; ?>
        
        <h2>
            Change Password
        </h2>            
        
        <form action="/account/change_password" method="post">
            <p>
                Please use the following form to change your password. Your new password must be longer than 8 characters.
            </p>    
              
            <div class="form-group">
                <label for="new">Password:</label>
                <input class="form-control" name="new" id="new" value="" type="password" />
            </div>
              
            <div class="form-group">
                <label for="new_confirm">Confirm Password:</label>
                <input class="form-control" name="new_confirm" id="new_confirm" value="" type="password" />
            </div>    
              
            <p>
                <input id="change-password-button" type="submit" value="Change Password" /> 
            </p>
                  
        </form>
      
    </div>        
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce80d0820e2" data-unique="55ce80d0820e2" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce80d0820e2"></script>
<?php endif; ?>    