<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Merchant Info</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> <i class="fa fa-angle-right"></i> Market Visibility <i class="fa fa-angle-right"></i> <a href="/enforcement/merchant">Merchant Info</a>
        </div>
    
				<section class="clear select_report">
					<fieldset>
						<div class="leftCol"><label>Display</label></div>
						<div class="rightCol">
							<div class="inputContainer reports_radio">
								<input type="radio" class="radio" name="merchOrRet" value="merchants" id="y" />
								<label for="y">Marketplace Merchants</label>
								<input type="radio" class="radio" name="merchOrRet" value="retailers" id="z" />
								<label for="z">Websites</label>
								<input type="radio" class="radio" name="merchOrRet" value="all" id="x" checked />
								<label for="x">All</label>
							</div>
						</div>
					</fieldset>
				</section>
				
				<?=$this->load->view('components/bydate2', '', TRUE)?>
				
				<section id="merchant">
					<div class="actionArea clear" style="padding: 10px 30px;">
						<div id="enforcement_grid_msg"></div>
						<div class="catalog_filter" style="display: inline; float: left; width: 50%;">
							Search: <input type="text" name="searchString" id="searchMerchantString" class="search">
							<!--  
							<div style="display:inline; margin: -2px 0px 5px 0px;">
								<input type="button" value="Reset" class="btn btn-success">
							</div>
							-->
						</div>
						<div id="export-contacts-area" style="float: right;">
						    <a id="export-contacts" class="btn btn-success" href="">Export to Excel</a>
						</div>
						<script type="text/javascript">
                  
            $(document).ready(function() {
                
                $("#export-contacts").click(function() {

                    // see: http://www.jqwidgets.com/jquery-grid-export-to-excel/
                    $("#enforcement_grid").jqxGrid('exportdata', 'xls', 'trackstreet_merchant_info');

                    return false;
                });
                
            });

            </script>
						
					</div>
					
					<!-- .actionArea -->
					<div id="enforcement_grid"></div>
					
					<?=$this->load->view('components/violator_notifications_form', '', TRUE)?>
	
					<!-- Dialog for show mearchant note list -->
                    <?=$this->load->view('enforcement/_merchant_notes_dialog', '', TRUE)?>
					
                    
                    <!-- Dialog for show mearchant staff note list -->
                    <?=$this->load->view('enforcement/_merchant_staff_notes_dialog', array('permission_id'=>$permission_id), TRUE)?>
					
                    <?php if ( isset($permission_id) && ($permission_id == 0 || $permission_id == 2) ): ?>
                    <!-- Dialog for show mearchant staff note list -->
                    <?=$this->load->view('enforcement/_merchant_staff_notes_edit_dialog', '', TRUE)?>
                    <?php endif; ?>
                    
                    <!-- Dialog for show contact history -->
                    <?=$this->load->view('enforcement/_contact_history_dialog', '', TRUE)?>
					
				</section>
    </div>
</div>

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce7e0dbba63" data-unique="55ce7e0dbba63" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce7e0dbba63"></script>
<?php endif; ?>    