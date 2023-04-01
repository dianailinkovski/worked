<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";

require_once( "Objects/FunctionClass.php");

$username = isset($_GET['username']) ? $_GET['username'] : "";
$password = isset($_GET['password']) ? $_GET['password'] : "";

//$idrepresentant = 3;
	$json = array();
	
	try {
		
		$functionObject = new FunctionClass('./');
		$member_id = $functionObject->getIdMembre($username, $password);
		
		
		$STH = $DBH->prepare("
		SELECT editions.id, editions.id_journal, editions.datePublication, editions.downloadPath, editions.imagePath, editions.quantite, 
	journal.nom, journal.type, journal.categorie 
		FROM member 
		LEFT JOIN package_association ON package_association.member_id = member.id 
		LEFT JOIN editions ON editions.id_journal = package_association.journal_id 
		LEFT JOIN editions_download_member ON editions_download_member.member_id = member.id AND editions_download_member.edition_id = editions.id 
		LEFT JOIN achats_package ON achats_package.user_id = member.id AND achats_package.package_id = member.package_id 
		LEFT JOIN journal ON journal.id = editions.id_journal 
		WHERE member.id = :memberId 
		AND editions_download_member.id IS NULL
		AND editions.datePublication BETWEEN achats_package.date_achat AND achats_package.date_fin 
		AND editions.id_journal = package_association.journal_id 
		ORDER BY editions.datePublication ASC
		");
		$STH->bindParam(":memberId", $member_id);
		$STH->execute();
		$STH->setFetchMode(PDO::FETCH_ASSOC);  
		
		
		$x = 0;
		while($row = $STH->fetch()) {  
		//print_r($row);
		
		$temp_journal = array();
		
		$temp_journal['id'] = $row['id'];
		$temp_journal['id_journal'] = $row['id_journal'];
		$temp_journal['nom'] = $row['nom'];
		$temp_journal['type'] = $row['type'];
		$temp_journal['categorie'] = $row['categorie'];
		$temp_journal['id_journal'] = $row['id_journal'];
		$temp_journal['datePublication'] = $row['datePublication'];
		$temp_journal['downloadPath'] = $row['downloadPath'];
		$temp_journal['coverPath'] = $row['imagePath'];
		$temp_journal['prix'] = $row['quantite'];
		
		$json[$x] = $temp_journal;
		
		++$x;
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