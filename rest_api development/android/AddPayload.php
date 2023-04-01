<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "../includes/db.php";


require_once( "../Objects/InsertAchatAndroidPayloadClass.php");
require_once( "../Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);

$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

$sku = isset($_POST['sku']) ? $_POST['sku'] : "";
$quantite = isset($_POST['quantite']) ? $_POST['quantite'] : "";
$prix = isset($_POST['prix']) ? $_POST['prix'] : "";
$payload = isset($_POST['payload']) ? $_POST['payload'] : "";


$functionObject = new FunctionClass('./../');
$user_id = $functionObject->getIdMembre($username, $password);
$virtual_currency_bought = $functionObject->getVirtualCurrencyBundleIdForAndroidWithQuantite($quantite);
try {
	$DBH->query('BEGIN');
	
	$dateheure = date('Y-m-d H:i:s', time());
	
	
	/*
	enregistrer la transaction
	*/
	
	
	
	
	
	
	$STH = $DBH->prepare("
	INSERT IGNORE INTO `achats_android` (
	`user_id`, `sku`, `virtual_currency_bought`, `quantite`, `prix`, `payload`, `date`) 
	VALUES (
	:user_id, :sku, :virtual_currency_bought, :quantite, :prix, :payload, :date)
	");
	
	//print_r($json);
	
	
	$achatAndroidPayloadClass = new InsertAchatAndroidPayloadClass($user_id, $sku, $virtual_currency_bought, $quantite, $prix, $payload, $dateheure);
	$STH->execute((array)$achatAndroidPayloadClass);
	
	$achatAndroidPayloadClass_affected = $STH->rowCount();
	$idachat = $DBH->lastInsertId('id');
	
	if($achatAndroidPayloadClass_affected == 1) {
		$DBH->query('COMMIT');
		
		echo json_encode(array("resultat" => "true", "data" => array('idachat'=>$idachat)));
	}
	else {
		$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "payloadUsed"));
	}
	
}
catch(PDOException $e) {
	if(($DBH->errorCode() == 23000) || ($PDOStatement->errorCode() == 23000)) {
        //Duplicate, show friendly error to the user and whatnot
		echo json_encode(array("resultat" => "false", "data" => "payloadUsed"));
    }
	else {
	
		$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-android-123"));
		
	}
}


?>