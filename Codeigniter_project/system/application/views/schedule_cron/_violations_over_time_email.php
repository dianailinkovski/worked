<?php
if(isset($Data)):
	if (count($Data) > 0):
		$square = 'display: inline-block; border-radius: 2px; width: 10px; height: 10px; margin: 0 2px 0 0;';
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
						<th width="100" align="left" <?=$style?>>Marketplace</th>
						<th width="100" align="left" <?=$style?>>Merchant</th>
						<th width="100" align="left" <?=$style?>>Date</th>
						<th width="100" align="left" <?=$style?>>Time</th>
						<th width="100" align="left" <?=$style?>>Wholesale</th>
						<th width="80" align="left" <?=$style?>>Retail</th>
						<th width="80" align="left" <?=$style?>>MAP</th>
						<th width="80" align="left" <?=$style?>>Price</th>
						<th width="100" align="left" <?=$style?>>Violation</th>
						<th width="100" align="left" <?=$style?>>URL</th>
					</tr>
				</thead>
				<tbody><?php
					for($i=0, $n=sizeof($productData); $i<$n; $i++){
						$marketName = strtolower($productData[$i]['marketplace']);
						if ($marketRetailer[$marketName]) continue;
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
						<td <?=$style?>><?=date('m/d/Y', $productData[$i]['timestamp']);?></td>
						<td <?=$style?>><?=date('h:i A', $productData[$i]['timestamp']);?></td>
						<td <?=$style?>>$<?=number_format($productData[$i]['wholesale'], 2);?></td>
						<td <?=$style?>>$<?=number_format($productData[$i]['retail'], 2);?></td>
						<td <?=$style?>><?php if(!empty($productData[$i]['map']) && $productData[$i]['map'] != 0 && $productData[$i]['map'] != 0.00):?>$<?=number_format($productData[$i]['map'], 2);?><?php endif;?></td>
						<td <?=$style?>>$<?=number_format($productData[$i]['price'], 2);?></td>
						<td <?=$style?>>
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
						<td <?=$style?>><a href="<?=$productData[$i]['url'];?>" target="_blank"><?=extractDomainByURL($productData[$i]['url']);?></a></td>
					</tr><?php
					}?>
				</tbody>
			</table>
			<br /><?php
			endif;

			if( ! empty($retailersExist[$prodId])): ?>
			<table border="0" bordercolor="#CCCCCC" cellpadding="3" cellspacing="0" width="100%" style="border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
				<thead>
					<tr style="background:#E7E7E8; height:30px; color:#000; vertical-align: top">
						<th width="100" align="left" <?=$style?>>Retailer</th>
						<th width="100" align="left" <?=$style?>>Merchant</th>
						<th width="100" align="left" <?=$style?>>Date</th>
						<th width="100" align="left" <?=$style?>>Time</th>
						<th width="100" align="left" <?=$style?>>Wholesale</th>
						<th width="80" align="left" <?=$style?>>Retail</th>
						<th width="80" align="left" <?=$style?>>MAP</th>
						<th width="80" align="left" <?=$style?>>Price</th>
						<th width="100" align="left" <?=$style?>>Violation</th>
						<th width="100" align="left" <?=$style?>>URL</th>
					</tr>
				</thead>
				<tbody><?php
					for($i=0, $n=sizeof($productData); $i<$n; $i++){
						$marketName = strtolower($productData[$i]['marketplace']);
						if ( ! $marketRetailer[$marketName]) continue;
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
						<td <?=$style?>><?=date('m/d/Y', $productData[$i]['timestamp']);?></td>
						<td <?=$style?>><?=date('h:i A', $productData[$i]['timestamp']);?></td>
						<td <?=$style?>>$<?=number_format($productData[$i]['wholesale'], 2);?></td>
						<td <?=$style?>>$<?=number_format($productData[$i]['retail'], 2);?></td>
						<td <?=$style?>><?php if(!empty($productData[$i]['map']) && $productData[$i]['map'] != 0 && $productData[$i]['map'] != 0.00):?>$<?=number_format($productData[$i]['map'], 2);?><?php endif;?></td>
						<td <?=$style?>>$<?=number_format($productData[$i]['price'], 2);?></td>
						<td <?=$style?>>
							<?php
							//until we start keeping correct map values in dynamo - just grab from google data
							$map = $productData[$i]['map'];
							if($productData[$i]['price'] < $map){
								$violation = '<img src="'.frontImageUrl().'floor.png" alt="" style="margin-right:5px;"> LOW';
								//for non-violation reports we - don't really have the shot value from the violations table
								$shot = get_violation_image($productData[$i]);
								if ( ! empty($shot))
									$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

								echo $violation;
							}
							?>
						</td>
						<td <?=$style?>><a href="<?=$productData[$i]['url'];?>" target="_blank"><?=extractDomainByURL($productData[$i]['url']);?></a></td>
					</tr><?php
					}?>
				</tbody>
			</table><?php
			endif;
		endforeach;
	elseif(isset($my)): ?>
		<table cellpadding="0" cellspacing="0" width="100%" border="0" style="border-collapse: collapse; background:#fff; font-family:Arial, Helvetica, sans-serif; font-size:12px">
			<tr><td align="center"><?=$noRecord?></td></tr>
		</table><?php
	endif;
endif;
