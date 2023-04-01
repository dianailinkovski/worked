<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";
require_once("Objects/FunctionClass.php");
require_once("PHPMailer-master/class.phpmailer.php");

$username = $_GET['username'];
$password = $_GET['password'];

if($username == "" || $password == "d41d8cd98f00b204e9800998ecf8427e") {
	echo json_encode(array("resultat" => "false", "data" => "Le nom d'utilisateur ou mot de passe que vous avez entré est incorrect."));
	die();
}

//echo json_encode(array("resultat" => "true", "data" => "Message envoyé avec succès"));
//echo json_encode(array("resultat" => "true", "data" => "sera fait durant l'approbation de l'application par Apple"));

try {
	
	$functionObject = new FunctionClass('./');
	
	$usager_id = $functionObject->getIdMembre($username, $password);
	
	// si l'usager existe
	if($usager_id !== FALSE) {
		
		$tokenString = rand_str();
		//$usager_id = $usager['id'];
		
		$DBH->query('BEGIN');
		
		$STH = $DBH->prepare("
		UPDATE `member` 
		SET `token` = :tokenString 
		WHERE `id` = :usager_id 
		LIMIT 1 
		");
		
		$STH->bindParam(":tokenString", $tokenString);
		$STH->bindParam(":usager_id", $usager_id);
		
		$STH->execute();

		$usager_affected = $STH->rowCount();
		
		if($usager_affected == 1) {
			$DBH->query('COMMIT');
			
			
			$url = "http://api.ngser.gnetix.com/v1.1/ActiverMemberWithToken.php?member_id=".$usager_id."&token=".$tokenString;
			
			$html = file_get_contents('emailbasehtml.html');
			$html = str_replace("--URLTOREPLACE--", $url, $html);
			
			//die($html);
			$result = send_email($username, "Activation de votre compte ekiosk mobile", $html, "envoi_ngser@gnetix.com");
			if($result === true) {
				echo json_encode(array("resultat" => "true", "data" => "Courriel envoyé avec succès"));
			}
			else {
				echo json_encode(array("resultat" => "false", "data" => "Erreur d'envoi du message. ".$result));
			}
			
			//echo json_encode(array("resultat" => "true", "data" => "sera fait durant l'approbation de l'application par Apple"));
		}
		else {
			$DBH->query('ROLLBACK');
			echo json_encode(array("resultat" => "false", "data" => "Erreur de création du token"));
		}
		
	}
	
}
catch(PDOException $e) {
	//echo $e->getMessage();
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
}






function rand_str($length = 32, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890') {

    $chars_length = (strlen($chars) - 1);
	
    $string = $chars{rand(0, $chars_length)};

    for ($i = 1; $i < $length; $i = strlen($string)) {

        $r = $chars{rand(0, $chars_length)};
		
        if ($r != $string{$i - 1}) $string .=  $r;
    }

    return $string;
}

function send_email($address, $subject, $body, $from, $from_name = ""){
	
	$mail = new PHPMailer();
					
	$mail->IsSMTP();
	$mail->Host = "mail.gnetix.com";
	$mail->SMTPAuth = true;
	$mail->Username = 'envoi_ngser@gnetix.com';
	$mail->Password = 'Wuspe4a&';
	$mail->Port = 7025;
						
	$mail->From = $from;
	$mail->FromName = $from_name;
	$mail->AddAddress($address);
					
	$mail->IsHTML(true);
					
	$mail->CharSet = 'UTF-8';
					
	$mail->Subject = $subject;
	$mail->Body    = $body;
	$mail->AltBody = "Your e-mail program does not support HTML, the content of this email could not be displayed";
	
	
	//$mail->SMTPDebug = 1;
	
	
	if(!$mail->Send()){
		
		return $mail->ErrorInfo;
		
	} else {
		
		return true;	
	}
}

function createEmail() {
	
	
}

?>