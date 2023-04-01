<?php 

$incCols = array();

switch($report_type)
{
	case 'dns_list':
		$incCols = array(0, 1, 2, 3, 4, 5);
	default:
		$incCols = 'all';
		break;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body style="padding-bottom: 50px;">
<div style=" margin:0 auto; width:760px; color:#666; font-family:Arial, Helvetica, sans-serif">
	<table border="0" cellpadding="0" cellspacing="0" width="760">
		<tr>
			<td width="515" align="left">
				<table cellpadding="0" cellspacing="5" width="465" border="0">
					<tr>
						<td width="175" valign="bottom"><img src="<?=frontImageUrl()?>trackstreet_logo_white_293w.png" alt="Yellow" width="293" height="42" /></td>
						<td valign="top"><?php if(isset($merchant_logo) && !empty($merchant_logo)):?> <img src="http://<?php echo $this->config->item('s3_bucket_name').'/stickyvision/brand_logos/'.$merchant_logo;?>" /><?php endif;?></td>
					</tr>
				</table>
			</td>
			<td width="145" align="left">
				<table cellpadding="0" cellspacing="0" width="145">
					<tr>
						<td height="10">&nbsp;</td>
					</tr>
					<tr>
						<td align="right">
							<span style="font-size:60px;font-family: 'Times New Roman', Times, serif;color:#6a696e;">REPORTING</span><br />
							<span style="font-size:21px;color:#6a696e; font-family:Arial, Helvetica, sans-serif; font-weight:bold;"><?php echo 'Dates: '.$headerDate;?></span>
						</td>
					</tr>
					<tr>
						<td align="right" style="font-size:32px;font-family:Arial, Helvetica, sans-serif"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width="660" style="height:8px; border-bottom: yellow;" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td height="10" colspan="2"></td>
		</tr>
		<tr>
			<td colspan="2" style="font-size:35px;color:#fff; padding:5px;" bgcolor="#00a0d1">
				<table cellspacing="0" cellpadding="0" width="760">
						<tr>
							<td width="10">&nbsp;</td>
							<td width="640"><?=$title?></td>
							<td width="10">&nbsp;</td>
						</tr>
					</table>
			</td>
		</tr>
		<tr>
			<td height="10" colspan="2"></td>
		</tr><?php
		if($graph_image_name):
		?>
		<tr>
			<td colspan="2" style="padding:10px 0"><img src="http://<?=$this->config->item('s3_bucket_name').'/stickyvision/graph_images/'.$this->store_id.'/'.$graph_image_name;?>" width="760" /></td>
		</tr><?php
		endif; ?>
	</table><?php
	foreach($report as $rep){ ?>
	<p style="margin:0px 0; color:#000; font-size:30px;"><?=$rep['title'];?></p>
	<table cellpadding="2" cellspacing="0" width="100%" style=" background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:20px; color:#666666;"><?php
		$cnt = 0;
		foreach($rep['table'] as $key_ => $table) {
			$selector = ($key_ == 0) ? 'th' : 'td';
			foreach($this->mvexport->getContentRow($table) as $tr) {
				$columns = $this->mvexport->getRowColumns($tr, $selector);
				$columns_count = count($columns);

				if($columns_count > 0) {
					$bgColor = ($cnt % 2 == 0) ? 'bgcolor="#E7E7E8"' : '';
					$attributes = ($cnt == 0) ? 'style="font-size:25px; font-weight:bold; border:1px solid #CCC; color:#000"' : '';
					$cnt++;
					echo '<tr '.$bgColor.' '.$attributes.'>';

					$tr_str = '';
					$counter = 0;
					
					foreach ($columns as $index => $td)
          {
						$txt = $td->innertext();
						
						if ($key_ == 0)
            {
						    //header cells - populate widths
						    $tr_str = '<'.$selector.' style="border:1px solid #E7E6E5;height:8px;text-align:left;" >'.$txt.'</'.$selector.'>';
						}
						else
            {
							if ($counter == 0 && $time_frame != '24')
              {
								//link to daily report
								if ($anchor = $td->find('a', 0))
                {
									$aHref = $anchor->getAttribute('href');
									$sText  = strip_tags($txt);
									$txt = '<a href="'.$aHref.'">'.$sText."</a>";
								}
							}
							elseif ($counter == 1 && $time_frame == '24')
              {
								//link to merchant details
								if ($anchor = $td->find('a', 0))
                {
									$aHref = $anchor->getAttribute('href');
									$sText  = strip_tags($txt);
									$txt = '<a href="'.$aHref.'">'.$sText."</a>";
								}
							}
							elseif ($counter == 9 && $time_frame == '24')
              {
								//last column shows product listing
								if ($anchor = $td->find('a', 0))
                {
									$aHref = $anchor->getAttribute('href');
									$sText  = strip_tags($txt);
									$txt = '<a href="'.$aHref.'">'.$sText."</a>";
								}
							}
							
							$txt = str_replace(array('height="16"', 'width="16"', 'padding: 0px; margin-top: 1px'), array('height="8"', 'width="8"', ''), $txt);
							
							$tr_str = '<'.$selector.'>'.$txt.'</'.$selector.'>';
						}
						
						echo $tr_str;
						
						//$counter++;
						
						if ($incCols != 'all' && !in_array($index, $incCols))
						{
							$counter++;
							
							continue;
						}
					}
					echo '</tr>';
				}
			}
		} 
		?>
		<tr>
		    <td colspan="6" height="10"></td>
		</tr>
	</table>
<?php 
}

$this->mvexport->clear();

?>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td height="10"></td>
		</tr>
		<tr>
			<td width="660" style="height:8px; display:block; border-bottom: yellow;">&nbsp;</td>
		</tr>
	</table>
</div>
<p>
    Report produced on <?php echo date('m-d-Y'); ?>
</p>
</body>
</html>