<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Edit Enforcement Email</strong>
        </h3>       
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/violationoverview/violation_dashboard">MAP Enforcement</a> 
            <i class="fa fa-angle-right"></i> <a href="/enforcement/templates">Email Templates</a> 
            <i class="fa fa-angle-right"></i> Template Message
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
    
						<section id="emails">

							<form autocomplete="off" action="<?=site_url('enforcement/template/'.$template_id)?>" method="post" id="enforcement_form" name="enforcement_form">
							
                    <input type="hidden" name="template_id" id="template_id" value="<?=$template_id;?>" />
                
                    <h2>
                        Edit <?=convert_number_to_name($notification_level)?> Warning Email Template
                    </h2>
                    
									<?php if ( ! empty($message)) echo '<div class="message error">'.$message.'</div>';?>

									<div id="emailTabs" class="tabs subTabs tabsYellow">
										<ul class="tabNav clear">
											<li>
											    <div class="tabCornerL"></div><a href="#HTML-known" class="tabItem">E-mail 1 Template (for known sellers)</a>
											</li>                      
											<li>
											    <div class="tabCornerL"></div><a href="#HTML-unknown" class="tabItem">E-mail 2 Template (for unknown sellers)</a>
											</li>											
										</ul>
										<section id="HTML-known" class="tabContent">
											<div class="emailLegend">
												<h4>Wildcard Text</h4>
												<p>#SellerName<br>
												#CompanyName<br>
												#ContactName<br>
												#Phone<br>
												#Evidence<br>
												#NameTo<br>
												#EmailTo<br>
												<!-- #ProductName --></p>
											</div>
											<textarea id="wysiwyg1" class="wysiwyg" name="known_seller_html_body"><?=set_value('known_seller_html_body', $known_seller_html_body);?></textarea>
											<?=form_error('known_seller_html_body');?>
										</section>
										
										<section id="HTML-unknown" class="tabContent">
											<div class="emailLegend">
												<h4>Wildcard Text</h4>
												<p>#SellerName<br>
												#CompanyName<br>
												#ContactName<br>
												#Phone<br>
												#Evidence<br>
												#NameTo<br>
												#EmailTo<br>
												<!-- #ProductName --></p>
											</div>
											<textarea id="wysiwyg2" class="wysiwyg" name="unknown_seller_html_body"><?=set_value('unknown_seller_html_body', $unknown_seller_html_body);?></textarea>
											<?=form_error('unknown_seller_html_body');?>
										</section>
										
									</div><!-- .tabs -->

									<div class="button redButton">
										<div class="buttonCornerL"></div>
										<input class="btn btn-success" name="submit" type="submit" value="Save Changes">
										<input id="cancel-button" class="btn btn-primary" type="button" value="Cancel" />
									</div>
							</form>


						</section>
    </div>
</div>

<script type="text/javascript">
      
$(document).ready(function() {

    $('#cancel-button').click(function(event) {

        window.location = '/enforcement/templates';

    });

});

</script>

<?php if ($this->config->item('environment') == 'production'): ?>
    <!--  
    <script id="interact_55ce7fe345617" data-unique="55ce7fe345617" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7fe345617"></script>
    -->
    <script id="interact_55db664540eb9" data-unique="55db664540eb9" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55db664540eb9"></script>
<?php endif; ?>    