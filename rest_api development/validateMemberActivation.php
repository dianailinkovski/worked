<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";
require_once( "Objects/FunctionClass.php");

$username = $_GET['username'];
$password = $_GET['password'];

if($username == "" || $password == "d41d8cd98f00b204e9800998ecf8427e") {
	echo json_encode(array("resultat" => "false", "data" => "Le nom d'utilisateur ou mot de passe que vous avez entré est incorrect."));
	die();
}

	try {
		
		$STH = $DBH->prepare("SELECT activated FROM member WHERE email = :username AND password = :password LIMIT 1");
		$STH->bindParam(":username", $username);  
		$STH->bindParam(":password", $password);  
		
		$STH->setFetchMode(PDO::FETCH_OBJ);  
		$STH->execute();
		
		$usager = $STH->fetch();
		# si l'usager existe
		if($usager !== FALSE) {  
			
			$activated = $usager->activated;
			
			echo json_encode(array("resultat" => "true", "data" => array('activated'=>$activated)));
			
		}
		else {
			//echo "Le nom d'utilisateur ou mot de passe que vous avez entré est incorrect.";
			echo json_encode(array("resultat" => "false", "data" => "Erreur lors de la vérification de votre compte."));
		}
	}
	catch(PDOException $e) {
		//echo $e->getMessage();
		echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
	}

?>