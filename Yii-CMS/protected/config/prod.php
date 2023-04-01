<?php

return CMap::mergeArray(
	require(dirname(__FILE__).'/main.php'),
	array()
	
	/*
	array(
		'components'=>array(
			'db'=>array(
				'connectionString' => 'mysql:host=;dbname=',
				'emulatePrepare' => true,
				'username' => '',
				'password' => '',
				'charset' => 'utf8',
			),
		),
	)
	*/
);
