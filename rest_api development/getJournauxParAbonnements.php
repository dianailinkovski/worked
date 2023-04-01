<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";

$categorie = isset($_GET['categorie']) ? explode("-,-", $_GET['categorie']) : "";

//$idrepresentant = 3;
	$json = array();
	
	try {
		
		for($y = 0; $y < count($categorie); ++$y) {
			
			if($categorie[$y] == "Au choix") { 
				$STH = $DBH->prepare("
				SELECT id, nom, type, categorie, image 
				FROM journal 
				");
			}
			else {
				
				$STH = $DBH->prepare("
				SELECT id, nom, type, categorie, image 
				FROM journal 
				WHERE categorie = :categorie AND visible=1
				");
				$STH->bindParam(":categorie", $categorie[$y]);
				
			}
			
			$STH->execute();
			$STH->setFetchMode(PDO::FETCH_ASSOC); 
			
			$temp_section = array();
			$temp_section['title'] = $categorie[$y];
			$temp_section['journaux'] = array();
			
			$x = 0;
			while($row = $STH->fetch()) {  
				//print_r($row);
				
				$temp = array();
				$temp['id'] = $row['id'];
				$temp['nom'] = $row['nom'];
				$temp['type'] = $row['type'];
				$temp['categorie'] = $row['categorie'];
				$temp['image'] = 'http://24.37.121.170/ngser/public_html/files/_user/journal/'.$row['image'];
				$temp['selected'] = 0;
				
				$temp_section['journaux'][$x] = $temp;
				
				++$x;
			}
			$json[$y] = $temp_section;
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