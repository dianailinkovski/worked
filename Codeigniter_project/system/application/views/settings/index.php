<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Add Brand</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> <a href="/catalog">Product Catalog</a> <i class="fa fa-angle-right"></i> Add Brand
        </div>
        
        <h2 class="head">Step 1: Account Settings</h2>
    
					<form action="<?=site_url('settings/add_brand');?>" class="formular" method="post" id="addStore1" enctype="multipart/form-data">
						<input type="hidden" name="new_store_id" id="new_store_id" value="<?=$new_store_id;?>" />
						<?php if ( ! empty($message)) echo '<div class="message error">' . $message . '</div>';?>
						
						<div class="row_dat">
							<div class="lbel">Brand Name:</div>
							<div class="mdInpuCont">
								<input type="text" id="store_name" name="store_name" tabindex="1" class="validate[required,minSize[3],maxSize[255]] medium" value="<?=set_value('store_name');?>" />
								<span class="error" id="store_name_error"></span>
							</div>
							<div  class="clear"></div>
						</div>
						<div class="row_dat">
							<div class="lbel">Upload Brand Logo:</div>
							<div class="mdInpuCont"><input type="file" name="brand_logo" id="brand_logo" tabindex="2" class="medium" /></div>
							<div  class="clear"></div>
						</div>
						<div class="row_dat" style="width:80px; padding-top:10px;">
							<input id="create_store_step_1" type="submit" value="Next" class="btn btn-success">
							<div class="clear"></div>
						</div>
					</form>
					
    </div>
</div>  

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce7c7560bd6" data-unique="55ce7c7560bd6" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7c7560bd6"></script>
<?php endif; ?>    
    