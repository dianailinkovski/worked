<?php

class InsertAchatAssociationClass {  
    public $member_id;
	public $package_item_id;
	public $journal_id;
	
    function __construct($member_id, $package_item_id, $journal_id) {  
		
		$this->member_id = $member_id;
		$this->package_item_id = $package_item_id;
		$this->journal_id = $journal_id;
		
    }  
}

?>