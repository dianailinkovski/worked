<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";
require_once( "Objects/FunctionClass.php");

$username = $_GET['username'];
$password = md5($_GET['password']);

$ekcredit_ipad = $_GET['ekcredit'];


if($username == "" || $password == "d41d8cd98f00b204e9800998ecf8427e") {
	echo json_encode(array("resultat" => "false", "data" => "Le nom d'utilisateur ou mot de passe que vous avez entré est incorrect."));
	die();
}

	try {
		
		$STH = $DBH->prepare("SELECT id, email, password, ek_credit, activated FROM member WHERE email = :username AND password = :password LIMIT 1");
		$STH->bindParam(":username", $username);  
		$STH->bindParam(":password", $password);  
		
		$STH->setFetchMode(PDO::FETCH_OBJ);  
		$STH->execute();
		
		$usager = $STH->fetch();
		# si l'usager existe
		if($usager !== FALSE) {  
			
			$id = $usager->id;
			$email = $usager->email;
			$password = $usager->password;
			$ek_credit = $usager->ek_credit;
			$activated = $usager->activated;
			
			if($ekcredit_ipad != 0) {
				$functionObject = new FunctionClass('./');
				$user_id = $functionObject->getIdMembre($username, $password);
				$quantite_au_compte = $functionObject->getQuantiteFromMember($user_id);
				$nouveau_total = $quantite_au_compte + $ekcredit_ipad;
				$ek_credit = $nouveau_total;
				
				$STH = $DBH->prepare("
				UPDATE `member` 
				SET `ek_credit` = :total 
				WHERE `id` = :user_id
				LIMIT 1
				");
				
				$STH->bindParam(':total', $nouveau_total);
				$STH->bindParam(':user_id', $user_id);
				
				$STH->execute();
				
				$updateMemberClass_affected = $STH->rowCount();
				
				if($updateMemberClass_affected != 1) {
					$DBH->query('ROLLBACK');
					echo json_encode(array("resultat" => "false", "data" => "Erreur lors de l'ajout des crédits déjà achetés au compte."));
				}
			}
			
			
			
			
			//echo "true";
			echo json_encode(array("resultat" => "true", "data" => array('id' => $id, 'email' => $email, 'password' => $password, 'ek_credit'=> $ek_credit, 'activated'=>$activated)));
			
		}
		else {
			//echo "Le nom d'utilisateur ou mot de passe que vous avez entré est incorrect.";
			echo json_encode(array("resultat" => "false", "data" => "Le courriel ou mot de passe que vous avez entré est incorrect."));
		}
	}
	catch(PDOException $e) {
		//echo $e->getMessage();
		echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
	}

?>