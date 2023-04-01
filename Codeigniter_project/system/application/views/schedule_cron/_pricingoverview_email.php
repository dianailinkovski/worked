<?php
$tableHeadStyle = 'style="background:#E7E7E8;color:#000;"';
$tableCellStyle = 'style="border:1px solid #E7E6E5;height:20px;padding:2px 0 0 10px;text-align:left;"';
$solidLineStyle = 'style="background-color:#FBB925;height:1px;line-height:1px;overflow:hidden;"';
$h2Style = 'font-size:12px;width:580px;margin:12px 0 0 0';
?>
<div style="color:#666;font-family:Arial,Helvetica,sans-serif;min-height:400px;">

	<h2 style="<?=$h2Style?>">Who's Selling My Products Today</h2>

	<?php
//echo "HERE\n";
//print_r($marketplaces); exit;
	if (isset($marketplaces[0]) OR isset($retailers[0])):
		if (isset($marketplaces[0])):
			?>

			<table width="580" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;background:none repeat scroll 0 0 rgb(255, 255, 255);font-family:Arial,Helvetica,sans-serif;font-size:11px;margin-top:10px;">

				<thead>
					<tr <?=$tableHeadStyle;?>>
						<th <?=$tableCellStyle;?> width="40%">Marketplace</th>
						<th <?=$tableCellStyle;?> width="20%">Products</th>
						<th <?=$tableCellStyle;?> width="20%">Merchants</th>
						<th <?=$tableCellStyle;?> width="20%">Last Tracking</th>
					</tr>
				</thead>

				<tbody>

					<?php
					foreach ($marketplaces as $key => $data) {

						$class = ($key % 2 == 1) ? ' style="background:#E7E7E8"' : '';
						$crawl_info = !empty($last_crawl[$data['marketplace']]) ? $last_crawl[$data['marketplace']] : FALSE;
						$crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';
						?>

						<tr <?php echo $class;?>>
							<td <?=$tableCellStyle;?>><a href="<?php echo base_url() . 'whois/report_marketplace/' . $data['marketplace']?>"><?php echo $data['display_name'];?></a></td>
							<td <?=$tableCellStyle;?>><?=number_format($data['total_products']);?></td>
							<td <?=$tableCellStyle;?>><?=number_format($data['total_listing']);?></td>
							<td <?=$tableCellStyle;?>><?=$crawl_start?></td>
						</tr>

		<?php }?>

				</tbody>
			</table><br>

		<?php
		endif;
		if (isset($retailers[0])):
			?>

			<table width="580" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;background:none repeat scroll 0 0 rgb(255, 255, 255);font-family:Arial,Helvetica,sans-serif;font-size:11px;margin-top:10px;">

				<thead>
					<tr <?=$tableHeadStyle;?>>
						<th <?=$tableCellStyle;?> width="40%">Retailer</th>
						<th <?=$tableCellStyle;?> width="40%">Products</th>
						<th <?=$tableCellStyle;?> width="20%">Last Tracking</th>
					</tr>
				</thead>

				<tbody>

					<?php
					foreach ($retailers as $key => $data) {

						$class = ($key % 2 == 1) ? ' style="background:#E7E7E8"' : '';
						$crawl_info = !empty($last_crawl[$data['marketplace']]) ? $last_crawl[$data['marketplace']] : FALSE;
						$crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';
						?>

						<tr <?php echo $class;?>>
							<td <?=$tableCellStyle;?>><a href="<?php echo base_url() . 'whois/report_merchant/' . $data['marketplace'] . '/' . $data['id']?>"><?php echo $data['display_name'];?></a></td>
							<td <?=$tableCellStyle;?>><?php echo number_format($data['total_products']);?></td>
							<td <?=$tableCellStyle;?>><?php echo $crawl_start?></td>
						</tr>

		<?php }?>

				</tbody>
			</table>

		<?php
		endif;
	else:
		?>
		<p>No record found.</p><?php
endif;?>
</div>
