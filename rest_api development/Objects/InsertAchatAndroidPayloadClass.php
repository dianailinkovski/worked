<?php

class InsertAchatAndroidPayloadClass {  
    public $user_id;
	public $sku;
	public $virtual_currency_bought;
	public $quantite;
	public $prix;
	public $payload;
	public $date;
	
    function __construct($user_id, $sku, $virtual_currency_bought, $quantite, $prix, $payload, $dateheure) {  
		
		$this->user_id = $user_id;
		$this->sku = $sku;
		$this->virtual_currency_bought = $virtual_currency_bought;
		$this->quantite = $quantite;
		$this->prix = $prix;
		$this->payload = $payload;
		$this->date = $dateheure;
		
    }  
}

?>