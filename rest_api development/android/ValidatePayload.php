<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "../includes/db.php";

require_once( "../Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);

$username = isset($_POST['username']) ? $_POST['username'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

$sku = isset($_POST['sku']) ? $_POST['sku'] : "";
$payload = isset($_POST['payload']) ? $_POST['payload'] : "";


$functionObject = new FunctionClass('./../');
$user_id = $functionObject->getIdMembre($username, $password);

try {
	$DBH->query('BEGIN');
	
	/*
	enregistrer la transaction
	*/
	$STH = $DBH->prepare("
		SELECT id 
		FROM `achats_android` 
		WHERE `payload` = :payload 
		AND `sku` = :sku 
		LIMIT 1
	");
	
	
	
	$STH->bindParam(":payload", $payload);
	$STH->bindParam(":sku", $sku);
	$STH->execute();
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	
	$id = NULL;
	
	//print_r($json);
	while($row = $STH->fetch()) {  
		
		$id = $row['id'];
		
	}  
	//print_r($json);
	//echo json_encode($json);
	if($id != NULL) {
		echo json_encode(array("resultat" => "true", "data" => $id));
	} else {
		echo json_encode(array("resultat" => "false", "data" => "Aucun achat correspondant trouvé. SKU=".$sku." CODE=".$payload));
	}
	
	
	
}
catch(PDOException $e) {
	$DBH->query('ROLLBACK');
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-android-124"));
}


?>