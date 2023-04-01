<?php

class InsertDownloadEditionClass {  
    public $member_id;
	public $edition_id;
	public $date;
	
    function __construct($member_id, $edition_id, $date) {  
		
		$this->member_id = $member_id;
		$this->edition_id = $edition_id;
		$this->date = $date;
		
    }  
}

?>