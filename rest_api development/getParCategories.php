<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";

$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : "";


$json = array();
if($categorie == "aujourdhui" || $categorie == "hier") {
	
	
	
	try {
		$date = "";
		if($categorie == "aujourdhui") {
			//$date = date('Y-m-d');
			$date = "2013-12-28";
		}
		if($categorie == "hier") {
			$hier = time() - (24 * 60 * 60);
			$date = date('Y-m-d', $hier);
		}
		
		$STH = $DBH->prepare("
		SELECT editions.id, editions.id_journal, editions.datePublication, editions.downloadPath, editions.imagePath, editions.prix, 
		journal.nom, journal.type, journal.categorie 
		FROM editions 
		LEFT JOIN journal ON journal.id = editions.id_journal AND journal.visible = 1
		WHERE editions.datePublication = :date
		");
		
		$STH->bindParam(":date", $date);
		$STH->execute();
		$STH->setFetchMode(PDO::FETCH_ASSOC);  
		
		
		$x = 0;
		while($row = $STH->fetch()) {  
			//print_r($row);
			
			$temp_journal = array();
			
			$temp_journal['id'] = $row['id'];
			$temp_journal['nom'] = $row['nom'];
			$temp_journal['type'] = $row['type'];
			$temp_journal['categorie'] = $row['categorie'];
			$temp_journal['id_journal'] = $row['id_journal'];
			$temp_journal['datePublication'] = $row['datePublication'];
			$temp_journal['downloadPath'] = $row['downloadPath'];
			$temp_journal['coverPath'] = $row['imagePath'];
			$temp_journal['prix'] = $row['prix'];
			
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

}
else {
	echo json_encode(array("resultat" => "false", "data" => "Cette catégorie n'existe pas ou n'a donné aucun résultat."));
}

?>