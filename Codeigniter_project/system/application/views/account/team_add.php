<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Add Team Member</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> Account <i class="fa fa-angle-right"></i> <a href="/account/team">Team Members</a> <i class="fa fa-angle-right"></i> <a href="/account/team_add">Add Member</a>
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
        
        <form action="/account/team_add" method="post">

        		<div class="form-group">
          	    <label for="firstname">First Name:</label><br />
                <input class="form-control" name="first_name" id="first_name" value="<?php echo set_value('first_name', ''); ?>" type="text" />
            </div>
        		<div class="form-group">
            		<label for="lastname">Last Name:</label><br />
            		<input class="form-control" name="last_name" id="last_name" value="<?php echo set_value('last_name', ''); ?>" type="text" />
          	</div>
        	  <div class="form-group">
        	    	<label for="email">Email:</label><br />
        	    	<input class="form-control" name="email" id="email" value="<?php echo set_value('email', ''); ?>" type="text" />
        	  </div>
        	  <div class="form-group">
        	      <label for="role_id">Permissions Role:</label><br />
        	      <?php echo $role_dropdown; ?>
        	  </div>
        		
            <input class="btn btn-success" type="submit" value="Add Member" /> 
        
        </form>
        
    </div>
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <!-- 
    <script id="interact_55ce80d0820e2" data-unique="55ce80d0820e2" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce80d0820e2"></script>
    -->
    <script src="//interact.juststicky.com/js/embed/client.js/2028/interact_5627d849ab0eb" id="interact_5627d849ab0eb" data-text="Discuss this with Sticky Interact" data-unique="5627d849ab0eb"></script>
<?php endif; ?>    