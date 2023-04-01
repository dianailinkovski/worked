<?php if ($this->success_msg != '' || $success_msg != ''): ?>
	<div class="alert alert-success" role="alert">
		<?php echo $this->success_msg; ?>
	</div>
<?php endif; ?>

<?php if ($this->error_msg != ''): ?>
	<div class="alert alert-danger" role="alert">
		<?php echo $this->error_msg; ?>
	</div>
<?php endif; ?>

<?php if ($error_msg != ''): ?>
	<div class="alert alert-danger" role="alert">
		<?php echo $error_msg; ?>
	</div>
<?php endif; ?>

<?php if (validation_errors() != ''): ?>	
	<div class="alert alert-danger" role="alert">
		<?php echo validation_errors(); ?>
	</div>
<?php endif; ?>

<div id="login-area"> 
    <div id="login-form" style="width: 400px;">
        
        <div class="well no-padding">
        
            <form class="smart-form client-form" action="/login" method="post">
            	<header>
            		Account Login
            	</header>
            
            	<fieldset>
            		
            		<section>
            			<label class="label">Email</label>
            			<label class="input"> <i class="icon-append fa fa-user"></i>
            				<input class="form-control" name="email" id="email" value="" type="text" />
            				<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter in your email address.</b></label>
            		</section>
            
            		<section>
            			<label class="label">Password</label>
            			<label class="input"> <i class="icon-append fa fa-lock"></i>
            				<input class="form-control" name="password" id="password" value="" type="password" />
            				<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Please enter in your password.</b> </label>
            			<div class="note">
            				<a href="/signup/forgot_password">Forgot password?</a>
            			</div>
            		</section>
            
                <!--  
            		<section>
            			<label class="checkbox">
            				<input type="checkbox" name="remember" checked="">
            				<i></i>Stay signed in</label>
            		</section>
            		-->
            		
            	</fieldset>
            	<footer>
            		<input class="btn btn-primary" type="submit" value="Login" /> 
            	</footer>
            </form>
            
        </div>
    </div>
</div>

<!--  
<div id="terms-area">
    <div id="accept-form">
        <form action="/login" method="post">
        
            <fieldset>
                <p>
                    Please enter in your email address and password to log into your account.
                </p>    
                  
                <p>
                    <label for="email">Email:</label>
                    <input name="email" id="email" value="" type="text" />
                </p>
                  
                <p>
                    <label for="password">Password:</label>
                    <input name="password" id="password" value="" type="password" />
                </p>    
            </fieldset>   
              
            <p>
                <input type="submit" value="Login" /> 
            </p>
            
            <p>
                <a href="/signup/forgot_password">Forgot password?</a>
            </p>
                  
        </form>
    </div>        
</div>  

-->