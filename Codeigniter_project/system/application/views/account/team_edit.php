<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Edit Team Member</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> Account 
            <i class="fa fa-angle-right"></i> <a href="/account/team">Team Members</a> 
            <i class="fa fa-angle-right"></i> Edit Member
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
            Member Details
        </h2>
        
        <form action="/account/team_edit/<?php echo $user_uuid; ?>" method="post">           
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
        	      <label for="role_id">Permissions Role:</label><br />
        	      <?php echo $role_dropdown; ?>
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

        window.location = '/account/team';

        return false;

    });

});

</script>

<?php if ($this->config->item('environment') == 'production'): ?>
    <!-- 
    <script id="interact_55ce80d0820e2" data-unique="55ce80d0820e2" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce80d0820e2"></script>
    -->
    <!-- 
    <script src="//interact.juststicky.com/js/embed/client.js/2028/interact_5627d5bca3226" id="interact_5627d5bca3226" data-text="Discuss this with Sticky Interact" data-unique="5627d5bca3226"></script>
    -->
    <script src="//interact.juststicky.com/js/embed/client.js/2028/interact_5627ddc6ccf01" id="interact_5627ddc6ccf01" data-text="Discuss this with Sticky Interact" data-unique="5627ddc6ccf01"></script>
<?php endif; ?>    