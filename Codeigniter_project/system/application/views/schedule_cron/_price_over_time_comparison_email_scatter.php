<?php
$style = 'style="border: 1px solid #E7E6E5;height: 20px;padding: 2px 0 0 10px;text-align: left;vertical-align: top;"';
foreach($Data as $prodId=>$productData):
	$color = Color_handler::get_next($color_index[$prodId])->get_hex();
	$compWidth = '57';
	$brandWidth = '43'; ?>

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
				<th width="10%" align="left" <?=$style?>>Marketplace</th>
				<th width="18%" align="left" <?=$style?>>Merchant</th>
				<th width="7%" align="left" <?=$style?>>Date</th>
				<th width="7%" align="left" <?=$style?>>Time</th>
				<!-- competitor pricing -->
				<th width="9%" align="left" <?=$style?>>URL</th>
				<th width="6%" align="left" <?=$style?>>Price</th>

				<!-- our pricing -->
				<th width="6%" align="left" <?=$style?>>Price</th>
				<th width="6%" align="left" <?=$style?>>Retail</th>
				<th width="9%" align="left" <?=$style?>>Wholesale</th>
				<th width="6%" align="left" <?=$style?>>MAP</th>
				<th width="8%" align="left" <?=$style?>>Violation</th>
				<th width="8%" align="left" <?=$style?>>URL</th>
			</tr>
		</thead>
		<tbody><?php
			for($i=0, $n=sizeof($productData); $i<$n; $i++){
				$marketName = strtolower($productData[$i]['marketplace']);
				if ($marketRetailer[$marketName]) continue;
				$date = $productData[$i]['dt'];
				$crawl_id = $productData[$i]['crawl_id'];
				$merchant_id = $productData[$i]['merchant_id'];
				$class = '';
				if (($i % 2) == 0) $class = 'style="background:#E7E7E8"'; ?>
			<tr <?php echo $class; ?>>
				<td <?=$style?>><?=ucfirst($productData[$i]['marketplace']);?></td>
				<td <?=$style?>>
					<?php
					$merchantName = getMerchantName($productData[$i]['merchant_id']);
					if($merchantName == 'livamed') $merchantName = 'Livamed.com';
					if($merchantName == 'vitacost') $merchantName = 'Vitacost.com';
					if(strtolower($productData[$i]['marketplace'])=='amazon' && $merchantName != 'Amazon.com'){
						echo '<a href="http://www.amazon.com/gp/aag/details/ref=aag_m_ss?ie=UTF8&isAmazonFulfilled=&marketplaceID=ATVPDKIKX0DER&isCBA=&asin=&seller='.$productData[$i]['merchant_id'].'" target="_blank">'.$merchantName.'</a>';
					}else{
						echo $merchantName;
					}
					?>
				</td>
				<td <?=$style?>><?=date('m/d/Y', $date);?></td>
				<td <?=$style?>><?=date('h:i A', $date);?></td>

				<!-- competitor price -->
				<td <?=$style?>><a href="<?=$productData[$i]['url']?>" target="_blank">View Listing</a></td>
				<td <?=$style?>>$<?=number_format($productData[$i]['price'], 2)?></td>


				<?php
					$comparison = false;
					if(isset($comparison_data[$prodId][$marketName][$crawl_id][$merchant_id])):
						$comparison = $comparison_data[$prodId][$marketName][$crawl_id][$merchant_id];
						$merchantName = getMerchantName($comparison['merchant_id']);
					endif; ?>
					<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['price'], 2)?><?php endif; ?></td>
					<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['retail'], 2)?><?php endif; ?></td>
					<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['wholesale'], 2)?><?php endif; ?></td>
					<td <?=$style?>><?php if($comparison): ?><?php if(!empty($comparison['map']) && $comparison['map'] != 0 && $comparison['map'] != 0.00):?>$<?=number_format($comparison['map'], 2); endif;?><?php endif; ?></td>
					<td <?=$style?>>
						<?php if($comparison):
						//until we start keeping correct map values in dynamo - just grab from google data
						$map = $comparison['map'];
						if($comparison['price'] < $map){
							$violation = '<img src="'.frontImageUrl().'floor.png" alt="" style="margin-right:5px;"> LOW';
							//for non-violation reports we - don't really have the shot value from the violations table
							$shot = get_violation_image($comparison);
							if ( ! empty($shot))
								$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

							echo $violation;
						}
						endif; ?>
					</td>
					<td <?=$style?>><?php if($comparison): ?><a href="<?=$comparison['url']?>" target="_blank">View Listing</a><?php endif; ?></td>
			</tr><?php
			} ?>
		</tbody>
	</table>
	<br /><?php
	endif;

	if( ! empty($retailersExist[$prodId])): ?>
	<table border="0" bordercolor="#CCCCCC" cellpadding="3" cellspacing="0" width="100%" style="border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
		<thead>
			<tr style="background:#E7E7E8; height:30px; color:#000; vertical-align: top">
				<th align="left" width="10%" <?=$style?>>Retailer</th>
				<th align="left" width="18%" <?=$style?>>Merchant</th>
				<th align="left" width="7%" <?=$style?>>Date</th>
				<th align="left" width="7%" <?=$style?>>Time</th>
				<!-- competitor pricing -->
				<th align="left" width="9%" <?=$style?>>URL</th>
				<th align="left" width="6%" <?=$style?>>Price</th>

				<!-- our pricing -->
				<th align="left" width="6%" <?=$style?>>Price</th>
				<th align="left" width="6%" <?=$style?>>Retail</th>
				<th align="left" width="9%" <?=$style?>>Wholesale</th>
				<th align="left" width="6%" <?=$style?>>MAP</th>
				<th align="left" width="8%" <?=$style?>>Violation</th>
				<th align="left" width="8%" <?=$style?>>URL</th>
			</tr>
		</thead>
		<tbody><?php
			for($i=0, $n=sizeof($productData); $i<$n; $i++){
				$marketName = strtolower($productData[$i]['marketplace']);
				if ( ! $marketRetailer[$marketName]) continue;
				$date = $productData[$i]['dt'];
				$crawl_id = $productData[$i]['crawl_id'];
				$merchant_id = $productData[$i]['merchant_id'];
				$class = '';
				if (($i % 2) == 0) $class = 'style="background:#E7E7E8"'; ?>
			<tr <?php echo $class; ?>>
				<td <?=$style?>><?=ucfirst($productData[$i]['marketplace'])?></td>
				<td <?=$style?>>
					<?php
					$merchantName = getMerchantName($productData[$i]['merchant_id']);
					if($merchantName == 'livamed') $merchantName = 'Livamed.com';
					if($merchantName == 'vitacost') $merchantName = 'Vitacost.com';
					if(strtolower($productData[$i]['marketplace'])=='amazon' && $merchantName != 'Amazon.com'){
						echo '<a href="http://www.amazon.com/gp/aag/details/ref=aag_m_ss?ie=UTF8&isAmazonFulfilled=&marketplaceID=ATVPDKIKX0DER&isCBA=&asin=&seller='.$productData[$i]['merchant_id'].'" target="_blank">'.$merchantName.'</a>';
					}else{
						echo $merchantName;
					}
					?>
				</td>
				<td <?=$style?>><?=date('m/d/Y', $productData[$i]['dt'])?></td>
				<td <?=$style?>><?=date('h:i A', $productData[$i]['dt'])?></td>

				<td <?=$style?>><a href="<?=$productData[$i]['url'];?>" target="_blank">View Listing</a></td>
				<td <?=$style?>>$<?=number_format($productData[$i]['price'], 2)?></td>

				<?php
				$comparison = false;
				if(isset($comparison_data[$prodId][$marketName][$crawl_id][$merchant_id])):
					$comparison = $comparison_data[$prodId][$marketName][$crawl_id][$merchant_id];
					$merchantName = getMerchantName($comparison['merchant_id']);
				endif; ?>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['price'], 2)?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['retail'], 2)?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?>$<?=number_format($comparison['wholesale'], 2)?><?php endif; ?></td>
				<td <?=$style?>><?php if($comparison): ?><?php if(!empty($comparison['map']) && $comparison['map'] != 0 && $comparison['map'] != 0.00):?>$<?=number_format($comparison['map'], 2); endif;?><?php endif; ?></td>
				<td <?=$style?>>
					<?php if($comparison):
					//until we start keeping correct map values in dynamo - just grab from google data
					$map = $comparison['map'];
					if($comparison['price'] < $map){
						$violation = '<img src="'.frontImageUrl().'floor.png" alt="" style="margin-right:5px;"> LOW';
						//for non-violation reports we - don't really have the shot value from the violations table
						$shot = get_violation_image($comparison);
						if ( ! empty($shot))
							$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

						echo $violation;
					}
					endif; ?>
				</td>
				<td <?=$style?>><?php if($comparison): ?><a href="<?=$comparison['url']?>" target="_blank">View Listing</a><?php endif; ?></td>
			</tr><?php
			} ?>
		</tbody>
	</table><?php
	endif;
endforeach;
