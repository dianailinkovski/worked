<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Edit Brand</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/catalog">Product Catalog</a> <i class="fa fa-angle-right"></i> Edit Brand
        </div>    
        
        <?php if (!empty($message)) echo '<div class="alert alert-success" role="alert">' . $message . '</div>';?>
    
        <h2>
            Edit Brand Details
        </h2>
    
						<section id="editBrand" class="clear">
							<form action="/settings/edit_store" class="formular clear" method="post" id="addStore1" enctype="multipart/form-data">
								
								<div class="storeLogo">
									<img src="<?=isset($merchant_logo) && !empty($merchant_logo) ? 'http://' . $this->config->item('s3_bucket_name') . '/stickyvision/brand_logos/' . $merchant_logo : base_url() . 'images/no_bio_image.gif';?>" alt="<?=(isset($merchant_logo) && !empty($merchant_logo)) ? $store_name : 'No Logo Available';?>" />
								</div>
		
								<?php //$this->load->view('admin/partials/notices');?>
								<div class="error" id="brand_logo_error" style="display:none"></div>
		
								<div class="inputBlockContainer" style="margin-top: 15px;">
									<p>
									    <label for="store_name"><b>Brand Name:</b></label>
									</p>    
									<p>
									    <input type="text" name="edit_store_name" id="store_name" tabindex="1" required="required" class="validate[required,minSize[3],maxSize[255] medium" value="<?=$store_name;?>" id="store_name" maxlength="255" />
									    <!-- <a href="javascript:void(0);" class="clickTip exampleTip" title="This is the name that you&rsquo;ll want for your own reference. It will show up on reports and screens later."><img src="<?=frontImageUrl();?>icons/question-mark.png" width="24" height="24" class="imgIcon" /></a> -->
									</p>    
								</div>
		
		            <div class="inputBlockContainer">
		                <p>
		                    <label for="brand_name_variants"><b>Brand Name Variants:</b></label>
		                </p>
		                <p>
		                    Note: This can contain a pipe-deliminated (example: brand name|brandname|Brand) string of alternative names your brand may go by.<br />
		                    <textarea name="brand_name_variants" style="width: 500px; height: 100px;"><?php echo $brand_name_variants; ?></textarea> 
		                </p>     
		            </div>
		
								<div class="inputBlockContainer">
								    <p>
									      <label for="brand_logo"><b>Upload Brand Logo:</b></label>
									  </p>
									  <p>    
									      <input type="file" name="brand_logo" id="brand_logo" tabindex="2" class="medium" />
									  </p>
								</div>
								
								<div class="inputBlockContainer" style="display: none">
									<label>Show merchant discussions on MAP Enforcement Page:</label>
									<input type="radio" name="note_enable" id="note_enable_on" value="1" tabindex="3"
									<?php //if ($note_enable==1) echo 'checked="checked"'?>
									checked="checked" /><label for="note_enable_on">On</label>
									&nbsp;
									<input type="radio" name="note_enable" id="note_enable_off" value="0" tabindex="4" <?php if ($note_enable==0) echo 'checked="checked"'?> /><label for="note_enable_off">Off</label>
								</div>
								
								<input id="edit_store" name="store_button" type="submit" value="Save" />
		
								<input type="hidden" name="old_image" value="<?=isset($merchant_logo) && !empty($merchant_logo) ? $merchant_logo : '';?>" />
							</form>
						</section>
						
    </div>
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    
    <!--
    <script id="interact_55ce7cc237f0e" data-unique="55ce7cc237f0e" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7cc237f0e"></script>
    -->
    
    <script id="interact_55fb123806901" data-unique="55fb123806901" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55fb123806901"></script>

<?php endif; ?>    