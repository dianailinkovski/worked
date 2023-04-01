<?php
$reportString = "TrackStreet Reports - $title\r\n\r\n$brand_name - $headerDate\r\n\r\n";
$reportString .= "Who Is Selling My Products\n";

if (isset($retailers[0])):
	$reportString .= "Retailer / Products \r\n\r\n";
	foreach ($retailers as $data)
		$reportString .= $data['display_name'] . ' / ' . number_format($data['total_products']) . "\n";
	$reportString .= "\r\n\r\n";
else:
	$reportString .= "No record found \r\n\r\n";
endif;

$reportString .= "\r\n© " . date('Y') . ' Sticky Business, LLC - www.juststicky.com';
echo wordwrap($reportString);
