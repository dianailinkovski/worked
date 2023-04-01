<?php
$reportString = "TrackStreet Reports - $title\r\n\r\n$brand_name - $headerDate\r\n\r\n";

$reportString .= "Violations By Marketplace\n";
if ( !empty($violatedMarketplaces)):
	$reportString .= "Marketplace / Products / Violations / Last Tracking\r\n\r\n";
	foreach ($marketplaces as $data):
		$marketIndex = strtolower($data['marketplace']);
		if ( !empty($violatedMarketplaces[$marketIndex])):
			$crawl_info = !empty($last_crawl[$marketIndex]) ? $last_crawl[$marketIndex] : FALSE;
			$crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';

			$reportString .= $data['display_name'].' / '.number_format($data['total_products']).' / '.(isset($market_violations[$marketIndex])?$market_violations[$marketIndex]:0).' / '.$crawl_start."\n";
		endif;
	endforeach;
	$reportString .= "\r\n\r\n";
else:
	$reportString .= "No Marketplace Violations\r\n\r\n";
endif;

$reportString .= "Violations By Retailer\n";
if( !empty($violatedRetailers)):
	$reportString .= "Retailer / Products / Violations / Last Tracking\r\n\r\n";
	foreach ($retailers as $data):
		$marketIndex = strtolower($data['marketplace']);
		if ( !empty($violatedRetailers[$marketIndex])):
			$crawl_info = !empty($last_crawl[$marketIndex]) ? $last_crawl[$marketIndex] : FALSE;
			$crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';

			$reportString .= $data['display_name'].' / '.number_format($data['total_products']).' / '.(isset($market_violations[$marketIndex])?$market_violations[$marketIndex]:0).' / '.$crawl_start."\n";
		endif;
	endforeach;
	$reportString .= "\r\n\r\n";
else:
	$reportString .= "No Retailer Violations.\r\n\r\n";
endif;

$reportString .= "Price Violators\n";
if(count($priceViolators) > 0) {
	$reportString .= "Merchant / Product Violations\r\n\r\n";
  foreach ($priceViolators as $key => $data) {
		$data = $data['crowl_merchant'];
		$name = (!empty($data['original_name']) && $data['original_name'] != NULL) ? $data['original_name']: $data['merchant_name'];

		$reportString .= trim($name).' / '.$priceViolators[$key]['total_violations']."\n";
	}
	$reportString .= "\r\n\r\n";
} else {
	$reportString .= "No records found.\r\n\r\n";
}

$reportString .= "Violated Products\n";
if (count($violatedProducts) > 0) {
	$reportString .= "Title / UPC / Retail / Wholesale / MAP\r\n\r\n";
	foreach ($violatedProducts as $key => $data) {
		$reportString .= html_entity_decode($data['title']).' / '.$data['upc_code'].' / $'.$data['retail_price'].' / $'.$data['wholesale_price'].' / $'.$data['price_floor']."\r\n";
	}
	$reportString .= "\r\n\r\n";
} else {
	$reportString .= "No record found.\r\n\r\n";
}

$reportString .= "\r\n© ".date('Y').' Sticky Business, LLC - www.juststicky.com';
echo wordwrap($reportString);
