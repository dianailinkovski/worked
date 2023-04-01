<?php

//include "FunctionClass.php";

class UpdateMembrePackageClass {  
	public $member_id;
	public $package_id;
	
	function __construct($member_id, $package_id) {
		$this->member_id = $member_id;
		$this->package_id = $package_id;
		
	}
}

?>