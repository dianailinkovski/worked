<?php
$reportString = "TrackStreet Reports - $title\r\n\r\n$brand_name - $headerDate\r\n\r\nUPC/Product Name:\r\n";
foreach($upc_list as $upc){
	$reportString .= $upc['upc_code'].' - '.$upc['title']."\r\n";
}
$reportString .= "\r\n© ".date('Y').' Sticky Business, LLC - www.juststicky.com';
echo wordwrap($reportString);
?>