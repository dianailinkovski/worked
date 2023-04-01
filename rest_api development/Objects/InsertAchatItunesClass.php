<?php

class InsertAchatItunesClass {  
    public $user_id;
	public $vcbought;
	public $quantite;
	public $prix;
	public $date;
	
    function __construct($user_id, $vcbought, $quantite, $prix, $dateheure) {  
		
		$this->user_id = $user_id;
		$this->vcbought = $vcbought;
		$this->quantite = $quantite;
		$this->prix = $prix;
		$this->date = $dateheure;
		
    }  
}

?>