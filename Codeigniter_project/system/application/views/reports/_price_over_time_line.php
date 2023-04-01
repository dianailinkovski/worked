<?php
foreach($Data as $prodId=>$productData){
	if( ! empty($marketplacesExist[$prodId])){ ?>
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable exportable">
		<thead>
			<tr>
				<th align="left" width="18%">Marketplace</th>
				<th align="left" width="16%">Date</th>
				<th align="left" width="18%">Wholesale</th>
				<th align="left" width="16%">Retail</th>
				<th align="left" width="16%">MAP</th>
				<th align="left" width="16%">Avg Price</th>
			</tr>
		</thead>
		<tbody><?php
			foreach($productData as $marketName=>$pricing){
				if ($marketRetailer[$marketName]) continue;
				for($i=0, $n=sizeof($pricing); $i<$n; $i++){ ?>
			<tr>
				<td><a href="<?=site_url('reports/market/'.$pricing[$i]['marketplace'].'/'.$prodId.'/'.date('Y-m-d', $pricing[$i]['dt']) . '/' . $url_modifier)?>" class="product-drilldown"><?=ucfirst($pricing[$i]['marketplace'])?></a></td>
				<td><?=date('m/d/Y', $pricing[$i]['dt'])?></td>
				<td>$<?=number_format($pricing[$i]['wholesale'], 2)?></td>
				<td>$<?=number_format($pricing[$i]['retail'], 2)?></td>
				<td><?php if( ! empty($pricing[$i]['map']) && $pricing[$i]['map'] != 0 && $pricing[$i]['map'] != 0.00):?>$<?=number_format($pricing[$i]['map'], 2); endif;?></td>
				<td>$<?=number_format($pricing[$i]['price'], 2)?></td>
			</tr><?php
				}
			} ?>
		</tbody>
	</table>
	<br /><?php
	}

	if( ! empty($retailersExist[$prodId])){ ?>
	<table cellspacing="0" cellpadding="0" border="0" width="100%" class="reportTable sortable exportable">
		<thead>
			<tr>
				<th align="left" width="18%">Retailer</th>
				<th align="left" width="16%">Date</th>
				<th align="left" width="18%">Wholesale</th>
				<th align="left" width="16%">Retail</th>
				<th align="left" width="16%">MAP</th>
				<th align="left" width="16%">Avg Price</th>
			</tr>
		</thead>
		<tbody><?php
			foreach($productData as $marketName=>$pricing){
				if ( ! $marketRetailer[$marketName]) continue;
				for($i=0, $n=sizeof($pricing); $i<$n; $i++){ ?>
			<tr>
				<td><a href="<?=site_url('reports/market/'.$pricing[$i]['marketplace'].'/'.$prodId.'/'.date('Y-m-d', $pricing[$i]['dt']) . '/' . $url_modifier)?>" class="product-drilldown"><?=ucfirst($pricing[$i]['marketplace']);?></a></td>
				<td><?=date('m/d/Y', $pricing[$i]['dt'])?></td>
				<td>$<?=number_format($pricing[$i]['wholesale'], 2)?></td>
				<td>$<?=number_format($pricing[$i]['retail'], 2)?></td>
				<td><?php if( ! empty($pricing[$i]['map']) && $pricing[$i]['map'] != 0 && $pricing[$i]['map'] != 0.00):?>$<?=number_format($pricing[$i]['map'], 2); endif;?></td>
				<td>$<?=number_format($pricing[$i]['price'], 2)?></td>
			</tr><?php
				}
			} ?>
		</tbody>
	</table><?php
	}
}
