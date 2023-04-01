<?php
/**
 * Verify a receipt and return receipt data
 *
 * @param   string  $receipt    Base-64 encoded data
 * @param   bool    $isSandbox  Optional. True if verifying a test receipt
 * @throws  Exception   If the receipt is invalid or cannot be verified
 * @return  array       Receipt info (including product ID and quantity)
 */
function getReceiptData($receipt, $isSandbox = true)
{
	// determine which endpoint to use for verifying the receipt
	if ($isSandbox) {
		$endpoint = 'https://sandbox.itunes.apple.com/verifyReceipt';
	}
	else {
		$endpoint = 'https://buy.itunes.apple.com/verifyReceipt';
	}

	// build the post data
	$postData = json_encode(
		array('receipt-data' => $receipt)
	);

	// create the cURL request
	$ch = curl_init($endpoint);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	
	// execute the cURL request and fetch response data
	$response = curl_exec($ch);
	$errno    = curl_errno($ch);
	$errmsg   = curl_error($ch);
	curl_close($ch);

	// ensure the request succeeded
	if ($errno != 0) {
		throw new Exception($errmsg, $errno);
	}

	// parse the response data
	$data = json_decode($response);

	// ensure response data was a valid JSON string
	if (!is_object($data)) {
		throw new Exception('Invalid response data');
	}

	// ensure the expected data is present
	if (!isset($data->status) || $data->status != 0) {
		throw new Exception('Invalid receipt');
	}

	// build the response array with the returned data
	return array(
		'quantity'       =>  $data->receipt->quantity,
		'product_id'     =>  $data->receipt->product_id,
		'transaction_id' =>  $data->receipt->transaction_id,
		'purchase_date'  =>  $data->receipt->purchase_date,
		'app_item_id'    =>  $data->receipt->app_item_id,
		'bid'            =>  $data->receipt->bid,
		'bvrs'           =>  $data->receipt->bvrs
	);
}

// fetch the receipt data and sandbox indicator from the post data
$receipt   = $_POST['receipt'];
$isSandbox = (bool) $_POST['sandbox'];

// verify the receipt
try {
	$info = getReceiptData($receipt, $isSandbox);
	//print_r($info);
	//echo "test de print";
	// receipt is valid, now do something with $info
}
catch (Exception $ex) {
	// unable to verify receipt, or receipt is not valid
	print_r($ex);
	echo "test de print 2";
}
?>
