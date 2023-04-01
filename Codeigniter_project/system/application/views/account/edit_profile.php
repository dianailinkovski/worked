<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Edit Profile</strong>
        </h3>
    </div>
    
    <div class="panel-body">

        <?php if ($this->success_msg != ''): ?>
        	<div class="success-msg">
        		<?php echo $this->success_msg; ?>
        	</div>
        <?php endif; ?>
        
        <?php if ($this->error_msg != ''): ?>
        	<div class="error-msg">
        		<?php echo $this->error_msg; ?>
        	</div>
        <?php endif; ?>
        
        <?php if (validation_errors() != ''): ?>    
            <div class="error-msg">
                <?php echo validation_errors(); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($form_error_msgs)): ?>
            <div class="error-msg">
            	<?php foreach ($form_error_msgs as $error_msg): ?>
                    <p><?php echo $error_msg; ?></p>
            	<?php endforeach; ?>
            </div>    
        <?php endif; ?>

        <h3>
            Your Details
        </h3>        
        
        <form action="/account/edit_profile" method="post">
        		<div class="form-group">
          	    <label for="firstname">First Name:</label><br />
                <input class="form-control" name="first_name" id="first_name" value="<?php echo set_value('first_name', $user['first_name']); ?>" type="text" />
            </div>
        		<div class="form-group">
            		<label for="lastname">Last Name:</label><br />
            		<input class="form-control" name="last_name" id="last_name" value="<?php echo set_value('last_name', $user['last_name']); ?>" type="text" />
          	</div>
        	  <div class="form-group">
        	    	<label for="email">Email:</label><br />
        	    	<input class="form-control" name="email" id="email" value="<?php echo set_value('email', $user['email']); ?>" type="text" />
        	  </div>
        	  <div class="form-group">
        	    	<label for="phone_number">Phone:</label><br />
        	    	<input class="form-control" name="phone_number" id="phone_number" value="<?php echo set_value('phone_number', $user['phone_number']); ?>" type="text" />
        	  </div>
            <input class="btn btn-success" type="submit" value="Save Changes" />
            <input id="cancel-button" class="btn btn-primary" type="button" value="Cancel" />
        </form>
        
        <hr />
        
        <form action="/account/upload_profile_photo" method="post" enctype="multipart/form-data">
            <fieldset>
                <h3>
                    Profile Photo
                </h3> 	
                
                <?php if ($user['profile_img'] != ''): ?>
                    <div>
                        <img width="200" src="http://images.juststicky.com/stickyvision/profile_photos/<?php echo $user['profile_img']; ?>">
                    </div>
                <?php endif; ?>
                								
                <p>
                    <label for="profile_photo">Upload New Photo:</label><br />
        		        <input type="file" name="profile_photo" id="profile_photo" class="medium" />
        		    </p>
        		    <p>
                    <input class="btn btn-success" type="submit" value="Change Photo" /> 
                </p>	
            </fieldset>
        </form>

    </div>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#cancel-button').click(function(event) {

        window.location = '/account/profile';

    });

});

</script>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce80d0820e2" data-unique="55ce80d0820e2" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce80d0820e2"></script>
<?php endif; ?>    