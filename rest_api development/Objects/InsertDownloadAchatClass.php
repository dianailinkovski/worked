<?php

class InsertDownloadAchatClass {  
    public $member_id;
	public $achat_id;
	public $date;
	
    function __construct($member_id, $achat_id, $date) {  
		
		$this->member_id = $member_id;
		$this->achat_id = $achat_id;
		$this->date = $date;
		
    }  
}

?>