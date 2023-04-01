<table border="0" cellpadding="" cellspacing="" width="100%" style="margin:20px">
	<tr>
		<td>
			<?php if ( ! empty($message)): ?>
				<p><?=$message?></p>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php
$style = ' style="border: 1px solid #E7E6E5;height: 20px;padding: 2px 0 0 10px;text-align: left;vertical-align: top;"'; ?>
<h3 style="font-size:14px;line-height:20px;margin:0;padding:5px 0 10px"><?=$merchant?> Violations</h3>
<table border="0" bordercolor="#CCCCCC" cellpadding="3" cellspacing="0" width="100%" style=" border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
	<thead>
		<tr style="background:#E7E7E8; height:30px; color:#000; vertical-align: top">
			<th width="80" align="left" <?=$style?>>Marketplace</th>
			<th width="80" align="left" <?=$style?>>Date</th>
			<th width="80" align="left" <?=$style?>>Time</th>
			<th width="95" align="left" <?=$style?>>Name</th>
			<th width="95" align="left" <?=$style?>>UPC</th>
			<th width="75" align="left" <?=$style?>>Retail</th>
			<th width="75" align="left" <?=$style?>>MAP</th>
			<th width="75" align="left" <?=$style?>>Price</th>
			<th width="100" align="left" <?=$style?>>URL</th>
		</tr>
	</thead>
	<tbody><?php
	$i = 0;
	foreach ($productData as $product):
		$class = '';
		if (($i++ % 2) == 0)
			$class = 'style="background:#E7E7E8"'; ?>
			<tr <?php echo $class;?>>
				<td<?=$style?>><?=ucfirst($product['marketplace']);?></td>
				<td<?=$style?>><?=date('m/d/Y', $product['timestamp']);?></td>
				<td<?=$style?>><?=date('h:i A', $product['timestamp']);?></td>
				<td<?=$style?>><?=$product['title'];?></td>
				<td<?=$style?>><?=$product['upc_code'];?></td>
				<td<?=$style?>>$<?=number_format($product['retail'], 2);?></td>
				<td<?=$style?>><?php if (!empty($product['map']) && $product['map'] != 0 && $product['map'] != 0.00):?>$<?=number_format($product['map'], 2);?><?php endif;?></td>
				<td<?=$style?>>$<?=number_format($product['price'], 2);?></td>
				<td <?=$style?>><a href="<?=$product['url'];?>" target="_blank"><?=extractDomainByURL($product['url']);?></a></td>
			</tr><?php
	endforeach; ?>
	</tbody>
</table>