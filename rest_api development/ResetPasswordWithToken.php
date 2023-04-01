<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include "../includes/functions.php";
include "includes/db.php";
require_once("Objects/FunctionClass.php");
require_once("PHPMailer-master/class.phpmailer.php");

$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : "";
$token = isset($_GET['token']) ? $_GET['token'] : "";

$type = isset($_GET['type']) ? $_GET['type'] : "html";

if($type != "html" && $type != "json") {
	$type = "html";
}

if($member_id == "" || $token == "") {
	echo json_encode(array("resultat" => "false", "data" => "Erreur."));
	die();
}


try {
	
	$STH = $DBH->prepare("
	SELECT email, reset_time
	FROM member 
	WHERE id = :member_id 
	AND reset_token = :token 
	LIMIT 1 
	");
	
	$STH->bindParam(":member_id", $member_id);
	$STH->bindParam(":token", $token);
	
	$STH->execute();
	
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	
	$member = $STH->fetch();
	if($member['reset_time'] == '' || time() > strtotime($member['reset_time'].' + 1 day')) {
		$msg = "Ce lien est expiré, veuillez recommencer le processus.";
		if ($type == "html") {
			$html = file_get_contents('emailErrorbasehtml.html');
			$html = str_replace("--TEXTERROR--", $msg, $html);
			die($html);
		}
		else {
			echo json_encode(array("resultat" => "false", "data" => $msg));
		}
		
	}
	else {
		$username = $member['email'];

		if (isset($_POST['password']) && strlen($_POST['password']) > 3 && $_POST['password'] == $_POST['confirm_password'])
		{
			$DBH->query('BEGIN');
			
			$STH = $DBH->prepare("
			UPDATE `member` 
			SET `password` = :password, `reset_token` = NULL, `reset_time` = NULL
			WHERE `id` = :member_id 
			AND `reset_token` = :token 
			LIMIT 1
			");

			$newPassword = md5($_POST['password']);
			
			$STH->bindParam(":token", $token);
			$STH->bindParam(":member_id", $member_id);
			$STH->bindParam(":password", $newPassword);
			
			$STH->execute();

			$usager_affected = $STH->rowCount();
			
			if($usager_affected == 1) {
				$DBH->query('COMMIT');
				
				if ($type == "html") {
					$html = file_get_contents('emailResetSuccessbasehtml.html');
					$result = send_email($username, "Compte ekiosk mobile mot de passe changé", $html, "envoi_ngser@gnetix.com");
					die($html);
				}
				else {
					echo json_encode(array("resultat" => "true", "data" => "Mot de passe changé"));
				}
				
			}
			else {
				$DBH->query('ROLLBACK');
				
				$msg = "Erreur de mot de passe.";
				if ($type == "html") {
					$html = file_get_contents('emailErrorbasehtml.html');
					$html = str_replace("--TEXTERROR--", $msg, $html);
					die($html);
				}
				else {
					echo json_encode(array("resultat" => "false", "data" => $msg));
				}
			}
		} else {
			echo file_get_contents('emailResetPasswordhtml.html');
		}
	}
}
catch(PDOException $e) {
	//echo $e->getMessage();
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
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
?>

