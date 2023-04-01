<?php $this->load->view('components/overview_header', array('report_name' => 'Change Password:'), TRUE); ?>

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

<h2 id="terms-header">
    Set Password for New Account
</h2>

<div id="terms-area">
    <div id="accept-form">
        <form action="/signup/password/<?php echo $user_uuid; ?>" method="post">
        
            <fieldset>
                <p>
                    Please use the following form to set your new password. Your new password must be longer than 8 characters.
                </p>    
                <p>
                    <label for="new">Password:</label>
                    <input name="new" id="new" value="" type="password" />
                </p>
                  
                <p>
                    <label for="new_confirm">Confirm Password:</label>
                    <input name="new_confirm" id="new_confirm" value="" type="password" />
                </p>    
            </fieldset>   
              
            <p>
                <input class="btn btn-success" type="submit" value="Save New Password" /> 
            </p>
                  
        </form>
    </div>
</div>            

<?php $this->load->view('account/parts/accounts_footer'); ?>