<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>My Profile</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> Account <i class="fa fa-angle-right"></i> <a href="/account/profile">My Profile</a>
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
    
        <?php if ($user['profile_img'] != ''): ?>
            <div id="profile-photo">
                <img width="200" src="http://images.juststicky.com/stickyvision/profile_photos/<?php echo $user['profile_img']; ?>">
            </div>
        <?php endif; ?>    

        <div id="profile-area">
            <h3>
                First Name
            </h3>
            <p>
                <?php echo $user['first_name']; ?>
            </p>
            <h3>
                Last Name
            </h3>
            <p>
                <?php echo $user['last_name']; ?>
            </p>    
            <h3>
                Email
            </h3>
            <p>
                <?php echo $user['email']; ?>
            </p>   
            <h3>
                Phone
            </h3>
            <p>
                <?php echo $user['phone_number']; ?>
            </p> 
            <h3>
                Company
            </h3>
            <p>
                <?php echo $user['company_name']; ?>
            </p>   
            <h3>
                User Account Type
            </h3> 
            <p>
                <?php echo $user['role_label']; ?>
            </p>
        </div>
        
        <div class="clear"></div>
        
        <hr />
        
        <p>
            <a class="btn btn-success" href="/account/edit_profile">Edit Profile</a>
        </p>
    </div>
    
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce80d0820e2" data-unique="55ce80d0820e2" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce80d0820e2"></script>
<?php endif; ?>    