<?php
$tableCellStyle = 'style="border:1px solid #E7E6E5;height:20px;padding:2px 0 0 10px;text-align:left;"';
$solidLineStyle = 'style="background-color:#FBB925;height:1px;line-height:1px;overflow:hidden;"';
?>
<body>
<div style="color:#666;font-family:Arial,Helvetica,sans-serif;min-height:400px;">
	<table border="0" cellpadding="0" cellspacing="0" width="580">
		<tr>
			<td width="515" align="left">
				<img src="<?=$this->config->item('public_base_url').'images/nav/sticky-vision.png';?>" alt="Logo" />
				<?php if( ! empty($merchant_logo)):?> <img src="<?=$merchant_logo;?>" alt="<?=$brand_name;?>" /><?php endif;?>
			</td>
    	<td width="65" align="right">
				<span style="font-size:23px;font-family:'Times New Roman', Times, serif;color:#6a696e;">REPORTING</span><br />
				<span style="font-size:9px;color:#6a696e;font-family:Arial, Helvetica, sans-serif;font-weight:bold;"><?='Dates:'.$headerDate;?></span>
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
	<table width="580" cellspacing="0" cellpadding="3" border="1" style="border-collapse:collapse;background:none repeat scroll 0 0 rgb(255, 255, 255);font-family:Arial,Helvetica,sans-serif;font-size:11px;">
		<tr style="background:#E7E7E8;color:#000;">
			<th width="180" <?=$tableCellStyle;?>>UPC</th>
			<th width="400" <?=$tableCellStyle;?>>Product Name</th>
		</tr><?php
		$i = 1;
		$class = '';
		foreach($upc_list as $upc) {
			if (($i++ % 2) == 0)
				$class = ' style="background:#E7E7E8"';
			else
				$class = '';
			?>
		<tr<?=$class;?>>
			<td <?=$tableCellStyle;?>><a href="<?=base_url();?>prod_management/index/<?=$store_id;?>" target="_blank"><?=$upc['upc_code']?></a></td>
			<td <?=$tableCellStyle;?>><?=$upc['title']?></td>
		</tr><?php }?>
	</table>
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