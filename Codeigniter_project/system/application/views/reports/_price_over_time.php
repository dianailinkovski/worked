<?php
if(isset($Data)):
	if (count($Data) > 0):
		echo $this->load->view('reports/_price_over_time_' . $report_chart, '', TRUE);
	else: ?>
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable exportable">
		<tr><td align="center"><?=$noRecord?></td></tr>
	</table>
	<?php
	endif;
endif;
