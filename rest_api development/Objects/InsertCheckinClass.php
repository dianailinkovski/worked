<?php

class InsertCheckinClass {  
    public $idrepresentant;  
	public $idpointvente;
    public $latitude;  
    public $longitude;  
    public $dateheure;
  
    function __construct($idrepresentant, $idpointvente, $latitude, $longitude) {  
        $this->idrepresentant = $idrepresentant;
		$this->idpointvente = $idpointvente;
        $this->latitude = $latitude;  
        $this->longitude = $longitude;  
        $this->dateheure = date( 'Y-m-d H:i:s', time() );
    }  
}

?>