<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Config for the Amazon S3 library
 *
 * @see ../libraries/AmazonS3.php
 */

// Amazon credentials

//For S3
$config['s3_bucket_name'] = 'images.juststicky.com';
$config['s3_cname'] = 'http://'. $config['s3_bucket_name'] .'/';
$config['s3_access_key'] = 'AKIAIATBDDDFRGLWQTBQ';
$config['s3_secret_key'] = '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';
$config['s3_violations_path'] = 'testvision/violations/';