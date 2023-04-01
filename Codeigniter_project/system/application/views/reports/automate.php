<div id ="saveReport_sch">
	<div id="saveReportMessage"></div>
	<form action="<?=site_url('reports')?>" method="post" name="schedule_reports" id="schedule_reports">

		<div class="row_dat">
			<div class="lbel">Report Name:</div>
			<div class="lbl_inpuCnt">
				<input type="text" name="report_name" id="report_name" value="<?=$report_info['report_name'];?>" />
			</div>
			<div  class="clear"></div>
		</div>
		<div class="row_dat">
			<div class="lbl_inpuCnt">Would you like to automate this report?
				<input type="radio" name="report_is_recursive" class="report_is_recursive_check" value="0"<?= $frequency>0 ? '' : ' checked="checked"'?> />No
				<input type="radio" name="report_is_recursive" class="report_is_recursive_check" value="1"<?= $frequency>0 ? ' checked="checked"' : ''?> />Yes
			</div>
			<div  class="clear"></div>
		</div>
		<?php
		$showAuto = $frequency>0 ? '' : ' hidden';
		$reportDate = strtotime($report_info['report_datetime']);
		$time = date('a', $reportDate);
		?>
		<div class="row_dat report_is_recursive_div<?=$showAuto;?>">
			<div class="lbel" id="report_schedule_label"><?=false ? 'Starting' : 'Report Schedule';?>:</div>
			<div class="lbl_inpuCnt">
				<input type="text" name="report_datetime" id="report_datetime" size="12" value="<?=date('m-d-Y', strtotime($report_info['report_datetime']))?>" />
				<?=renderHourDropDown('hh', date('g', $reportDate))?>:
				<?=renderMinuteDropDown('mm', date('i', $reportDate))?>:
				<select name="ampm" id="time">
					<option value="am" <?=$time === 'am' ? 'selected="selected"' : ''?> >am</option>
					<option value="pm" <?=$time === 'pm' ? 'selected="selected"' : ''?>>pm</option>
				</select>
			</div>
			<div  class="clear"></div>
		</div>
		<div class="row_dat report_is_recursive_div<?=$showAuto;?>">
			<div class="lbel">Recurring:</div>
			<div class="lbl_inpuCnt">
				<select name="report_recursive_frequency" id="report_recurring" onchange="markRecursive(this.value)">
					<option value="0">None</option>
					<option value="1"<?=$frequency == '1' ? 'selected="selected"' : ''?>>Every Day</option>
					<option value="7"<?=$frequency == '7' ? 'selected="selected"' : ''?>>Every Week</option>
					<option value="31"<?=$frequency == '31' ? 'selected="selected"' : ''?>>Every Month</option>
					<option value="365"<?=$frequency == '365' ? 'selected="selected"' : ''?>>Every Year</option>
				</select>
			</div>
			<div  class="clear"></div>
		</div>
		<div class="row_dat report_is_recursive_div<?=$showAuto;?>">
			<div class="lbel">Emails:</div>
			<div class="lbl_inpuCnt">
				<input type="text" name="email_addresses" id="email_addresses" value="" />

				<div class="email_container">
					<?php
					$emails = empty($report_info['email_addresses']) ? array() : explode(',', $report_info['email_addresses']);
					if ( ! empty($emails)):
						for ($i = 0, $n = count($emails); $i < $n; $i++): ?>
						<div class="reportEmails" id="rptE_<?=$i?>">
							<input type="hidden" name="email_addresses[]" value="<?=$emails[$i]?>" /><?=$emails[$i]?>
							<span class="jsLink" onclick="xRptEmail(this);">
								<img src="/images/icons/16/69.png" alt="Remove" class="imgIcon">
							</span>
						</div>
						<?php
						endfor;
					endif; ?>
				</div>
			</div>
			<div  class="clear"></div>
		</div>

		<input type="hidden" name='report_id' id="report_id" value="<?=isset($report_info['id']) ? $report_info['id'] : '';?>" />
		<input type="hidden" name="report_where" id="report_where" value="<?=(isset($report_info['report_where']) && is_array($report_info['report_where']) ? htmlentities(json_encode($report_info['report_where']), ENT_QUOTES) : '')?>" />
	</form>
</div>