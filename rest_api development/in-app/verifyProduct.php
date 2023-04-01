<?php

//$devmode = TRUE; // change this to FALSE after testing in sandbox
	
$receiptdata = $_POST['receiptdata'];	
//$data = isset($_POST['data']) ? json_decode($_POST['data']) : "";
$achat_id = $_POST['achatid'];

$username = $_POST['username'];
$password = $_POST['password'];


//print_r($response);











$response = validateReceipt($receiptdata);

if($response->{'status'} == 21007) {
	$response = validateReceipt($receiptdata, TRUE);
} else if($response->{'status'} == 21008) {
	$response = validateReceipt($receiptdata, FALSE);
}

if($response->{'status'} == 0) {
	//echo ('YES');
	require("../UpdateAchatConsumable.php");
} else {
	//echo ('NO');
	echo json_encode(array("resultat" => "false", "data" => $response));
	//print_r($response);	
}



function validateReceipt($receiptdata, $devmode = FALSE) {
	
	if($devmode)
		$appleURL = "https://sandbox.itunes.apple.com/verifyReceipt";
	else
		$appleURL = "https://buy.itunes.apple.com/verifyReceipt";
	
	$receipt = json_encode(array("receipt-data" => $receiptdata));
	
	
	
	
	
	$response_json = do_post_request($appleURL, $receipt);
	$response = json_decode($response_json);
	
	return $response;
}


function do_post_request($url, $data, $optional_headers = null)
{
  $params = array('http' => array(
			  'method' => 'POST',
			  'content' => $data
			));
  if ($optional_headers !== null) {
	$params['http']['header'] = $optional_headers;
  }
  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);
  if (!$fp) {
	throw new Exception("Problem with $url, $php_errormsg");
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
	throw new Exception("Problem reading data from $url, $php_errormsg");
  }
  return $response;
}

?>
