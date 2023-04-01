<?php

class InsertAchatEkCreditClass {  
    public $user_id;
	public $edition_id;
	public $package_id;
	public $quantite;
	public $date;
	
    function __construct($user_id, $edition_id, $package_id, $quantite, $dateheure) {  
		
		$this->user_id = $user_id;
		$this->edition_id = $edition_id;
		$this->package_id = $package_id;
		$this->quantite = $quantite;
		$this->date = $dateheure;
		
    }  
}

?>