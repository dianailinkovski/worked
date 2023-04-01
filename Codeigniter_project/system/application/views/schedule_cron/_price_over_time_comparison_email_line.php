<?php
$style = 'style="border: 1px solid #E7E6E5;height: 20px;padding: 2px 0 0 10px;text-align: left;vertical-align: top;"';
foreach($Data as $prodId=>$productData):
	$color = Color_handler::get_next($color_index[$prodId])->get_hex();
	$compWidth = '60';
	$brandWidth = '40'; ?>

	<div class="key_container">
		<div class="search-result" rel="<?=$prodId?>" style="float:left; width:<?=$compWidth?>%;">
			<div style="margin-bottom:10px;" class="product_save">
				<span style="background-color:<?=$color?>; <?=$square?>"></span>
				Competitor Product: <b><?=getProductsTitle($prodId)?></b><br>
			</div>
		</div><?php
		if (isset($comparison_data[$prodId])):
			$competitor_map_id = $competitor_map[$prodId]['id'];
			$competitor_map_color = Color_handler::get_next($color_index[$competitor_map_id])->get_hex(); ?>

		<div class="search-result" rel="<?=$competitor_map_id?>" style="float:left; width:<?=$brandWidth?>%;">
			<div style="margin-bottom:10px;" class="product_save">
				<span style="background-color:<?=$competitor_map_color?>; <?=$square?>"></span>
				Our Product: <b><?=getProductsTitle($competitor_map_id)?></b><br>
			</div>
		</div>
		<?php endif; ?>
		<div class="clear"></div>
	</div>

	<?php
	if( ! empty($marketplacesExist[$prodId])): ?>
	<table border="0" bordercolor="#CCCCCC" cellpadding="3" cellspacing="0" width="100%" style=" border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
		<thead>
			<tr style="background:#E7E7E8; height:30px; color:#000; vertical-align: top">
				<th width="40%" align="left" <?=$style?>>Marketplace</th>
				<th width="9%" align="left" <?=$style?>>Date</th>

				<!-- we're not showing competitor configured pricing - just marketplace pricing -->
				<th width="11%" align="left" <?=$style?>>Their Avg Price</th>

				<!-- our pricing -->
				<th width="11%" align="left" <?=$style?>>Our Avg Price</th>
				<th width="9%" align="left" <?=$style?>>Our Retail</th>
				<th width="11%" align="left" <?=$style?>>Our Wholesale</th>
				<th width="9%" align="left" <?=$style?>>Our MAP</th>
			</tr>
		</thead>
		<tbody><?php
			foreach($productData as $marketName=>$pricing){
				if ($marketRetailer[$marketName]) continue;
					for($i=0, $n=sizeof($pricing); $i<$n; $i++){
						$class = '';
						if (($i % 2) == 0) $class = 'style="background:#E7E7E8"'; ?>
			<tr <?php echo $class; ?>>
				<td <?=$style?>><a href="<?=public_url('reports/market/'.$pricing[$i]['marketplace'].'/'.$prodId.'/'.date('Y-m-d', $pricing[$i]['dt']) . '/' . $url_modifier)?>" target="_blank"><?=ucfirst($pricing[$i]['marketplace']);?></a></td>
				<td <?=$style?>><?=date('m/d/Y', $pricing[$i]['dt'])?></td>
				<td <?=$style?>>$<?=number_format($pricing[$i]['price'], 2)?></td>

				<?php
				$comparison = false;
				if(isset($comparison_data[$prodId][$marketName][$pricing[$i]['dt']])):
					$comparison = $comparison_data[$prodId][$marketName][$pricing[$i]['dt']];
				endif; ?>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['price'], 2)?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['retail'], 2)?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['wholesale'], 2)?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?><?php if(!empty($comparison['map']) && $comparison['map'] != 0 && $comparison['map'] != 0.00):?>$<?=number_format($comparison['map'], 2); endif;?><?php endif; ?></td>
			</tr><?php
				}
			}
		?>
		</tbody>
	</table>
	<br /><?php
	endif;

	if( ! empty($retailersExist[$prodId])): ?>
	<table border="0" bordercolor="#CCCCCC" cellpadding="3" cellspacing="0" width="100%" style="border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
		<thead>
			<tr style="background:#E7E7E8; height:30px; color:#000; vertical-align: top">
				<th align="left" width="40%" <?=$style?>>Retailer</th>
				<th align="left" width="9%" <?=$style?>>Date</th>

				<!-- we're not showing competitor configured pricing - just marketplace pricing -->
				<th align="left" width="11%" <?=$style?>>Their Avg Price</th>

				<!-- our pricing -->
				<th align="left" width="11%" <?=$style?>>Our Avg Price</th>
				<th align="left" width="9%" <?=$style?>>Our Retail</th>
				<th align="left" width="11%" <?=$style?>>Our Wholesale</th>
				<th align="left" width="9%" <?=$style?>>Our MAP</th>
			</tr>
		</thead>
		<tbody><?php
			foreach($productData as $marketName=>$pricing){
				if ( ! $marketRetailer[$marketName]) continue;
					for($i=0, $n=sizeof($pricing); $i<$n; $i++){
						$class = '';
						if (($i % 2) == 0) $class = 'style="background:#E7E7E8"'; ?>
			<tr <?php echo $class; ?>>
				<td <?=$style?>><a href="<?=public_url('reports/market/'.$pricing[$i]['marketplace'].'/'.$prodId.'/'.date('Y-m-d', $pricing[$i]['dt']) . '/' . $url_modifier)?>" target="_blank"><?=ucfirst($pricing[$i]['marketplace']);?></a></td>
				<td <?=$style?>><?=date('m/d/Y', $pricing[$i]['dt']);?></td>
				<td <?=$style?>>$<?=number_format($pricing[$i]['price'], 2);?></td>

				<?php
				$comparison = false;
				if(isset($comparison_data[$prodId][$marketName][$pricing[$i]['dt']])):
					$comparison = $comparison_data[$prodId][$marketName][$pricing[$i]['dt']];
				endif; ?>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['price'], 2);?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['retail'], 2);?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['wholesale'], 2);?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?><?php if(!empty($comparison['map']) && $comparison['map'] != 0 && $comparison['map'] != 0.00):?>$<?=number_format($comparison['map'], 2); endif;?><?php endif; ?></td>
			</tr><?php
				}
			} ?>
		</tbody>
	</table><?php
	endif;
endforeach;