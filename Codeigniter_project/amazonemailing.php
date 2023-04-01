<?php
if ( !isset($_POST['amazone_merchant_store_page_url']) || !isset($_POST['amazone_email']) || !isset($_POST['amazone_password']) || !isset($_POST['email_message']) ) {
?>

<html>
<body>
<form method="post">
	<h1>Please set the amazone informations</h1>
	<table border=0>
		<tr>
			<td>Mechant Store URL:&nbsp;</td>
			<td><input type="text" name="amazone_merchant_store_page_url" value="http://www.amazon.com/gp/aag/main?ie=UTF8&asin=&isAmazonFulfilled=&isCBA=&marketplaceID=ATVPDKIKX0DER&orderID=&seller=A2J1ICAS7Y5E35" size=100 /></td>
		</tr>
		<tr>
			<td>Your email address:&nbsp;</td>
			<td><input type="text" name="amazone_email" size=100 /></td>
		</tr>
		<tr>
			<td>Your password:&nbsp;</td>
			<td><input type="text" name="amazone_password" size=100 /></td>
		</tr>
		<tr>
			<td>Message:&nbsp;</td>
			<td><textarea name="email_message" cols=80 rows=10 ></textarea></td>
		</tr>
		<tr>
			<td/>
			<td><input type="submit" value="Test send email" /></td>
		</tr>
	</table>
</form>
</body>
</html>
<?php 
	exit;
}


$amazone_merchant_store_page_url = $_POST['amazone_merchant_store_page_url']; //"http://www.amazon.com/gp/aag/main?ie=UTF8&asin=&isAmazonFulfilled=&isCBA=&marketplaceID=ATVPDKIKX0DER&orderID=&seller=A2J1ICAS7Y5E35";
$amazone_email		= $_POST['amazone_email']; //"wendondon@hotmail.com";
$amazone_password	= $_POST['amazone_password']; //"djajsl123";
$email_message		= $_POST['email_message'];

/*
 $ch  = curl_init();

 curl_setopt($ch, CURLOPT_URL, $amazone_merchant_store_page_url);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_HEADER, 1);
 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 $html_contents = curl_exec($ch);


 // parse <A> tag in merchant store page
 if ( !preg_match_all('/<a href=.*?<\/a>/is', $html_contents, $atags) ) {
 die('Failed to find a tags!');
 }

 $contact_page_url = "";
 for ( $i = 0; $i < count($atags[0]); $i ++ ) {
 $text = trim( strip_tags($atags[0][$i]) );

 if ( $text == 'Contact the seller' ) {
 if ( preg_match_all('/<a[^>]+href=([\'"])(.+?)\1[^>]*>/i', $atags[0][$i], $result) ) {
 $contact_page_url = $result[2][0];
 break;
 }
 }
 }

 if ( $contact_page_url == '' ) {
 die('Failed to find a "Contact the seller"!');
 }*/

// parse parameters
$temp = explode("?", $amazone_merchant_store_page_url);
$temp = explode("&", $temp[1]);
$parameters = array();
for ( $i = 0; $i < count($temp); $i ++ ) {
	$p = explode("=", $temp[$i]);
	$parameters[$p[0]] = isset($p[1]) ? $p[1] : "";
}
if ( !isset($parameters['marketplaceID']) || !isset($parameters['seller']) ) {
	die("Failed to find marketpalseID or seller from URL");
}
$contact_page_url = "https://www.amazon.com/gp/help/contact/contact.html?ie=UTF8&asin=&isCBA=&marketplaceID=".$parameters['marketplaceID']."&orderID=&ref_=aag_m_fi&sellerID=".$parameters['seller'];
$signin_page_url = "https://www.amazon.com/ap/signin?_encoding=UTF8&openid.assoc_handle=usflex&openid.claimed_id=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.identity=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0%2Fidentifier_select&openid.mode=checkid_setup&openid.ns=http%3A%2F%2Fspecs.openid.net%2Fauth%2F2.0&openid.ns.pape=http%3A%2F%2Fspecs.openid.net%2Fextensions%2Fpape%2F1.0&openid.pape.max_auth_age=900&openid.return_to=".urlencode($contact_page_url);

$ch  = curl_init();
curl_setopt($ch, CURLOPT_COOKIEJAR, '/Volumes/WORK/amazoncookie.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/Volumes/WORK/amazoncookie.txt');
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.2.13) Gecko/20101206 Ubuntu/10.10 (maverick) Firefox/3.6.13');
curl_setopt($ch, CURLOPT_URL, $signin_page_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$html_contents = curl_exec($ch);

/*******************************************
 * 
 * Signin Form
 * 
 *******************************************/
if ( !preg_match('/<form name="signIn".*?<\/form>/is', $html_contents, $form) ) {
	die("Failed to find a signin form");
}
$form = $form[0];

// find the action of the login form
if (!preg_match('/action="([^"]+)"/i', $form, $action)) {
	die('Failed to find login form url');
}

$signin_form_action = $action[1]; // this is our new post url

// find all hidden fields which we need to send with our login, this includes security tokens
$count = preg_match_all('/<input type="hidden"\s*name="([^"]*)"\s*value="([^"]*)"/i', $form, $hiddenFields);

$postFields = array();

// turn the hidden fields into an array
for ($i = 0; $i < $count; ++$i) {
	$postFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
}

// add our login values
$postFields['email']    = $amazone_email;
$postFields['create']   = 0;
$postFields['password'] = $amazone_password;

$post = '';

// convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
foreach($postFields as $key => $value) {
	$post .= $key . '=' . urlencode($value) . '&';
}

$post = substr($post, 0, -1);

// set additional curl options using our previous options
curl_setopt($ch, CURLOPT_URL, $signin_form_action);
curl_setopt($ch, CURLOPT_REFERER, $signin_page_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

$html_contents = curl_exec($ch); // make request for signin

/*******************************************
 * 
 * Contact Option Form
 * 
 *******************************************/
if ( !preg_match('/<form action="\/gp\/help\/contact\/contact.html".*?<\/form>/is', $html_contents, $form) ) {
	die( 'Failed to find a contact form' );
}

$form = $form[0];

// find the action of the contact form
if (!preg_match('/action="([^"]+)"/i', $form, $action)) {
	die('Failed to find contact form url');
}

$contact_form_action = $action[1]; // this is our new post url
if ( substr($contact_form_action, 0, 4) != 'http' ) $contact_form_action = "https://www.amazon.com".$contact_form_action."?"; 

// find all hidden fields
$count = preg_match_all('/<input.*?type="hidden".*?name="([^"]*)".*?value="([^"]*)"/i', $form, $hiddenFields);

$formFields = array();
// add our contact option values
$formFields['assistanceType']   = "asin";
$formFields['subject']			= 5;
$formFields['writeMessageButton']="Write message";

// turn the hidden fields into an array
for ($i = 0; $i < $count; ++$i) {
	$formFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
}

// convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
foreach($formFields as $key => $value) {
	if ( $key == '' ) continue;
	$contact_form_action .= ($key . '=' . urlencode($value) . '&');
}
$contact_form_action = substr($contact_form_action, 0, -1);

// set additional curl options using our previous options
curl_setopt($ch, CURLOPT_URL, $contact_form_action);
curl_setopt($ch, CURLOPT_POST, FALSE);

$html_contents = curl_exec($ch); // go to write form

/*******************************************
 * 
 * Write Message Form
 * 
 *******************************************/
if ( !preg_match('/<form id="writeMessageForm".*?<\/form>/is', $html_contents, $form) ) {
	die( 'Failed to find a write form' );
}

$form = $form[0];

// find the action of the contact form
if (!preg_match('/action="([^"]+)"/i', $form, $action)) {
	die('Failed to find write form url');
}

$write_form_action = $action[1]; // this is our new post url
if ( substr($write_form_action, 0, 4) != 'http' ) $write_form_action = "https://www.amazon.com/gp/help/contact/contact.html".$write_form_action; 

// find all hidden fields
$count = preg_match_all('/<input.*?type="hidden".*?name="([^"]*)".*?value="([^"]*)"/i', $form, $hiddenFields);

$formFields = array();
// add our contact option values
$formFields['commMgrComments']  = $email_message;
$formFields['sendEmailButton']	= "Send e-mail"; 
// turn the hidden fields into an array
for ($i = 0; $i < $count; ++$i) {
	$formFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
}

// convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
$post = '';

// convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
foreach($formFields as $key => $value) {
	$post .= $key . '=' . urlencode($value) . '&';
}

// set additional curl options using our previous options
curl_setopt($ch, CURLOPT_URL, $write_form_action);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

$html_contents = curl_exec($ch); // go to write form

/*******************************************
 * 
 * Check result
 * 
 *******************************************/
if ( preg_match('/<div class="message success.*?<\/div>/is', $html_contents, $divSucces) ) {
	die('Your mail has been sent to Lifetime WaterWorks. It might take a few minutes for the message to show in Sent Items.');
} else {
	die('Failed send your email.');
}


curl_close($ch);