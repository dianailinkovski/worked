<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";


//$idrepresentant = 3;
	$json = array();
	
	try {
		
		$STH = $DBH->prepare("
		SELECT id, nom, type, categorie  
		FROM journal 
		");
		$STH->execute();
		$STH->setFetchMode(PDO::FETCH_ASSOC);  
		
		$STH2 = $DBH->prepare("
		SELECT id, id_journal, datePublication, downloadPath, imagePath 
		FROM editions 
		WHERE id_journal = :idjournal
		ORDER BY datePublication DESC
		LIMIT 1
		");
		
		$x = 0;
		while($row = $STH->fetch()) {  
			//print_r($row);
			
			$temp_journal = array();
			
			$temp_journal['id'] = $row['id'];
			$temp_journal['nom'] = $row['nom'];
			$temp_journal['type'] = $row['type'];
			$temp_journal['categorie'] = $row['categorie'];
			$temp_journal['editions'] = array();
			
			$STH2->bindParam(":idjournal", $row['id']);
			$STH2->execute();
			$STH2->setFetchMode(PDO::FETCH_ASSOC);  
			
			$y = 0;
			while($row2 = $STH2->fetch()) { 
				$temp_edition = array(); 
				$temp_edition['id'] = $row2['id'];
				$temp_edition['id_journal'] = $row2['id_journal'];
				$temp_edition['datePublication'] = $row2['datePublication'];
				$temp_edition['downloadPath'] = $row2['downloadPath'];
				$temp_edition['coverPath'] = $row2['imagePath'];
				
				$temp_journal['editions'][$y] = $temp_edition;
				++$y;
			}
			
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