<?php
foreach($Data as $prodId=>$productData){
	if( ! empty($marketplacesExist[$prodId])){ ?>
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable exportable">
		<thead>
			<tr>
				<th align="left" width="10%">Marketplace</th>
				<th align="left" width="21%">Merchant</th>
				<th align="left" width="7%">Date</th>
				<th align="left" width="6%">Time</th>
				<?php if($by_which !== 'bycompetition'):?>
				<th align="left" width="9%">Wholesale</th>
				<th align="left" width="6%">Retail</th>
				<th align="left" width="6%">MAP</th>
				<?php endif;?>
				<th align="left" width="6%">Price</th>
				<?php if($by_which !== 'bycompetition'):?>
				<th align="left" width="8%">Violation</th>
				<?php endif;?>
				<th align="left" width="21%">URL</th>
			</tr>
		</thead>
		<tbody><?php
			for($i=0, $n=sizeof($productData); $i<$n; $i++){
				$marketName = strtolower($productData[$i]['marketplace']);
				if ($marketRetailer[$marketName]) continue;
				$date = $productData[$i]['dt']; ?>
			<tr>
				<td><?=ucfirst($productData[$i]['marketplace']);?></td>
				<td>
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
				<td><?=date('m/d/Y', $productData[$i]['dt'])?></td>
				<td><?=date('h:i A', $productData[$i]['dt'])?></td>
				<?php if($by_which  !== 'bycompetition'):?>
				<td>$<?=number_format($productData[$i]['wholesale'], 2)?></td>
				<td>$<?=number_format($productData[$i]['retail'], 2)?></td>
				<td><?php if( ! empty($productData[$i]['map']) && $productData[$i]['map'] != 0 && $productData[$i]['map'] != 0.00):?>$<?=number_format($productData[$i]['map'], 2);?><?php endif;?></td>
				<?php endif;?>
				<td>$<?=number_format($productData[$i]['price'], 2);?></td>
				<?php if($by_which  !== 'bycompetition'):?>
				<td>
					<?php
					//until we start keeping correct map values in dynamo - just grab from google data
					$map = $productData[$i]['map'];
					if($productData[$i]['price'] < $map){
						$violation = '<img src="'.frontImageUrl() . 'icons/arrow-orange-down.png" alt="" style="margin-right:5px;"> LOW';
						//for non-violation reports we - don't really have the shot value from the violations table
						$shot = get_violation_image($productData[$i]);
						if ( ! empty($shot))
							$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

						echo $violation;
					}
					?>
				</td>
				<?php endif;?>
				<td><a href="<?=$productData[$i]['url']?>" target="_blank"><?=extractDomainByURL($productData[$i]['url'])?></a></td>
			</tr><?php
			}?>
		</tbody>
	</table>
	<br /><?php
	}

	if( ! empty($retailersExist[$prodId])){ ?>
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable exportable">
		<thead>
			<tr>
				<th align="left" width="10%">Retailer</th>
				<th align="left" width="21%">Merchant</th>
				<th align="left" width="7%">Date</th>
				<th align="left" width="6%">Time</th>
				<?php if($by_which !== 'bycompetition'):?>
				<th align="left" width="9%">Wholesale</th>
				<th align="left" width="6%">Retail</th>
				<th align="left" width="6%">MAP</th>
				<?php endif;?>
				<th align="left" width="6%">Price</th>
				<?php if($by_which !== 'bycompetition'):?>
				<th align="left" width="8%">Violation</th>
				<?php endif;?>
				<th align="left" width="21%">URL</th>
			</tr>
		</thead>
		<tbody><?php
			for($i=0, $n=sizeof($productData); $i<$n; $i++){
				$marketName = strtolower($productData[$i]['marketplace']);
				if ( ! $marketRetailer[$marketName]) continue;
				$date = $productData[$i]['dt']; ?>
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
				<?php if($by_which !== 'bycompetition'):?>
				<td>$<?=number_format($productData[$i]['wholesale'], 2)?></td>
				<td>$<?=number_format($productData[$i]['retail'], 2)?></td>
				<td><?php if( ! empty($productData[$i]['map']) && $productData[$i]['map'] != 0 && $productData[$i]['map'] != 0.00):?>$<?=number_format($productData[$i]['map'], 2);?><?php endif;?></td>
				<?php endif;?>
				<td>$<?=number_format($productData[$i]['price'], 2)?></td>
				<?php if($by_which !== 'bycompetition'):?>
				<td>
					<?php
					//until we start keeping correct map values in dynamo - just grab from google data
					$map = $productData[$i]['map'];
					if($productData[$i]['price'] < $map){
						$violation = '<img src="'.frontImageUrl() . 'icons/arrow-orange-down.png" alt="" style="margin-right:5px;"> LOW';
						//for non-violation reports we - don't really have the shot value from the violations table
						$shot = get_violation_image($productData[$i]);
						if ( ! empty($shot))
							$violation = '<a href="'.$shot.'" target="_blank">' . $violation . '</a>';

						echo $violation;
					}
					?>
				</td>
				<?php endif;?>
				<td><a href="<?=$productData[$i]['url']?>" target="_blank"><?=extractDomainByURL($productData[$i]['url'])?></a></td>
			</tr><?php
			} ?>
		</tbody>
	</table><?php
	}
}
