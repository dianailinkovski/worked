<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "includes/db.php";


require_once( "Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);
$username = $_POST['username'];
$password = $_POST['password'];

try {
	$DBH->query('BEGIN');
	
	$functionObject = new FunctionClass('./');
	$user_id = $functionObject->getIdMembre($username, $password);
	$quantite_au_compte = $functionObject->getQuantiteFromMember($user_id);
	$activated = $functionObject->getActivatedMember($user_id);
	
	echo json_encode(array("resultat" => "true", "data" => array('quantite'=>$quantite_au_compte, 'activated'=>$activated)));
	
}
catch(PDOException $e) {
	$DBH->query('ROLLBACK');
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-2"));
}


?>