<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "includes/db.php";


require_once("Objects/UpdateAchatItunesClass.php");
require_once( "Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);


try {
	$DBH->query('BEGIN');
	
	$transaction_id = $response->receipt->transaction_id;
	$unique_identifier = $response->receipt->unique_identifier;
	
	
	/*
	enregistrer la transaction
	*/
	
	$STH = $DBH->prepare("
	UPDATE `achats_itunes` 
	SET `transaction_id` = :transaction_id, 
	`unique_identifier` = :unique_identifier, 
	`receipt` = :receipt 
	WHERE `id` = :achat_id
	LIMIT 1
	");
	
	//print_r($json);
	$achatItunesClass = new UpdateAchatItunesClass($achat_id, $transaction_id, $unique_identifier, $receiptdata);
	$STH->execute((array)$achatItunesClass);
	
	$achatItunesClass_affected = $STH->rowCount();
	
	if($username == "" || $password == "") {
		$functionObject = new FunctionClass();
		$quantite_a_ajouter = $functionObject->getQuantiteFromIdTransaction($achat_id);
		
		if($achatItunesClass_affected == 1) {
			$DBH->query('COMMIT');
			echo json_encode(array("resultat" => "true", "data" => array('total'=>$quantite_a_ajouter)));
		}
		else {
			$DBH->query('ROLLBACK');
			echo json_encode(array("resultat" => "false", "data" => "Erreur d'enregistrement de la transaction sans compte"));
		}
		
		exit;
	}
	
	$functionObject = new FunctionClass();
	$user_id = $functionObject->getIdMembre($username, $password);
	$quantite_a_ajouter = $functionObject->getQuantiteFromIdTransaction($achat_id);
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
	
	if($achatItunesClass_affected == 1 && $updateMemberClass_affected == 1) {
		$DBH->query('COMMIT');
		echo json_encode(array("resultat" => "true", "data" => array('total'=>$nouveau_total)));
	}
	else {
		if($achatItunesClass_affected == 1) {
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
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-2"));
}


?>