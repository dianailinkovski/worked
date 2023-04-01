<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Config for the Amazon Simple Email Service library
 *
 * @see ../libraries/Amazon_ses.php
 */

// Amazon credentials
$config['amazon_ses_secret_key'] = 'AKIAJ4W7FFCAUKJGGFPQ';
$config['amazon_ses_access_key'] = 'z1i0dRogwxfcdfDVfRWYeADionC7iMH9/TILH/HZ ';

// Adresses
$config['amazon_ses_from'] = 'contact@JustSticky.com';
$config['amazon_ses_reply_to'] = 'contact@JustSticky.com';

// Path to certificate to verify SSL connection (i.e. 'certs/cacert.pem') 
$config['amazon_ses_cert_path'] = '';

// Charset to be used, for example UTF-8, ISO-8859-1 or Shift_JIS. The SMTP
// protocol uses 7-bit ASCII by default
$config['amazon_ses_charset'] = 'UTF-8';

//$config['AWS_CERTIFICATE_AUTHORITY'] = true;
define('AWS_CERTIFICATE_AUTHORITY', true);

//Bucket Name
$config['bucket_name'] = 'marketvision';
