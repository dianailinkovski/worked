<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Add Product(s)</strong>
        </h3>
    </div>
    
    <div class="panel-body" id="add-products-area">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/catalog">Product Catalog</a> 
            <i class="fa fa-angle-right"></i> Add Product(s)
        </div>
        
        <div class="tabs bigTabs clear ui-tabs ui-widget ui-widget-content ui-corner-all">

    			<ul class="tabNav clear">
    				<li><div class="tabCornerL"></div><a href="#byCsv" class="tabItem" onclick="return false;">Upload CSV File</a></li>
    				<li><div class="tabCornerL"></div><a href="#byManual" class="tabItem" onclick="return false;">Manually add Products</a></li>
    			</ul>
    
    			<div id="byCsv" class="tabContent storeSettings<?=(($product_method == '' || $product_method == 'bycsv') ? '': ' hidden');?>">
    				<div class="content clear">
    					<div class="topLeft"></div>
    					<div class="topRight"></div>
    					<div class="whiteArea">
    						<div class="topLeft"></div>
    						<div class="topRight"></div>
    						
    						<?php
    							//switch between upload and the processing the csv file...
    							if($file_processing):
    						?>
    					    <form action="<?php echo base_url().'settings/save_csv_products';?>" method="post" id="formID" onsubmit="return validateSubmit();">
    						    <input type="hidden" name="has_header" id="has_header" value="<?=$has_header;?>">
    						    <input type="hidden" name="archive_products" id="archive_products" value="<?=$archive_products;?>">
    		            <input type="hidden" name="cols_count" id="cols_count" value="<?php if (isset($headerColumns)) echo count($headerColumns);?>">
    		            <input type="hidden" name="rows_count" id="rows_count" value="<?php if (isset($dataArray)) echo count($dataArray);?>">
    		            <input type="hidden" name="file_name" id="file_name" value="<?php echo $file_name;?>">
    
    								<p>Tell us how your data matches up to our system.</p>
    								<input id="csv_submit" type="submit" value="Submit" class="hidden btn btn-success">
    
    		            <table width="100%" id="csvImportTable" border="0" cellspacing="0" cellpadding="5" class="rpt_area"><?php
    									if ($has_header):?>
    									<thead>
    	                  <tr><?php
    											for ($i = 0; $i < count($dataArray[0]); $i++){ ?>
    	                    <th>
    												<input type="hidden" name="user_header_<?php echo $i; ?>" id="user_header_<?php echo $i;?>" value="<?php echo htmlentities(remove_non_ascii($dataArray[0][$i]));?>">
    												<?php echo htmlentities(remove_non_ascii($dataArray[0][$i])); ?>
    											</th><?php
    											} ?>
    		                </tr>
    									</thead><?php
    									endif;
    									?>
                                                                            <?php
                                                                                    for ($i = 0; $i < count($dataArray[0]); $i++){ ?>
                                                                                        <input type="hidden" name="header_<?php echo $i; ?>" id="header_<?php echo $i;?>" value="">
                                                                            <?php
                                                                                    } ?>
    		              <thead>
    		                <tr id="csv_headers">
    		                  <th id="th_0" class="edit_col">
    		                    <select id="set_header_0" name="set_header_0">
    		                      <option value="">Select</option>
    		                      <option value="ignore">Ignore</option>
    		                      <option value="title">Item Name</option>
    		                      <option value="upc_code">UPC</option>
    		                      <option value="sku">SKU</option>
    		                      <option value="retail_price">Retail Price</option>
    		                      <option value="price_floor">MAP</option>
    		                      <option value="wholesale_price">Wholesale Price</option>
    		                    </select>
    		                    <img src="/images/icons/16/71.png" alt="Save" title="Save" onclick="saveColName(0);" class="jsLink">&nbsp;
    												<img src="/images/icons/16/55.png" alt="Ignore" title="Ignore" onclick="deleteColField(0);" class="jsLink">
    		                  </th><?php
    											if (isset($headerColumns)){
    												for ($i = 1; $i < count($headerColumns); $i++) { ?>
    		                  <th id="th_<?php echo $i;?>">
    												Column&nbsp;<?=($i+1);?><br>
    												<img src="/images/icons/16/2.png" alt="Set" title="Set" onclick="showEditField(<?php echo $i; ?>);" class="jsLink">&nbsp;
    												<img src="/images/icons/16/55.png" alt="Ignore" title="Ignore" onclick="deleteColField(<?php echo $i; ?>);" class="jsLink">
    											</th><?php
    												}
    											} ?>
    		                </tr>
    		              </thead>
    		              <tbody><?php
    									for ($i = 0; $i < count($dataArray); $i++) {
    										//skip header row if present
    										if($has_header && $i==0) continue;
    										//only output a sampling of data...
    										if (($i+1) > CSV_RECORDS_PER_PAGE) break; ?>
    		                  <tr class="row_reports" id="rows_reports_<?php echo $i;?>"><?php
    												for ($j = 0; $j < count($dataArray[$i]); $j++):?>
    		                    <td id="prodrow_<?php echo $i.'_'.$j;?>"><?php echo htmlentities(remove_non_ascii($dataArray[$i][$j]));?></td><?php
    												endfor; ?>
    		                </tr><?php
    									} ?>
    		              </tbody>
    		            </table>
    					    </form>
    
    							<?php
    							else:
    							?>
    							
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
    
    					    <form action="<?=base_url().'settings/products';?>" method="post" enctype="multipart/form-data" id="form_csv_add" name="form_csv_add">
    								<?php if (!empty($error)) echo '<div class="message error">'.$error.'</div>';?>
    								<input type="hidden" name="product_method" value="bycsv">
    
    					      <div class="inputBlockContainer">
    					        <p><label for="csv_file"><b>Select CSV file:</b></label></p>
    									<p><input type="file" name="csv_file" id="csv_file" class="validate[required] medium"></p>
    					      </div>
    					      
    					      <div class="inputBlockContainer">
    					          <label><b>Check all that apply:</b></label>
    					      </div>
    
    					      <div class="inputBlockContainer">
    					        <label>Does this CSV file contain a column label header row?</label>
    									<input type="checkbox" name="has_header" id="has_header" value="1">
    					      </div>
    					      
    					      <div class="inputBlockContainer">
    					        <label>Archive (and untrack) your existing products in TrackStreet that are <b>not</b> contained in this CSV file?</label>
    									<input type="checkbox" name="archive_products" id="archive_products" value="1">
    					      </div>    					      
    
    								<div class="inputBlockContainer">
    									<input type="submit" name="submit" value="Upload File" id="upload-input-button">
    					      </div>
    
    								<p style="margin-top: 30px;">
    								    For reference: <a href="<?=base_url();?>sample-csv/sample.csv">Download an example CSV file here</a>.
    								</p>
    
    					    </form><!-- #csv_form -->
    
    							<?php
    							endif;
    							?>
    
    						<div class="bottomLeft"></div>
    						<div class="bottomRight"></div>
    					</div>
    					<div class="bottomLeft"></div>
    					<div class="bottomRight"></div>
    				</div>
    			</div>
    
    			<div id="byManual" class="tabContent storeSettings<?=(($product_method == 'byform') ? ' hidden': '');?>">
    				<div class="content clear">
    					<div class="topLeft"></div>
    					<div class="topRight"></div>
    					<div class="whiteArea">
    						<div class="topLeft"></div>
    						<div class="topRight"></div>
    
    						<form action="<?=base_url().'settings/products#byManual';?>" method="post"  id="form_manual_add" name="form_manual_add">
    							<?php if (!empty($error)) echo '<div class="message error">'.$error.'</div>';?>
    							<input type="hidden" name="product_method" value="byform">
    
    							<?php for($i=0; $i<$product_count; $i++):?>
    							<div class="product_info_row">
    								<?php if($i > 0):?>
    								<hr>
    								<img class="jsLink" id="remove_<?=$i;?>" src="/images/icons/24/69.png" alt="Remove Product" onclick="removeProduct(this);">
    								<?php endif;?>
    					      <div class="inputBlockContainer add_pro">
    					        <label for="title_<?=$i;?>">Product Title:</label>
    					        <input type="text" name="title[]" id="title_<?=$i;?>" value="<?=$title[$i];?>" class="validate[required]">
    					      </div>
    
    					      <div class="inputBlockContainer add_pro">
    					        <label for="upc_code_<?=$i;?>">UPC Code:</label>
    					        <input type="text" name="upc_code[]" id="upc_code_<?=$i;?>" value="<?=$upc_code[$i];?>" class="validate[required,custom[integer]]">
    					      </div>
    
    					      <div class="inputBlockContainer add_pro">
    					        <label for="sku_<?=$i;?>">SKU:</label>
    					        <input type="text" name="sku[]" id="sku_<?=$i;?>" value="<?=$sku[$i];?>">
    					      </div>
    
    					      <div class="inputBlockContainer add_pro">
    					        <label for="retail_price_<?=$i;?>">Retail Price:</label>
    					        <input type="text" name="retail_price[]" id="retail_price_<?=$i;?>" value="<?=$retail_price[$i];?>" class="validate[required]">
    					      </div>
    
    					      <div class="inputBlockContainer add_pro">
    					        <label for="wholesale_price_<?=$i;?>">Wholesale Price:</label>
    					        <input type="text" name="wholesale_price[]" id="wholesale_price_<?=$i;?>" value="<?=$wholesale_price[$i];?>">
    					      </div>
    
    					      <div class="inputBlockContainer add_pro">
    					        <label for="price_floor_<?=$i;?>">MAP:</label>
    					        <input type="text" name="price_floor[]" id="price_floor_<?=$i;?>" value="<?=$price_floor[$i];?>">
    					      </div>
    							</div>
    							<?php endfor;?>
    
    							<hr id="btns">
    				      <div class="inputBlockContainer">
    				          <div style="float:left; margin-right: 8px;">
                          <input type="button" name="add" id="add_more" value="Add Another Row" class="btn">
                      </div> 
                      <div style="float:left">  
                          <input type="submit" name="submit" value="Submit Products" id="submit-products" onclick="return validateManualProducts();">
                      </div>    
    				      </div>
    
    				    </form><!-- #manual_form -->
    
    					</div>
    				</div>
    			</div>

    			</div>
    </div>			
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce7bf54f5ff" data-unique="55ce7bf54f5ff" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7bf54f5ff"></script>
<?php endif; ?>    
    