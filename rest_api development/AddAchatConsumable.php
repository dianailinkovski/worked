<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "includes/db.php";


require_once( "Objects/InsertAchatItunesClass.php");
require_once( "Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);

$data = isset($_POST['data']) ? json_decode($_POST['data']) : "";

$username = $data->username;
$password = $data->password;

$vcbought = $data->vcbought;
$quantite = $data->quantite;
$prix = $data->prix;



$functionObject = new FunctionClass('./');
$user_id = $functionObject->getIdMembre($username, $password);

try {
	$DBH->query('BEGIN');
	
	$dateheure = date('Y-m-d H:i:s', time());
	
	
	/*
	enregistrer la transaction
	*/
	
	$STH = $DBH->prepare("
	INSERT INTO `achats_itunes` (
	`user_id`,`virtual_currency_bought`, `quantite`, `prix`, `date`) 
	VALUES (
	:user_id, :vcbought, :quantite, :prix, :date)
	");
	
	//print_r($json);
	$achatItunesClass = new InsertAchatItunesClass($user_id, $vcbought, $quantite, $prix, $dateheure);
	$STH->execute((array)$achatItunesClass);
	
	$achatItunesClass_affected = $STH->rowCount();
	$idachat = $DBH->lastInsertId('id');
	
	if($achatItunesClass_affected == 1) {
		$DBH->query('COMMIT');
		
		echo json_encode(array("resultat" => "true", "data" => array('idachat'=>$idachat)));
	}
	else {
		$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "Erreur d'enregistrement"));
	}
}
catch(PDOException $e) {
	$DBH->query('ROLLBACK');
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-123"));
}


?>