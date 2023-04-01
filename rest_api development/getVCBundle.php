<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
require "includes/db.php";

$json = array();

try {
	
	$STH = $DBH->prepare("
	SELECT id, nom, itunes, google, quantite, prix_usd, equivalent, bonis 
	FROM virtual_currency_bundle 
	WHERE visible = 1 
	");
	
	$STH->execute();
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	
	
	$x = 0;
	while($row = $STH->fetch()) {  
		//print_r($row);
		
		$temp_bundle = array();
		
		$temp_bundle['id'] = $row['id'];
		$temp_bundle['nom'] = $row['nom'];
		$temp_bundle['itunes'] = $row['itunes'];
		$temp_bundle['google'] = $row['google'];
		$temp_bundle['quantite'] = $row['quantite'];
		$temp_bundle['prix_usd'] = $row['prix_usd'];
		$temp_bundle['equivalent'] = $row['equivalent'];
		$temp_bundle['bonis'] = $row['bonis'];
		
		$json[$x] = $temp_bundle;
		
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