<?php if (!empty($savedReports)): ?>

    <table class="table table-bordered table-striped table-success table-responsive" id="saved-reports-table">
        <thead>
    		    <tr>
    		        <th align="left">Name</th>
    		        <th align="center">Actions</th>
    		    </tr>
        </thead>
        <tbody>
        <?php foreach ($savedReports as $data): ?>
        		<tr id="tr_list_<?=$data->id;?>">
        			<td width="95%">
        				<span class="reportName jsLink"><?=$data->report_name;?></span>
        
        				<div id="tr_list_<?=$data->id?>_details" class="reportDetails hidden">
        					<form id="report_edit_<?=$data->id?>" name="report_edit_<?=$data->id?>">
        						<div class="rpt_error error hidden">Please complete all form fields</div>
        
        						<label for="report_name">Report Name:</label>
        						<input type="text" name="report_name" value="<?=$data->report_name?>">
        						<input type="hidden" name="report_id" value="<?=$data->id?>">
        						<input type="hidden" name="controller" value="<?=$data->controller?>">
        						<input type="hidden" name="controller_function" value="<?=$data->controller_function?>">
        						<input type="hidden" name="report_where" value="<?=htmlentities($data->report_where)?>">
        
        						<div class="clear"></div>
        
        						<div class="automation">
        							<label for="report_datetime">Report Schedule:</label>
        							<input type="text" name="report_datetime" class="report_datetime" size="12" value="<?=$data->report_datetime;?>">
        									<?php echo renderHourDropDown('hh', $data->hh)?>:
        									<?php
        									echo renderMinuteDropDown('mm', $data->mm);
        									$amSel = ($data->ampm == 'am') ? ' selected' : '';
        									$pmSel = ($data->ampm == 'pm') ? ' selected' : '';
        									?>:
        									<select name="ampm">
        										<option value="am"<?=$amSel;?>>am</option>
        										<option value="pm"<?=$pmSel;?>>pm</option>
        									</select>
        
        									<label>Recurring:</label>
        		              <?php $frequency = $data->report_recursive_frequency;?>
        									<select name="report_recursive_frequency">
        										<option value="0">None</option>
        										<option value="1"<?=$frequency === '1' ? 'selected="selected"' : ''?>>Every Day</option>
        										<option value="7"<?=$frequency === '7' ? 'selected="selected"' : ''?>>Every Week</option>
        										<option value="31"<?=$frequency === '31' ? 'selected="selected"' : ''?>>Every Month</option>
        										<option value="365"<?=$frequency === '365' ? 'selected="selected"' : ''?>>Every Year</option>
        									</select>
        
        									<input type="button" class="save_report" name="save_report" value="Save Changes">
        									<span class="cancel_savedreports jsLink">Cancel</span>
        
        									<div class="clear"></div>
        									<label for="email_addresses">Emails:</label>
        									<input type="text" name="email_addresses" value="">
        
        									<div class="email_container">
        										<?php for ($i = 0, $n = sizeof($data->email_addresses); $i < $n; $i++):?>
        											<div class="reportEmails"><input type="hidden" name="email_addresses[]" value="<?=$data->email_addresses[$i];?>"><?=$data->email_addresses[$i];?><span class="jsLink" onclick="xRptEmail(this);"><img src="/images/icons/16/69.png" alt="Remove"></span></div>
        										<?php endfor; ?>
        									</div>
        								</div>
        
        							</form>
        						</div>
        
        					</td>
        					<td width="5%" class="actions">
        						<a href="<?=$data->rlink;?>" title="View Report"><img src="/images/icons/16/70.png" alt="View Report"></a>
        						<span class="savedReportDelete jsLink" title="Delete report" data-id="<?=$data->id?>"><img src="/images/icons/16/110.png" alt="Delete report"></span>
        					</td>
        				</tr>
            <?php endforeach; ?>
        </tbody>
    </table>	
			
<?php else: ?>
    <p>
        No reports found
    </p>
<?php endif;?>


<script type="text/javascript">
      
$(document).ready(function() {

    $('#saved-reports-table').DataTable({
        "stateSave": true,
        "lengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]],
        "stateSaveCallback": function (settings, data) {
            $.ajax({
                "url": "/ajax/table_state_save/saved-reports-table",
                "data": data,
                "dataType": "json",
                "type": "POST",
                "success": function () {}    
            });
        },
        "stateLoadCallback": function (settings) {
            var o;
        
            $.ajax({
              "url": "/ajax/table_state_load/saved-reports-table",
              "async": false,
              "dataType": "json",
              "success": function (json) {
                o = json;
              }
            });
         
            return o;
        },             
        // i = number of results info 
        // f = search  
        "dom": 'R<"top"<"bulk-actions">f<"clear">>rt<"bottom"ilp<"clear">>'
    });
	
});

</script>
