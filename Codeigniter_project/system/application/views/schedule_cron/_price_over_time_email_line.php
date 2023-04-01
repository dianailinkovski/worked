<?php
$style = 'style="border: 1px solid #E7E6E5;height: 20px;padding: 2px 0 0 10px;text-align: left;vertical-align: top;"';
$j = 0;
foreach($Data as $prodId=>$productData):
	$color = Color_handler::get_next($j++)->get_hex(); ?>
	<div style="margin-bottom:10px;">
		<span class="squareKey" style="background-color:<?=$color . '; ' . $square?>"></span>
		<b><?=getProductsTitle($prodId)?></b><br>
	</div>
	<?php
	if( ! empty($marketplacesExist[$prodId])): ?>
	<table border="0" bordercolor="#CCCCCC" cellpadding="3" cellspacing="0" width="100%" style=" border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
		<thead>
			<tr style="background:#E7E7E8; height:30px; color:#000; vertical-align: top">
				<th width="200" align="left" <?=$style?>>Marketplace</th>
				<th width="160" align="left" <?=$style?>>Date</th>
				<th width="160" align="left" <?=$style?>>Wholesale</th>
				<th width="140" align="left" <?=$style?>>Retail</th>
				<th width="140" align="left" <?=$style?>>MAP</th>
				<th width="140" align="left" <?=$style?>>Avg Price</th>
			</tr>
		</thead>
		<tbody><?php
			foreach($productData as $marketName=>$pricing):
				if ($marketRetailer[$marketName]) continue;
				for($i=0, $n=sizeof($pricing); $i<$n; $i++):
					$class = '';
					if (($i % 2) == 0) $class = 'style="background:#E7E7E8"'; ?>
			<tr <?php echo $class; ?>>
				<td <?=$style?>><a href="<?=public_url('reports/market/'.$pricing[$i]['marketplace'].'/'.$prodId.'/'.date('Y-m-d', $pricing[$i]['dt']) . '/' . $url_modifier)?>" target="_blank"><?=ucfirst($pricing[$i]['marketplace'])?></a></td>
				<td <?=$style?>><?=date('m/d/Y', $pricing[$i]['dt'])?></td>
				<td <?=$style?>>$<?=number_format($pricing[$i]['wholesale'], 2)?></td>
				<td <?=$style?>>$<?=number_format($pricing[$i]['retail'], 2)?></td>
				<td <?=$style?>><?php if(!empty($pricing[$i]['map']) && $pricing[$i]['map'] != 0 && $pricing[$i]['map'] != 0.00):?>$<?=number_format($pricing[$i]['map'], 2); endif;?></td>
				<td <?=$style?>>$<?=number_format($pricing[$i]['price'], 2)?></td>
			</tr><?php
				endfor;
			endforeach; ?>
		</tbody>
	</table>
	<br /><?php
	endif;

	if( ! empty($retailersExist[$prodId])): ?>
	<table border="0" bordercolor="#CCCCCC" cellpadding="3" cellspacing="0" width="100%" style="border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
		<thead>
			<tr style="background:#E7E7E8; height:30px; color:#000; vertical-align: top">
				<th width="200" align="left" <?=$style?>>Retailer</th>
				<th width="160" align="left" <?=$style?>>Date</th>
				<th width="160" align="left" <?=$style?>>Wholesale</th>
				<th width="140" align="left" <?=$style?>>Retail</th>
				<th width="140" align="left" <?=$style?>>MAP</th>
				<th width="140" align="left" <?=$style?>>Avg Price</th>
			</tr>
		</thead>
		<tbody><?php
			foreach($productData as $marketName=>$pricing):
				if ( ! $marketRetailer[$marketName]) continue;
				for($i=0, $n=sizeof($pricing); $i<$n; $i++):
					$class = '';
					if (($i % 2) == 0) $class = 'style="background:#E7E7E8"'; ?>
			<tr <?=$class?>>
				<td <?=$style?>><a href="<?=public_url('reports/market/'.$pricing[$i]['marketplace'].'/'.$prodId.'/'.date('Y-m-d', $pricing[$i]['dt']) . '/' . $url_modifier)?>" target="_blank"><?=ucfirst($pricing[$i]['marketplace'])?></a></td>
				<td <?=$style?>><?=date('m/d/Y', $pricing[$i]['dt'])?></td>
				<td <?=$style?>>$<?=number_format($pricing[$i]['wholesale'], 2)?></td>
				<td <?=$style?>>$<?=number_format($pricing[$i]['retail'], 2)?></td>
				<td <?=$style?>><?php if(!empty($pricing[$i]['map']) && $pricing[$i]['map'] != 0 && $pricing[$i]['map'] != 0.00):?>$<?=number_format($pricing[$i]['map'], 2); endif;?></td>
				<td <?=$style?>>$<?=number_format($pricing[$i]['price'], 2)?></td>
			</tr><?php
				endfor;
			endforeach; ?>
		</tbody>
	</table><?php
	endif;
endforeach;
