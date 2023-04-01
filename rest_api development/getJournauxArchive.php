<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";
require_once("Objects/FunctionClass.php");

$id = isset($_GET['id']) ? $_GET['id'] : "";
$username = isset($_GET['username']) ? $_GET['username'] : "";
$password = isset($_GET['password']) ? $_GET['password'] : "";

$functionObject = new FunctionClass('./');

if ($username && $password){
	$member_id = $functionObject->getIdMembre($username, $password);
}


$json = array();



try {
	
	$STH = $DBH->prepare("
	SELECT DISTINCT categorie 
	FROM journal
	");
	
	$STH->execute();
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	
	
	while($row = $STH->fetch()) {
		
		$temp_journal = array();
		
		$temp_journal['categorie'] = $row['categorie'];
		$temp_journal['journaux'] = array();
		
		$STH2 = $DBH->prepare("
		SELECT journal.id, journal.nom, journal.image, subscription.until
		FROM journal
		LEFT JOIN subscription ON journal.id = subscription.journal_id AND until > NOW() AND member_id = '".(int)$member_id."'
		WHERE categorie = :categorie
		");
		
		$STH2->bindParam(":categorie", $row['categorie']);
		$STH2->execute();
		$STH2->setFetchMode(PDO::FETCH_ASSOC);  
		
		
		while($row2 = $STH2->fetch()) {  
			
			$temp_journal2 = array();
			
			$temp_journal2['id'] = $row2['id'];
			$temp_journal2['nom'] = $row2['nom'];
			$temp_journal2['image'] = "http://ngser.gnetix.com/files/_user/journal/".$row2['image'];
			$temp_journal2['isSubscription'] = $row2['until'] ? 1 : 0;
			
			$temp_journal['journaux'][] = $temp_journal2;
			
		
		}
		
		$json[] = $temp_journal;
		
	
	}  
	
	 
	//print_r($json);
	//echo json_encode($json);
	echo json_encode(array("resultat" => "true", "data" => $json));
}
catch(PDOException $e) {
	//echo $e->getMessage();
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
}

?>
