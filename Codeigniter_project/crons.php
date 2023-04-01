<?php
/**
* @author       Asim Zeeshan
* @web         http://www.asim.pk/
* @date     13th May, 2009
* @copyright    No Copyrights, but please link back in any way
*/
 
/*
|---------------------------------------------------------------
| CASTING argc AND argv INTO LOCAL VARIABLES
|---------------------------------------------------------------
|
*/
error_reporting(E_ALL);
$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];
 
// INTERPRETTING INPUT
if ($argc > 1 && isset($argv[1])) {
    $_SERVER['PATH_INFO']   = $argv[1];
    $_SERVER['REQUEST_URI'] = $argv[1];
} else {
    $_SERVER['PATH_INFO']   = '/cron';
    $_SERVER['REQUEST_URI'] = '/cron';
}
$_SERVER['HTTP_HOST'] = 'sv-new.juststicky.com';
$_SERVER['SERVER_NAME'] = 'sv-new.juststicky.com';
/*
|---------------------------------------------------------------
| PHP SCRIPT EXECUTION TIME ('0' means Unlimited)
|---------------------------------------------------------------
|
*/
set_time_limit(0);
 
require_once('index.php');
?>