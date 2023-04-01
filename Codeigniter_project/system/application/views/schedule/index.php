<div class="panel panel-default">

    <div class="panel-heading" id="section-content-title">
        <h3 class="panel-title">
            <strong>Schedule Reports</strong>
        </h3>
    </div>
    
    <div class="panel-body">
    
        <div id="panel-breadcrumbs">
            <i class="fa fa-caret-square-o-right"></i> <a href="/">Home</a> 
            <i class="fa fa-angle-right"></i> <a href="/schedule">Schedule Reports</a>
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

    	<div style="margin-bottom: 10px;">
    		<a id="add_schedule" href="#">Add a New Report</a>
    	</div>
    
    	<section id="scheduleList" class="tabContent">
    
    		<div class="scheduleActionArea hidden">
    			<div class="whiteArea">
    			  <div class="bulk">
    			    <h3>Add/Edit Scheduled Report</h3>
    			    <form id="schedule_edit" name="schedule_edit">
    				    <div class="schd_error error hidden">Please complete all form fields</div>
    
    						<div class="inputBlockContainer">
    							<div class="inputContainer">
    								<label for="report_name">Report Name:</label>
    								<input type="text" id="report_name" name="report_name" value="">
    								<input type="hidden" name="report_id" value="">
    							</div>
    							<div class="inputContainer">
    								<label for="controller">Report Type:</label>
    								<select name="controller" id="controller">
    									<option value="reports">Price Trend Report</option>
    									<option value="violations">Violation Report</option>
    									<option value="overview">Pricing Overview</option>
    									<option value="violationoverview">Violation Overview</option>
    								</select>
    								<input type="hidden" name="contoller_function" id="contoller_function" value="">
    							</div>
    						</div>
    
    						<div class="inputBlockContainer sch_prods">
    							<div class="inputContainer">
    								<label for="report_product">Specific Product</label>
    								<input type="radio" name="report_products" id="report_product" value="productpricing" checked="checked">
    							</div>
    							<!--  
    							<div class="inputContainer">
    								<label for="report_group">Specific Group</label>
    								<input type="radio" name="report_products" id="report_group" value="group_report">
    							</div>
    							-->
    						</div>
    
    						<div class="inputBlockContainer">
    							<label for="report_products_val" class="report_products_lbl">Product Name:</label>
    							<input type="text" name="report_products_val" class="report_products_val" value="">
    							<input type="hidden" name="report_products_vals" class="report_products_vals" value="">
    						</div>
    
    						<div class="inputBlockContainer">
    							<div class="inputContainer">
    								<label for="report_datetime">Report Schedule:</label>
    								<input type="text" name="report_datetime" id="report_datetime" class="report_datetime" size="12" value="">
    								<?php echo renderHourDropDown('hh')?>:
    								<?php echo renderMinuteDropDown('mm')?>:
    								<select name="ampm" id="ampm">
    									<option value="am">am</option>
    									<option value="pm">pm</option>
    								</select>
    							</div>
    							<div class="inputContainer">
    								<label>Recurring:</label>
    								<select name="report_recursive_frequency" id="report_recursive_frequency" onchange="markRecursive(this.value)">
    									<option value="0">None</option>
    									<option value="1">Every Day</option>
    									<option value="7">Every Week</option>
    									<option value="31">Every Month</option>
    									<option value="365">Every Year</option>
    								</select>
    							</div>
    						</div>
    
    						<div class="inputBlockContainer">
    							<label for="email_addresses">Emails:</label>
    							<input type="text" name="email_addresses" id="email_addresses" value="">
    						</div>
    
    				    <div class="email_container"></div>
    
    				    <input type="button" class="btn btn-success save_schedule" name="save_schedule" value="Save Changes">
    				    <input type="button" id="cancel_schedule" class="btn btn-primary jsLink" value="Cancel">
    
    			    </form>
    			    <div class="clear"></div>
    			  </div>
    
    			</div>
    		</div>
    		
    		<?php if (!empty($reports)): ?>
    
        		<div id="schedule-reports-table-area">
        		
                <table class="table table-bordered table-striped table-success table-responsive" id="products-table">
                    <thead>
                        <tr>
                            <th>
                                Title
                            </th>
                            <th>
                                Report Type
                            </th>
                            <th>
                                Start Date
                            </th>
                            <th>
                                Recurring
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>    		
                            <tr>
                                <td>
                                    <?php echo $report['report_name']; ?>
                                </td>
                                <td>
                                    <?php echo $report['display_type']; ?>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($report['report_datetime'])); ?>
                                </td>
                                <td>
                                    <?php echo $report['report_recursive_string']; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lg fa-fw fa-gear"></i> <span class="caret"></span></button>
                                        <ul class="dropdown-menu" role="menu">
                                            <!--  
                                            <li>
                                                <a href="/schedule/edit_report/<?php echo $report['id']; ?>">Edit Report</a>
                                            </li>
                                            -->
                                            <li>
                                                <a href="/schedule/delete_report/<?php echo $report['id']; ?>">Delete</a>  
                                            </li>
                                        </ul>                           
                                    </div>                            
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>        
        		
        		</div>
        		
      <?php else: ?>
      
          <div id="schedule-reports-table-area">
              <p>
                  No reports are scheduled.
              </p>
          </div>
      
      <?php endif; ?>  		
    
    	</section>
        
    </div>
</div>   

<?php if ($this->config->item('environment') == 'production'): ?>
    <script id="interact_55ce802c83de2" data-unique="55ce802c83de2" data-text="Discuss this with Sticky Interact" src="//interact.juststicky.com/js/embed/client.js/2028/interact_55ce802c83de2"></script>
<?php endif; ?>
    