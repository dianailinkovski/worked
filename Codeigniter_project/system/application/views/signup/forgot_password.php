<?php $this->load->view('components/overview_header', array('report_name' => 'Change Password:'), TRUE); ?>

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

<h2 id="terms-header">
    Forgot Password - Email Required
</h2>

<div id="terms-area">
    <div id="accept-form">
        <form action="/signup/forgot_password" method="post">
        
            <fieldset>
                <p>
                    Please enter in your email address and a link to change your password will be emailed to you.
                </p>    
                <p>
                    <label for="email">Email:</label>
                    <input name="email" id="email" value="" type="text" />
                </p>
            </fieldset>   
              
            <p>
                <input type="submit" value="Submit" /> 
            </p>
                  
        </form>
    </div>
</div>            

<?php $this->load->view('account/parts/accounts_footer'); ?>