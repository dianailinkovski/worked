<?php

class InsertAchatPackageClass {  
    public $user_id;
	public $package_id;
	public $date_achat;
	public $date_fin;
	public $original_transaction_id;
	public $transaction_id;
	public $unique_identifier;
	public $date;
	
    function __construct($receipt, $user_id, $package_id, $dateheure) {  
		
		$this->user_id = $user_id;
		$this->package_id = $package_id;
		$this->date = $dateheure;
		$this->date_achat = $receipt->purchase_date;
		$this->date_fin = $receipt->expires_date_formatted;
		$this->original_transaction_id = $receipt->original_transaction_id;
		$this->transaction_id = $receipt->transaction_id;
		$this->unique_identifier = $receipt->unique_identifier;
		
    }  
}

?>