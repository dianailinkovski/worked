<?php

include "FunctionClass.php";

class InsertInventairesBriseClass {  
    //public $idrepresentant;
	public $idinventaire;
	public $idproduit;
	public $quantite;
    public $dateheure;
	public $latitude;
	public $longitude;
  
    function __construct($idrepresentant, $idproduit, $quantite) {  
        //$this->idrepresentant = $idrepresentant;
		$this->idproduit = $idproduit;
		$this->quantite = $quantite;
		
		
        $this->dateheure = date( 'Y-m-d H:i:s', time() );
		
		$functionClass = new FunctionClass();
		$this->idinventaire = $functionClass->getInventaireForIdRepresentant($idrepresentant);
		
    }  
}

?>