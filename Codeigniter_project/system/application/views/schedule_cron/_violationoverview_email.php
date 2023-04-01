<?php
$tableHeadStyle = 'style="background:#E7E7E8;color:#000;"';
$tableCellStyle = 'style="border:1px solid #E7E6E5;height:20px;padding:2px 0 0 10px;text-align:left;"';
$solidLineStyle = 'style="background-color:#FBB925;height:1px;line-height:1px;overflow:hidden;"';
$h2Style = 'font-size:12px;width:580px;margin:12px 0 0 0';
?>
<body>
<div style="color:#666;font-family:Arial,Helvetica,sans-serif;min-height:400px;">
	<table border="0" cellpadding="0" cellspacing="0" width="580">
    <tr>
    	<td width="515" align="left">
			<img src="<?=frontImageUrl().'nav/sticky-vision.png'?>" alt="Logo" />
				<?php if(isset($merchant_logo) && !empty($merchant_logo)):?> <img src="<?=$merchant_logo;?>" alt="<?=$brand_name;?>" /><?php endif;?>
			</td>
    	<td width="65" align="right">
				<span style="font-size:23px;font-family:'Times New Roman', Times, serif;color:#6a696e;">REPORTING</span><br />
				<span style="font-size:9px;color:#6a696e;font-family:Arial, Helvetica, sans-serif;font-weight:bold;"><?=$headerDate;?></span>
			</td>
		</tr>
		<tr>
			<td colspan="2" <?=$solidLineStyle;?>>&nbsp;</td>
		</tr>
		<tr>
			<td height="10" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2" style="color:#fff;padding:5px;font-size:12px;font-weight:bold;" bgcolor="#00a0d1"><?=$title;?></td>
		</tr>
	</table>

	<h2 style="<?=$h2Style;?>">Violations By Marketplace</h2><?php
	if ( ! empty($violatedMarketplaces)): ?>
	<table width="580" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;background:none repeat scroll 0 0 rgb(255, 255, 255);font-family:Arial,Helvetica,sans-serif;font-size:11px;">
    <tr <?=$tableHeadStyle;?>>
      <th <?=$tableCellStyle;?> width="40%">Marketplace</th>
      <th <?=$tableCellStyle;?> width="20%">Products</th>
      <th <?=$tableCellStyle;?> width="20%">Violations</th>
      <th <?=$tableCellStyle;?> width="20%">Last Tracking</th>
    </tr><?php
		$i = 1;
		foreach ($marketplaces as $data):
			$class = (($i++ % 2) == 0) ? ' style="background:#E7E7E8"': '';
			$marketIndex = strtolower($data['marketplace']);
			if ( ! empty($violatedMarketplaces[$marketIndex])):
				$crawl_info = ! empty($last_crawl[$marketIndex]) ? $last_crawl[$marketIndex] : FALSE;
				$crawl_start = ! empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : ''; ?>
		<tr<?=$class;?>>
			<td <?=$tableCellStyle;?>><a href="<?php echo site_url('violationoverview/report_marketplace/'.$marketIndex) ?>"><?php echo $data['display_name'] ?></a></td>
			<td <?=$tableCellStyle;?>><?php echo number_format($data['total_products']);?></td>
			<td <?=$tableCellStyle;?>><?php echo (isset($market_violations[$marketIndex]))?$market_violations[$marketIndex]:0; ?></td>
			<td <?=$tableCellStyle;?>><?php echo $crawl_start ?></td>
		</tr><?php
			endif;
		endforeach; ?>
	</table><?php
	else: ?>
	<p>No Marketplace Violations</p><?php
	endif; ?>

	<h2 style="<?=$h2Style;?>">Violations By Retailer</h2><?php
	if( ! empty($violatedRetailers)): ?>
	<table width="580" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;background:none repeat scroll 0 0 rgb(255, 255, 255);font-family:Arial,Helvetica,sans-serif;font-size:11px;">
    <tr <?=$tableHeadStyle;?>>
      <th <?=$tableCellStyle;?> width="40%">Retailer</th>
      <th <?=$tableCellStyle;?> width="20%">Products</th>
      <th <?=$tableCellStyle;?> width="20%">Violations</th>
      <th <?=$tableCellStyle;?> width="20%">Last Tracking</th>
    </tr><?php
		$i=1;
		foreach ($retailers as $data):
			$class = (($i++ % 2) == 0) ? ' style="background:#E7E7E8"': '';
			$marketIndex = strtolower($data['marketplace']);
			if ( ! empty($violatedRetailers[$marketIndex])):
				$crawl_info = ! empty($last_crawl[$marketIndex]) ? $last_crawl[$marketIndex] : FALSE;
				$crawl_start = ! empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : ''; ?>
		<tr<?=$class;?>>
			<td <?=$tableCellStyle;?>><a href="<?php echo site_url('violationoverview/report_marketplace/'.$marketIndex) ?>"><?php echo $data['display_name'] ?></a></td>
			<td <?=$tableCellStyle;?>><?php echo number_format($data['total_products']);?></td>
			<td <?=$tableCellStyle;?>><?php echo (isset($market_violations[$marketIndex]))?$market_violations[$marketIndex]:0; ?></td>
			<td <?=$tableCellStyle;?>><?php echo $crawl_start ?></td>
		</tr><?php
			endif;
		endforeach; ?>
	</table><?php
	else: ?>
	<p>No Retailer Violations.</p><?php
	endif; ?>

  <h2 style="<?=$h2Style;?>">Price Violators</h2><?php
	if(count($priceViolators) > 0) { ?>
	<table width="580" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;background:none repeat scroll 0 0 rgb(255, 255, 255);font-family:Arial,Helvetica,sans-serif;font-size:11px;">
    <tr <?=$tableHeadStyle;?>>
      <th <?=$tableCellStyle;?> width="50%">Merchant</th>
      <th <?=$tableCellStyle;?> width="50%">Product Violations</th>
    </tr><?php
		$i=1;
	  foreach ($priceViolators as $key => $data) {
			$class = (($i++ % 2) == 0) ? ' style="background:#E7E7E8"': '';
			$data = $data['crowl_merchant'];
			$name = (!empty($data['original_name']) && $data['original_name'] != NULL) ? $data['original_name']: $data['merchant_name']; ?>
		<tr<?=$class;?>>
			<td <?=$tableCellStyle;?>><a href="<?=public_url('violationoverview/violator_report/'.$data['id'])?>"><?php echo trim($name);?></a></td>
		  <td <?=$tableCellStyle;?>><?php echo $priceViolators[$key]['total_violations'];?></td>
		</tr><?php
		} ?>
  </table><?php
	} else { ?>
	<p>No records found.</p><?php
	} ?>

	<h2 style="<?=$h2Style;?>">Violated Products</h2><?php
	if (count($violatedProducts) > 0) { ?>
	<table width="580" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;background:none repeat scroll 0 0 rgb(255, 255, 255);font-family:Arial,Helvetica,sans-serif;font-size:11px;">
		<tr <?=$tableHeadStyle;?>>
			<th <?=$tableCellStyle;?> width="40%">Title</th>
			<th <?=$tableCellStyle;?> width="15%">UPC</th>
			<th <?=$tableCellStyle;?> width="15%">Retail</th>
			<th <?=$tableCellStyle;?> width="15%">Wholesale</th>
			<th <?=$tableCellStyle;?> width="15%">MAP</th>
		</tr><?php
		$i=1;
		foreach ($violatedProducts as $key => $data) {
			$class = (($i++ % 2) == 0) ? ' style="background:#E7E7E8"': ''; ?>
		<tr<?=$class;?>>
			<td <?=$tableCellStyle;?>><a href="<?=public_url('violationoverview/violated_product/'.$data['id'])?>"><?=html_entity_decode($data['title']); ?></a></td>
			<td <?=$tableCellStyle;?>><?=$data['upc_code']; ?></td>
			<td <?=$tableCellStyle;?>><?='$'.$data['retail_price']; ?></td>
			<td <?=$tableCellStyle;?>><?='$'.$data['wholesale_price']; ?></td>
			<td <?=$tableCellStyle;?>><?='$'.$data['price_floor']; ?></td>
		</tr><?php
		} ?>
	</table><?php
	} else { ?>
	<p>No record found.</p><?php
	} ?>

	<table border="0" cellpadding="0" cellspacing="0" width="580">
		<tr>
			<td height="10">&nbsp;</td>
		</tr>
		<tr>
			<td width="580" <?=$solidLineStyle;?>>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><?='&copy;'.date('Y').' Sticky Business, LLC - www.juststicky.com';?></td>
		</tr>
	</table>
</div>
</body>