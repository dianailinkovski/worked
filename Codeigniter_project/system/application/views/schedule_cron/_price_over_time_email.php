<?php
if(isset($Data)):
	if (count($Data) > 0):
		$square = 'display: inline-block; border-radius: 2px; width: 10px; height: 10px; margin: 0 2px 0 0;';
		$comparison_view = $show_comparison ? 'comparison_' : '';
		$view = 'schedule_cron/_price_over_time_' . $comparison_view . 'email_' . $report_chart;
		echo $this->load->view($view, array('square' => $square), TRUE);
	else: ?>
	<table cellpadding="0" cellspacing="0" width="100%" border="0" style="border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
		<tr><td align="center"><?=$noRecord?></td></tr>
	</table>
	<?php
	endif;
endif;
