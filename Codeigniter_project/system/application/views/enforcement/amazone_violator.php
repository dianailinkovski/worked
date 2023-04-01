<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Marketplace Logins</strong>
        </h3>       
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview/violation_dashboard">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> MAP Settings 
            <i class="fa fa-angle-right"></i> <a href="/enforcement/amazone_violator">Marketplace Logins</a>
        </div>
    
        <section id="editBrand" class="clear">
        
            <?php if (!empty($email_message)) echo '<div class="alert alert-success" role="alert">' . $email_message . '</div>'; ?>
        
            <form action="<?=site_url('enforcement/amazone_violator')?>" class="formular clear" method="post" id="addStore1" autocomplete="off" >
								
							  <p>
							      The program will notify violators on your behalf, every night, using these account logins.
							  </p>
								<p>
								    Amazon places a limit of 20 notifications per day per account, so please add several logins here, if you are using Amazon.
								</p>
	
								<div class="message error" id="amazon-error"></div>
						
								<?php $ctr = 0 ?>
								<?php //echo "<pre>"; print_r($email_settings); exit;?>	
								<?php foreach ($email_settings as $arr): ?>
								<?php $ctr++?>
								<div id="entry<?= $ctr?>" class="clonedInput">
									<br>
									<hr>
									<h2 id="reference" name="reference" class="heading-reference">Login #<?= $ctr?></h2>
									<?php if(!empty($arr['login_failed'])): ?>
										<div class="error">This login has been failing lately.  Please check it: <a href="https://www.amazon.com/gp/communication-manager/inbox.html?ie=UTF8&ref_=ya_emails_with_sellers">https://www.amazon.com/gp/communication-manager</a>.</div>
										<?php if(!empty($arr['message'])): ?>
											<div class="error">Amazon Error message: <i><?=$arr['message'];?></i></div>
										<?php endif; ?>
										<br/>
									<?php endif; ?>
									 <fieldset>
										<label class="label_marketplace" for="marketplace">Marketplace:</label>
										<select name="ID<?php echo $ctr; ?>_marketplace" id="marketplace" class="validate[marketplace] medium input_marketplace" required>
											<option>Select One</option>
											<option<?= ($arr['marketplace']=='amazon') ? ' selected=selected':''?> value="amazon">Amazon.com</option>
											<option<?= ($arr['marketplace']=='gunbroker') ? ' selected=selected':''?> value="gunbroker">Gunbroker.com</option>
										</select>
									</fieldset>
									 <fieldset>
										<label class="label_email" for="email">Login Name:</label>
										<input type="text" name="ID<?php echo $ctr; ?>_email" id="email" class="validate[email] medium input_email" value="<?= @$arr['email']?>" />
									</fieldset>
									<fieldset>
										<label class="label_password" for="password">Login Password:</label>
										<input type="text" name="ID<?php echo $ctr; ?>_password" id="password" class="medium input_password" value="<?= @my_decrypt($arr['password'])?>"/>
									</fieldset>
								</div>
								<?php endforeach;?>
								
								<?php if ($ctr >= 5): ?>
								
								    <!-- max 5 login accounts allowed -->
								    <hr />
								    <p>
								        Maximum of 5 account logins has been reached.
								    </p>  
								    <hr />  
								
								<?php else: ?>
								
    								<hr />
    								<div id="addDelButtons">
    									<input class="btn btn-primary" type="button" id="btnAdd" value="Add Section"> 
    									<input class="btn btn-primary" type="button" id="btnDel" value="Remove Section">
    								</div>
    								<hr />
    								
    						<?php endif; ?>		
 
								<?php 
								
								/*								
								<div>
									<div class="inputBlockContainer">
										<label for="email">Amazon Email:</label>
										<input type="text" name="email" id="email" tabindex="5" class="validate[email] medium" value="<?= $email?>" />
									</div>
									<div class="inputBlockContainer">
										<label for="password">Amazon Password:</label>
										<input type="password" name="password" id="password" tabindex="6" class="medium" value="<?= $password?>"/>
									</div>
								</div>
								<?php if(isset($this->permission_id) && ($this->permission_id == '0' || $this->permission_id == '2')):?>
								<br/>
								<h1>Proxy Settings</h1>
								<br/>
								<div class="inputBlockContainer">
									<label for="proxy_address">Proxy Address:</label>
									<input type="text" name="proxy_address" id="proxy_address" tabindex="7" value="<?= $proxy_address?>" />
								</div>
								<div class="inputBlockContainer">
									<label for="proxy_port">Proxy Port:</label>
									<input type="text" name="proxy_port" id="proxy_port" tabindex="8" value="<?= $proxy_port?>" />
								</div>
								<div class="inputBlockContainer">
									<label for="proxy_user">Proxy User:</label>
									<input type="text" name="proxy_user" id="proxy_user" tabindex="9" value="<?= $proxy_user?>" />
								</div>
								<div class="inputBlockContainer">
									<label for="proxy_password">Proxy Password:</label>
									<input type="text" name="proxy_password" id="proxy_password" tabindex="10" value="<?= $proxy_password?>" />
								</div>
								<?php endif;?>
                */
                ?>
                
								<input class="btn btn-success" id="edit_store" name="store_button" type="submit" value="Save">
								
							</form>
						</section>
    </div>
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <!-- 
    <script id="interact_55ce7fe345617" data-unique="55ce7fe345617" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7fe345617"></script>
    -->
    <script id="interact_55db664540eb9" data-unique="55db664540eb9" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55db664540eb9"></script>
<?php endif; ?>    