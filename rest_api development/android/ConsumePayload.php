<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "../includes/db.php";

require_once( "../Objects/FunctionClass.php");
require_once( "../Objects/UpdateAchatAndroidPayloadClass.php");

//$json = json_decode($_GET['data']);

$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

$sku = isset($_POST['sku']) ? $_POST['sku'] : "";
$payload = isset($_POST['payload']) ? $_POST['payload'] : "";


$functionObject = new FunctionClass('./../');
$user_id = $functionObject->getIdMembre($username, $password);

try {
	$DBH->query('BEGIN');
	
	$consume_date = date('Y-m-d H:i:s', time());
	
	
	/*
	enregistrer la transaction
	*/
	
	$STH = $DBH->prepare("
		UPDATE `achats_android` 
		SET `consume_date` = :consume_date 
		WHERE `payload` = :payload 
		AND `sku` = :sku 
		LIMIT 1 
	");
	
	//print_r($json);
	
	
	$updateAchatAndroidPayloadClass = new UpdateAchatAndroidPayloadClass($sku, $payload, $consume_date);
	$STH->execute((array)$updateAchatAndroidPayloadClass);
	
	$achatAndroidClass_affected = $STH->rowCount();
	
	
	/*
	if($achatAndroidClass_affected == 1) {
		$DBH->query('COMMIT');
		
		echo json_encode(array("resultat" => "true", "data" => array('idachat'=>$idachat)));
	}
	else {
		$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "Erreur de consomation de l'achat"));
	}*/
	
	
	
	$functionObject = new FunctionClass();
	$idachat =  $functionObject->getIdFromPayloadAndSkuTransactionAndroid($payload, $sku);
	
	
	
	if($username == "" || $password == "") {
		
		$quantite_a_ajouter = $functionObject->getQuantiteFromIdTransactionAndroid($idachat);
		
		if($achatAndroidClass_affected == 1) {
			$DBH->query('COMMIT');
			echo json_encode(array("resultat" => "true", "data" => $quantite_a_ajouter));
		}
		else {
			$DBH->query('ROLLBACK');
			echo json_encode(array("resultat" => "false", "data" => "Erreur d'enregistrement de la transaction sans compte"));
		}
		
		exit;
	}
	
	
	
	$user_id = $functionObject->getIdMembre($username, $password);
	$quantite_a_ajouter = $functionObject->getQuantiteFromIdTransactionAndroid($idachat);
	$quantite_au_compte = $functionObject->getQuantiteFromMember($user_id);
	$nouveau_total = $quantite_au_compte + $quantite_a_ajouter;
	
	$STH = $DBH->prepare("
	UPDATE `member` 
	SET `ek_credit` = :total 
	WHERE `id` = :user_id
	LIMIT 1
	");
	
	$STH->bindParam(':total', $nouveau_total);
	$STH->bindParam(':user_id', $user_id);
	
	$STH->execute();
	
	$updateMemberClass_affected = $STH->rowCount();
	
	if($achatAndroidClass_affected == 1 && $updateMemberClass_affected == 1) {
		$DBH->query('COMMIT');
		echo json_encode(array("resultat" => "true", "data" => $nouveau_total));
	}
	else {
		if($achatAndroidClass_affected == 1) {
			$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "Erreur d'enregistrement de la transaction"));
		}
		else if($updateMemberClass_affected == 1) {
			$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "Erreur de sauvegarde des credit au compte"));
		}
		
		
	}
	
}
catch(PDOException $e) {
	$DBH->query('ROLLBACK');
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-android-125"));
}


?>