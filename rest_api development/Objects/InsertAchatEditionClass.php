<?php

class InsertAchatEditionClass {  
    public $user_id;
	public $edition_id;
	public $original_transaction_id;
	public $transaction_id;
	public $unique_identifier;
	public $date;
	
    function __construct($receipt, $user_id, $edition_id, $dateheure) {  
		
		$this->user_id = $user_id;
		$this->edition_id = $edition_id;
		$this->date = $dateheure;
		
		$this->original_transaction_id = $receipt->original_transaction_id;
		$this->transaction_id = $receipt->transaction_id;
		$this->unique_identifier = $receipt->unique_identifier;
		
    }  
}

?>