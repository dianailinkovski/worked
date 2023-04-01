<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include "includes/db.php";


require_once( "Objects/InsertAchatPackageClass.php");
require_once( "Objects/UpdateMembrePackageClass.php");
require_once( "Objects/InsertAchatAssociationClass.php");
require_once( "Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);

$username = $data[0];
$password = $data[1];

if(count($data) == 4) {
	$package_id = $data[3]->id;
	$selectedJournal = $data[2];
}
else {
	$package_id = NULL;
}


$functionObject = new FunctionClass();
$user_id = $functionObject->getIdMembre($username, $password);

try {
	$DBH->query('BEGIN');
	
	$dateheure = date('Y-m-d H:i:s', time());
	
	
	/*
	enregistrer la transaction
	*/
	
	$STH = $DBH->prepare("
	INSERT INTO `achats_package` (
	`user_id`,`package_id`, `date_achat`,`date_fin`, `original_transaction_id`, `transaction_id`, `unique_identifier`,`date`) 
	VALUES (
	:user_id, :package_id, :date_achat, :date_fin, :original_transaction_id, :transaction_id, :unique_identifier, :date)
	");
	
	//print_r($json);
	$achatAbonnementClass = new InsertAchatPackageClass($response->receipt, $user_id, $package_id, $dateheure);
	$STH->execute((array)$achatAbonnementClass);
	
	$achatAbonnement_affected = $STH->rowCount();
	
	
	if(count($data) != 4) {
		if($achatAbonnement_affected == 1) {
			$DBH->query('COMMIT');
		}
		else {
			$DBH->query('ROLLBACK');
			echo json_encode(array("resultat" => "false", "data" => "Erreur d'enregistrement"));
		}
		die();
	}
	
	
	
	
	
	/*
	ajouter chacun des éléments sélectionner pour le package
	*/
	
	$STH = $DBH->prepare("
	INSERT INTO `package_association` (
	`member_id`,`package_item_id`,`journal_id`) 
	VALUES (
	:member_id, :package_item_id, :journal_id)
	");
	
	$package_association_affected = 0;
	for($y = 0; $y < count($selectedJournal); ++$y) {
		
		$package_item_id = -1;
		
		for($x = 0; $x < count($data[3]->items); ++$x) {
			if($data[3]->items[$x]->type == $selectedJournal[$y]->categorie || $data[3]->items[$x]->type == "Au choix") {
				$package_item_id = $data[3]->items[$x]->id;
				break;
			}
		}
		
		$achatAssociationClass = new InsertAchatAssociationClass($user_id, $package_item_id, $selectedJournal[$y]->id); // CommandesRow
		
		$STH->execute((array)$achatAssociationClass);
		++$package_association_affected;
	}
	
	
	/*
	changer le package dans le membre
	*/
	
	$STH = $DBH->prepare("
	UPDATE `member` 
	SET `package_id` = :package_id 
	WHERE `id` = :member_id
	LIMIT 1
	");
	
	$membrePackageClass = new UpdateMembrePackageClass($user_id, $package_id);
	$STH->execute((array)$membrePackageClass);
	
	$membrePackage_affected = $STH->rowCount();
	
	
	
	if($achatAbonnement_affected != 1) {
		$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "achatAbonnement_affected=".$achatAbonnement_affected));
	}
	//elseif($membrePackage_affected != 1) { // il faudrait que je regard avant si c'est le meme package. pcq si c le meme ca va retourné zero comme row affected
	//	$DBH->query('ROLLBACK');
	//	echo json_encode(array("resultat" => "false", "data" => "membrePackage_affected=".$membrePackage_affected." ".$user_id." ".$package_id));
	//}
	elseif($package_association_affected != count($selectedJournal)) {
		$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "package_association_affected=".$package_association_affected));
	}
	else {
		$DBH->query('COMMIT');
		//echo json_encode(array("resultat" => "true", "data" => array('idcommande'=>$idcommande, 'numerocommande'=>$idcommande, 'commandedateheure'=>$commandedateheure)));
	}
}
catch(PDOException $e) {
	$DBH->query('ROLLBACK');
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-1"));
}


?>