<?php
if( ! empty($Data)){
	foreach($Data as $prodId=>$productData){
		$color = Color_handler::get_next($color_index[$prodId])->get_hex();
		$pTitle = getProductsTitle($prodId);?>
		<div class="key_container">
			<div class="search-result" rel="<?php echo $prodId ?>" style="float:left; width:50%;">
				<div style="margin-bottom:10px;" class="product_save">
					<span class="prod-heading">
						<span class="circle" style="margin-right: 5px; margin-top: 5px; background-color:<?=$color?>"></span>Competitor Product Name: <?php echo $pTitle ?>
					</span>
					<div class="clear"></div>
				</div>
			</div><?php
			if (isset($comparison_data[$prodId])):
				$competitor_map_id = $competitor_map[$prodId]['id'];
				$competitor_map_title = getProductsTitle($competitor_map_id);
				$competitor_map_color = Color_handler::get_next($color_index[$competitor_map_id])->get_hex(); ?>

			<div class="search-result" rel="<?php echo $competitor_map_id ?>" style="float:left; width:50%">
				<div style="margin-bottom:10px;" class="product_save">
					<span class="prod-heading">
						<span class="circle" style="margin-right: 5px; margin-top: 5px; background-color:<?=$competitor_map_color?>"></span>Product Name: <?php echo $competitor_map_title ?>
					</span>
					<div class="clear"></div>
				</div>
			</div>
			<?php endif; ?>
			<div class="clear"></div>
		</div>

			<?php
			if( ! empty($marketplacesExist[$prodId])){ ?>
			<table cellspacing="0" cellpadding="0" class="reportTable sortable exportable">
				<thead>
					<tr>
						<th align="left" width="10%">Marketplace</th>
						<th align="left" width="21%">Merchant</th>
						<th align="left" width="7%">Date</th>
						<th align="left" width="6%">Time</th>
						<th align="left" width="9%">Wholesale</th>
						<th align="left" width="6%">Retail</th>
						<th align="left" width="6%">MAP</th>
						<th align="left" width="6%">Price</th>
						<th align="left" width="8%">Violation</th>
						<th align="left" width="21%">URL</th>
						<th align="left" width="9%">Wholesale</th>
						<th align="left" width="6%">Retail</th>
						<th align="left" width="6%">MAP</th>
						<th align="left" width="6%">Price</th>
						<th align="left" width="8%">Violation</th>
						<th align="left" width="21%">URL</th>
					</tr>
				</thead>
				<tbody><?php
					for($i=0, $n=sizeof($productData); $i<$n; $i++){
						$marketName = strtolower($productData[$i]['marketplace']);
						if ($marketRetailer[$marketName]) continue;
						$date = $productData[$i]['timestamp'];
						$crawl_id = $productData[$i]['crawl_id'];
						$merchant_id = $productData[$i]['merchant_id'];
			?>
			<tr>
				<td><?=ucfirst($productData[$i]['marketplace'])?></td>
				<td>
					<?php
					$merchantName = getMerchantName($productData[$i]['merchant_id']);
					if($merchantName == 'livamed') $merchantName = 'Livamed.com';
					if($merchantName == 'vitacost') $merchantName = 'Vitacost.com';
					if(strtolower($productData[$i]['marketplace'])=='amazon' && $merchantName != 'Amazon.com')
						echo '<a href="http://www.amazon.com/gp/aag/details/ref=aag_m_ss?ie=UTF8&isAmazonFulfilled=&marketplaceID=ATVPDKIKX0DER&isCBA=&asin=&seller='.$productData[$i]['merchant_id'].'" target="_blank">'.$merchantName.'</a>';
					else
						echo $merchantName;
					?>
				</td>
				<td><?=date('m/d/Y', $date)?></td>
				<td><?=date('h:i A', $date)?></td>
				<td>$<?=number_format($productData[$i]['wholesale'], 2)?></td>
				<td>$<?=number_format($productData[$i]['retail'], 2)?></td>
				<td><?php if(!empty($productData[$i]['map']) && $productData[$i]['map'] != 0 && $productData[$i]['map'] != 0.00):?>$<?=number_format($productData[$i]['map'], 2);?><?php endif;?></td>
				<td>$<?=number_format($productData[$i]['price'], 2)?></td>
				<td>
					<?php
					//until we start keeping correct map values in dynamo - just grab from google data
					$map = $productData[$i]['map'];
					if($productData[$i]['price'] < $map){
						$violation = '<img src="'.frontImageUrl().'icons/arrow-orange-down.png" alt="" style="margin-right:5px;"> LOW';
						//for non-violation reports we - don't really have the shot value from the violations table
						$shot = get_violation_image($productData[$i]);
						if ( ! empty($shot))
							$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

						echo $violation;
					}
					?>
				</td>
				<td><a href="<?=$productData[$i]['url']?>" target="_blank"><?=extractDomainByURL($productData[$i]['url'])?></a></td>

				<?php
				$comparison = false;
				if(isset($comparison_data[$prodId][$marketName][$crawl_id][$merchant_id])):
					$comparison = $comparison_data[$prodId][$marketName][$crawl_id][$merchant_id];
					$merchantName = getMerchantName($comparison['merchant_id']);
				endif; ?>
				<td><?php if($comparison): echo $merchantName; endif; ?></td>
				<td><?php if($comparison): ?>$<?=number_format($comparison['wholesale'], 2);?><?php endif; ?></td>
				<td><?php if($comparison): ?>$<?=number_format($comparison['retail'], 2);?><?php endif; ?></td>
				<td><?php if($comparison): ?><?php if(!empty($comparison['map']) && $comparison['map'] != 0 && $comparison['map'] != 0.00):?>$<?=number_format($comparison['map'], 2); endif;?><?php endif; ?></td>
				<td><?php if($comparison): ?>$<?=number_format($comparison['price'], 2);?><?php endif; ?></td>
				<td>
					<?php if($comparison):
					//until we start keeping correct map values in dynamo - just grab from google data
					$map = $comparison['map'];
					if($comparison['price'] < $map){
						$violation = '<img src="'.frontImageUrl().'icons/arrow-orange-down.png" alt="" style="margin-right:5px;"> LOW';
						//for non-violation reports we - don't really have the shot value from the violations table
						$shot = get_violation_image($comparison);
						if ( ! empty($shot))
							$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

						echo $violation;
					}
					endif; ?>
				</td>
				<td><?php if($comparison): ?><a href="<?=$comparison['url']?>" target="_blank"><?=extractDomainByURL($comparison['url'])?></a><?php endif; ?></td>
			</tr>
			<?php
					}?>
				</tbody>
			</table>
			<br /><?php
			}



	if( ! empty($retailersExist[$prodId])){ ?>
	<table cellspacing="0" cellpadding="0" class="reportTable sortable exportable">
		<thead>
			<tr>
				<th align="left" width="10%">Retailer</th>
				<th align="left" width="21%">Merchant</th>
				<th align="left" width="7%">Date</th>
				<th align="left" width="6%">Time</th>
				<th align="left" width="9%">Wholesale</th>
				<th align="left" width="6%">Retail</th>
				<th align="left" width="6%">MAP</th>
				<th align="left" width="6%">Price</th>
				<th align="left" width="8%">Violation</th>
				<th align="left" width="21%">URL</th>
			</tr>
		</thead>
		<tbody><?php
					for($i=0, $n=sizeof($productData); $i<$n; $i++){
						$marketName = strtolower($productData[$i]['marketplace']);
						if ( ! $marketRetailer[$marketName]) continue;
						$date = $productData[$i]['dt'];
						$crawl_id = $productData[$i]['crawl_id'];
						$merchant_id = $productData[$i]['merchant_id'];
			?>
			<tr>
				<td><?=ucfirst($productData[$i]['marketplace'])?></td>
				<td>
					<?php
					$merchantName = getMerchantName($productData[$i]['merchant_id']);
					if($merchantName == 'livamed') $merchantName = 'Livamed.com';
					if($merchantName == 'vitacost') $merchantName = 'Vitacost.com';
					if(strtolower($productData[$i]['marketplace'])=='amazon' && $merchantName != 'Amazon.com')
						echo '<a href="http://www.amazon.com/gp/aag/details/ref=aag_m_ss?ie=UTF8&isAmazonFulfilled=&marketplaceID=ATVPDKIKX0DER&isCBA=&asin=&seller='.$productData[$i]['merchant_id'].'" target="_blank">'.$merchantName.'</a>';
					else
						echo $merchantName;
					?>
				</td>
				<td><?=date('m/d/Y', $productData[$i]['dt'])?></td>
				<td><?=date('h:i A', $productData[$i]['dt'])?></td>
				<td>$<?=number_format($productData[$i]['wholesale'], 2)?></td>
				<td>$<?=number_format($productData[$i]['retail'], 2)?></td>
				<td><?php if(!empty($productData[$i]['map']) && $productData[$i]['map'] != 0 && $productData[$i]['map'] != 0.00):?>$<?=number_format($productData[$i]['map'], 2);?><?php endif;?></td>
				<td>$<?=number_format($productData[$i]['price'], 2)?></td>
				<td>
					<?php
					//until we start keeping correct map values in dynamo - just grab from google data
					$map = $productData[$i]['map'];
					if($productData[$i]['price'] < $map){
						$violation = '<img src="'.frontImageUrl().'icons/arrow-orange-down.png" alt="" style="margin-right:5px;"> LOW';
						//for non-violation reports we - don't really have the shot value from the violations table
						$shot = get_violation_image($productData[$i]);
						if ( ! empty($shot))
							$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

						echo $violation;
					}
					?>
				</td>
				<td><a href="<?=$productData[$i]['url']?>" target="_blank"><?=extractDomainByURL($productData[$i]['url'])?></a></td>

				<?php
				$comparison = false;
				if(isset($comparison_data[$prodId][$marketName][$crawl_id][$merchant_id])):
					$comparison = $comparison_data[$prodId][$marketName][$crawl_id][$merchant_id];
					$merchantName = getMerchantName($comparison['merchant_id']);
				endif; ?>
				<td><?php if($comparison): echo $merchantName; endif; ?></td>
				<td><?php if($comparison): ?>$<?=number_format($comparison['wholesale'], 2)?><?php endif; ?></td>
				<td><?php if($comparison): ?>$<?=number_format($comparison['retail'], 2)?><?php endif; ?></td>
				<td><?php if($comparison): ?><?php if(!empty($comparison['map']) && $comparison['map'] != 0 && $comparison['map'] != 0.00):?>$<?=number_format($comparison['map'], 2); endif;?><?php endif; ?></td>
				<td><?php if($comparison): ?>$<?=number_format($comparison['price'], 2)?><?php endif; ?></td>
				<td>
					<?php if($comparison):
					//until we start keeping correct map values in dynamo - just grab from google data
					$map = $comparison['map'];
					if($comparison['price'] < $map){
						$violation = '<img src="'.frontImageUrl().'icons/arrow-orange-down.png" alt="" style="margin-right:5px;"> LOW';
						//for non-violation reports we - don't really have the shot value from the violations table
						$shot = get_violation_image($comparison);
						if ( ! empty($shot))
							$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

						echo $violation;
					}
					endif; ?>
				</td>
				<td><?php if($comparison): ?><a href="<?=$comparison['url']?>" target="_blank"><?=extractDomainByURL($comparison['url'])?></a><?php endif; ?></td>
			</tr>
			<?php
					}?>
				</tbody>
			</table>
			<br /><?php
			}
	}
}/*elseif(isset($Data)){ ?>
	<table cellpadding="0" cellspacing="0" width="100%" border="0" class="reportTable sortable exportable">
		<tr><td align="center"><?=$noRecord?></td></tr>
	</table>
<?php
}*/
?>
