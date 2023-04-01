<?php
mb_internal_encoding('utf-8');
define('LOCAL', $_SERVER['REMOTE_ADDR'] == '127.0.0.1');

// Change the following paths if necessary
$config=dirname(__FILE__).'/protected/config/main.php';

if (is_dir('yii'))
{
	$yii=realpath('yii/yii.php');
	$config=dirname(__FILE__).'/protected/config/prod.php';
}
elseif (LOCAL)
{
	$yii=realpath(trim(file_get_contents('.yii_location'))).'/yii.php';
	$config=dirname(__FILE__).'/protected/config/dev.php';
}
else
{
	$yii=dirname(__FILE__).'/../../yii/yii-1.1.16.bca042/framework/yii.php';
	$config=dirname(__FILE__).'/protected/config/prod.php';
}

if (LOCAL)
{
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	defined('YII_DEBUG') or define('YII_DEBUG',true);
	defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
}

require_once($yii);
Yii::createApplication('CWebApplication', $config)->run();