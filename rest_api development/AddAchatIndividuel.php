<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "includes/db.php";


require_once( "Objects/InsertAchatEkCreditClass.php");
require_once( "Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);

$data = isset($_POST['data']) ? json_decode($_POST['data']) : "";

$username = $data->username;
$password = $data->password;

$edition_id = $data->editionid;
$quantite = $data->quantite;


$functionObject = new FunctionClass('./');
$user_id = $functionObject->getIdMembre($username, $password);

try {
	$DBH->query('BEGIN');
	
	$dateheure = date('Y-m-d H:i:s', time());
	
	
	/*
	enregistrer la transaction
	*/
	
	$STH = $DBH->prepare("
	INSERT INTO `achat_ekcredit` (
	`user_id`,`edition_id`, `package_id`, `quantite`, `date`) 
	VALUES (
	:user_id, :edition_id, :package_id, :quantite, :date)
	");
	
	//print_r($json);
	$achatEkCreditClass = new InsertAchatEkCreditClass($user_id, $edition_id, NULL, $quantite, $dateheure);
	$STH->execute((array)$achatEkCreditClass);
	
	$achatEkCreditClass_affected = $STH->rowCount();
	
	
	
	if($username == "" || $password == "") {
		if($achatEkCreditClass_affected == 1) {
			$DBH->query('COMMIT');
			
			echo json_encode(array("resultat" => "true", "data" => array('total'=>$quantite)));
		}
		else {
		
			$DBH->query('ROLLBACK');
			echo json_encode(array("resultat" => "false", "data" => "Erreur d'enregistrement de la transaction"));
		}
		
		exit;
	}
	
	$quantite_au_compte = $functionObject->getQuantiteFromMember($user_id);
	$nouveau_total = $quantite_au_compte - $quantite;
	
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
	
	
	if($achatEkCreditClass_affected == 1 && ($updateMemberClass_affected == 1 || $quantite == 0)) {
		$DBH->query('COMMIT');
		
		echo json_encode(array("resultat" => "true", "data" => array('total'=>$nouveau_total)));
	}
	else {
		if($achatEkCreditClass_affected == 1) {
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
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-1"));
}


?>