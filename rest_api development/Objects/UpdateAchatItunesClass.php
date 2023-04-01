<?php

class UpdateAchatItunesClass {  
    public $achat_id;
	public $transaction_id;
	public $unique_identifier;
	public $receipt;
	
    function __construct($achat_id, $transaction_id, $unique_identifier, $receipt) {  
		
		$this->achat_id = $achat_id;
		$this->transaction_id = $transaction_id;
		$this->unique_identifier = $unique_identifier;
		$this->receipt = $receipt;
		
    }  
}

?>