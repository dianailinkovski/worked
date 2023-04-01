<?php
$reportString = "TrackStreet Reports - $title\r\n\r\n$brand_name - $headerDate\r\n\r\n";
$reportString .= "Pricing Overview\n";

if (isset($marketplaces[0]) OR isset($retailers[0])):
	if (isset($marketplaces[0])):

		$reportString .= "Marketplace / Products / Merchants / Last Tracking \r\n\r\n";

		foreach ($marketplaces as $key => $data) {
			$crawl_info = !empty($last_crawl[$data['marketplace']]) ? $last_crawl[$data['marketplace']] : FALSE;
			$crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';

			$reportString .= $data['display_name'] . ' / ' . number_format($data['total_products']) . ' / ' . number_format($data['total_listing']) . ' / ' . $crawl_start . "\n";
		}

	endif;
	if (isset($retailers[0])):
		$reportString .= "\r\n\r\n";

		$reportString .= "Retailer / Products / Last Tracking \r\n\r\n";

		foreach ($retailers as $key => $data) {
			$crawl_info = !empty($last_crawl[$data['marketplace']]) ? $last_crawl[$data['marketplace']] : FALSE;
			$crawl_start = !empty($crawl_info->start_datetime) ? date('g:i a', strtotime($crawl_info->start_datetime)) : '';

			$reportString .= $data['display_name'] . ' / ' . number_format($data['total_products']) . ' / ' . $crawl_start . "\n";
		}
		$reportString .= "\r\n\r\n";
	endif;
else:
	$reportString .= "No record found \r\n\r\n";

endif;

$reportString .= "\r\n© " . date('Y') . ' Sticky Business, LLC - www.juststicky.com';
echo wordwrap($reportString);
