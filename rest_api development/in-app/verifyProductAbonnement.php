<?php

$hex = $_POST['receiptdata'];
$data = isset($_POST['data']) ? json_decode($_POST['data']) : "";
//echo "\npost\n";
//print_r($_POST['data']);
//echo "\njson\n";
//print_r($data);

//$testing = 1;



$response = validateReceiptAbonnement($hex);

if($response->{'status'} == 21007) {
	$response = validateReceiptAbonnement($hex, 1);
}
else if($response->{'status'} == 21008) {
	$response = validateReceiptAbonnement($hex, 0);
}


if($response->{'status'} == 0) {
	echo ('YES');
	//print_r($response);	
	include "../AddAchatPackage.php";
	
} else {
	echo ('NO iTunesCode='.$response->{'status'});
	//print_r($response);		
}

function validateReceiptAbonnement($hex, $testing = 1) {
	$password = "2e3013503b56480ba27122c398c9e74d";
	
	if ($testing == 1)
        $url = 'https://sandbox.itunes.apple.com/verifyReceipt';
	else
			$url = 'https://buy.itunes.apple.com/verifyReceipt';
	
			
	$postData = json_encode
	(
	array(
			'receipt-data' => $hex,
			'password' => $password,
			)
	);
	
	$response_json = do_post_request($url, $postData);
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