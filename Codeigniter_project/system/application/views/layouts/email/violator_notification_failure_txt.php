<?php
$reportString = "TrackStreet Enforcement Email Failure \r\n\r\n$brand_name - $headerDate\r\n\r\n";

$reportString .= "$failures of $attempts enforcement emails failed to be delivered. ";
$reportString .= "The details of the failures are described below. \r\n\r\n\r\n";

$reportString .= "Failures:\r\n\r\n";
if ( ! empty($failed)) {
	for ($i = 0, $n = count($failed); $i < $n; $i++) {
		$notification = $failed[$i];
		if ( ! empty($notification['name_to']))
			$reportString .= $notification['name_to'] . ' <' . $notification['email_to'] . '>' . "\r\n";
		else
			$reportString .= $notification['email_to'] . "\r\n";

		$reportString .= 'Reason for failure: ';
		$exception = $exceptions[$i];
		$reportString .= empty($exception) ? 'Unknown Error' : $exception;

		$reportString .= "\r\n\r\n";
	}
}

$reportString .= "\r\n© ".date('Y').' Sticky Business, LLC - www.juststicky.com';
echo wordwrap($reportString);
