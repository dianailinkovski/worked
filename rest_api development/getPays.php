<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "includes/db.php";

$sql = "SELECT * FROM pays";
$json = array();
try {
	
	$STH = $DBH->prepare($sql);
	$STH->execute();
	$STH->setFetchMode(PDO::FETCH_ASSOC);  

	$x = 0;
	while($row = $STH->fetch()) {  

		$json[$x] = $row;
		
		++$x;
	}  
	
	echo json_encode(array("resultat" => "true", "data" => array('pays'=>$json)));
}
catch(PDOException $e) {
	//echo $e->getMessage();
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
}
?>