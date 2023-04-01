<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "includes/db.php";


require_once( "Objects/InsertAchatEditionClass.php");
require_once( "Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);

$username = $data[0];
$password = $data[1];

if(count($data) == 3) {
	$edition_id = $data[2]->id;
}
else {
	$edition_id = NULL;
}


$functionObject = new FunctionClass();
$user_id = $functionObject->getIdMembre($username, $password);

try {
	$DBH->query('BEGIN');
	
	$dateheure = date('Y-m-d H:i:s', time());
	
	
	/*
	enregistrer la transaction
	*/
	
	$STH = $DBH->prepare("
	INSERT INTO `achats_editions` (
	`user_id`,`edition_id`, `original_transaction_id`, `transaction_id`, `unique_identifier`,`date`) 
	VALUES (
	:user_id, :edition_id, :original_transaction_id, :transaction_id, :unique_identifier, :date)
	");
	
	//print_r($json);
	$achatEditionClass = new InsertAchatEditionClass($response->receipt, $user_id, $edition_id, $dateheure);
	$STH->execute((array)$achatEditionClass);
	
	$achatEditionClass_affected = $STH->rowCount();
	
	
	
	
	
	
	
	
	
	
	if($achatEditionClass_affected == 1) {
		$DBH->query('COMMIT');
		//echo json_encode(array("resultat" => "true", "data" => array('idcommande'=>$idcommande, 'numerocommande'=>$idcommande, 'commandedateheure'=>$commandedateheure)));
	}
	else {
		$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "Erreur d'enregistrement"));
	}
}
catch(PDOException $e) {
	$DBH->query('ROLLBACK');
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-2"));
}


?>