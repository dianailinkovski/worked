<?php

class UpdateAchatAndroidPayloadClass {  
	public $sku;
	public $payload;
	public $consume_date;
	
    function __construct($sku, $payload, $consume_date) {  
		$this->sku = $sku;
		$this->payload = $payload;
		$this->consume_date = $consume_date;
		
    }  
}

?>