<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";
require_once( "Objects/FunctionClass.php");


//$idrepresentant = 3;
	$json = array();
	
	$username = isset($_GET['username']) ? $_GET['username'] : "";
	$password = isset($_GET['password']) ? $_GET['password'] : "";
	try {
		
		$functionObject = new FunctionClass('./');
		$package_id = $functionObject->getPackageMembre($username, $password);
		
		$STH = $DBH->prepare("
		SELECT id, nom, prix_1, prix_3 
		FROM package 
		WHERE deactivated = 0
		");
		$STH->execute();
		$STH->setFetchMode(PDO::FETCH_ASSOC);  
		
		$STH2 = $DBH->prepare("
		SELECT id, type, amount 
		FROM package_item  
		WHERE package_id = :idpackage 
		ORDER BY rank ASC
		");
		
		$STH3 = $DBH->prepare("
		SELECT id, type, itunes, google, prix_usd 
		FROM prix 
		WHERE id = :idprix
		");
		
		$x = 0;
		while($row = $STH->fetch()) {  
			//print_r($row);
			
			$temp_abonnement = array();
			
			$temp_abonnement['id'] = $row['id'];
			$temp_abonnement['nom'] = $row['nom'];
			$temp_abonnement['prix_1'] = array();
			$temp_abonnement['prix_3'] = array();
			$temp_abonnement['items'] = array();
			
			if($package_id != NULL && $package_id == $row['id']) {
				$temp_abonnement['bought'] = 1;
			}
			else {
				$temp_abonnement['bought'] = 0;
			}
			
			$STH3->bindParam(":idprix", $row['prix_1']);
			$STH3->execute();
			$STH3->setFetchMode(PDO::FETCH_ASSOC);  
			$tempprix = $STH3->fetch();
			
			$temp_abonnement['prix_1']['prix'] = $tempprix['prix_usd'];
			$temp_abonnement['prix_1']['itunes'] = $tempprix['itunes'];
			if($row['prix_3'] != NULL) {
				$STH3->bindParam(":idprix", $row['prix_3']);
				$STH3->execute();
				$STH3->setFetchMode(PDO::FETCH_ASSOC);  
				$tempprix = $STH3->fetch();
				
				$temp_abonnement['prix_3']['prix'] = $tempprix['prix_usd'];
				$temp_abonnement['prix_3']['itunes'] = $tempprix['itunes'];
			}
			
			
			
			
			
			
			
			
			
			$STH2->bindParam(":idpackage", $row['id']);
			$STH2->execute();
			$STH2->setFetchMode(PDO::FETCH_ASSOC);  
			
			$y = 0;
			while($row2 = $STH2->fetch()) { 
				$temp_item = array(); 
				$temp_item['id'] = $row2['id'];
				$temp_item['type'] = $row2['type'];
				$temp_item['amount'] = $row2['amount'];
				
				$temp_abonnement['items'][$y] = $temp_item;
				++$y;
			}
			
			$json[$x] = $temp_abonnement;
			
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