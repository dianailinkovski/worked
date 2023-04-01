<?php
/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
//include "../includes/functions.php";
include "includes/db.php";
require_once( "Objects/FunctionClass.php");

$functionObject = new FunctionClass('./');

$username = isset($_GET['username']) ? $_GET['username'] : "";
$password = isset($_GET['password']) ? $_GET['password'] : "";
$member_id = $functionObject->getIdMembre($username, $password);

$categorie = isset($_GET['categorie']) ? $_GET['categorie'] : "";
$pays = isset($_GET['pays']) ? (int)$_GET['pays'] : "";
$abonnement = isset($_GET['abonnement']) ? $_GET['abonnement'] : "";

if($categorie == "" || $categorie == "Tous") {
	$sql = "
		SELECT journal.nom, journal.type, journal.categorie, pays.id AS pays_id, pays.nom AS pays_nom, pays.abbrev AS pays_abbrev, pays.image AS pays_image, 
		editions.id, editions.id_journal, editions.datePublication, editions.downloadPath, editions.imagePath, editions.quantite, subscription.until  
		FROM journal 
		LEFT JOIN subscription ON journal.id = subscription.journal_id AND until > NOW() AND member_id = '".(int)$member_id."'
		LEFT JOIN pays ON journal.id_pays = pays.id
		LEFT JOIN editions ON editions.id_journal = journal.id 
			AND editions.visible = 1 
			AND editions.datePublication = (SELECT MAX(editions.datePublication) FROM editions WHERE editions.id_journal = journal.id AND editions.visible=1)
		WHERE editions.id IS NOT NULL
		AND journal.visible = 1
		".($pays != '' ? " AND journal.id_pays = '".$pays."'" : '')."
		".($abonnement ? " AND subscription.until <> '' " : '')."
		
		GROUP BY journal.id
		ORDER BY journal.nom ASC ,editions.datePublication ASC
		";
}
else {
	$sql = "
		SELECT journal.nom, journal.type, journal.categorie, pays.id AS pays_id, pays.nom AS pays_nom, pays.abbrev AS pays_abbrev, pays.image AS pays_image, 
		editions.id, editions.id_journal, editions.datePublication, editions.downloadPath, editions.imagePath, editions.quantite, subscription.until  
				FROM journal 
		LEFT JOIN subscription ON journal.id = subscription.journal_id AND until > NOW() AND member_id = '".(int)$member_id."'
		LEFT JOIN pays ON journal.id_pays = pays.id
		LEFT JOIN editions ON editions.id_journal = journal.id 
			AND editions.visible = 1 
			AND editions.datePublication = (SELECT MAX(editions.datePublication) FROM editions WHERE editions.id_journal = journal.id AND editions.visible=1)
		WHERE journal.categorie = '".$categorie."' 
		AND journal.visible = 1
		".($pays != '' ? " AND journal.id_pays = '".$pays."'" : '')."
		AND editions.id IS NOT NULL
		".($abonnement ? " AND subscription.until <> '' " : '')."
		
		GROUP BY journal.id
		ORDER BY journal.nom ASC ,editions.datePublication ASC
		";
}

//$idrepresentant = 3;
	$json = array();
	//$abonnementArray = $functionObject->getJournauxForCurrentAbonnement($member_id);
	try {
		
		$STH = $DBH->prepare($sql);
		$STH->execute();
		$STH->setFetchMode(PDO::FETCH_ASSOC);  
		
		//$STH2 = $DBH->prepare("
		//SELECT editions.id, editions.id_journal, editions.datePublication, editions.downloadPath, editions.imagePath, editions.quantite 
		//FROM editions 
		//WHERE editions.id_journal = :idjournal
		//ORDER BY editions.datePublication DESC
		//LIMIT 1
		//");
		
		$x = 0;
		while($row = $STH->fetch()) {  
			//print_r($row);
			
			$temp_journal = array();
			
			
			$temp_journal['nom'] = $row['nom'];
			$temp_journal['type'] = $row['type'];
			$temp_journal['categorie'] = $row['categorie'];
			$temp_journal['pays_id'] = $row['pays_id'];
			$temp_journal['pays_nom'] = $row['pays_nom'];
			$temp_journal['pays_abbrev'] = $row['pays_abbrev'];
			$temp_journal['pays_image'] = $row['pays_image'];
			$temp_journal['isSubscription'] = $row['until'] ? 1 : 0;
			
			//$STH2->bindParam(":idjournal", $row['id']);
			//$STH2->execute();
			//$STH2->setFetchMode(PDO::FETCH_ASSOC);  
			//while($row2 = $STH2->fetch()) { 
			
				$temp_journal['id'] = $row['id'];
				$temp_journal['id_journal'] = $row['id_journal'];
				$temp_journal['datePublication'] = $row['datePublication'];
				$temp_journal['downloadPath'] = $row['downloadPath'];
				$temp_journal['coverPath'] = $row['imagePath'];
				$temp_journal['prix'] = $row['quantite'];
				
				//$bought_id = $functionObject->getBoughtIssueMembre($member_id, $row['id']);
				//if($bought_id != NULL) {
					//$temp_journal['bought'] = 1;
				$temp_journal['telechargementRestant'] = $functionObject->getNbTelechargementIssueForAchat($member_id, $row['id']);
				//}
				//else {
				//	$temp_journal['bought'] = verifProductForAbonnement($row, $abonnementArray);
				//}
			//}
			
			
			$json[$x] = $temp_journal;
			
			++$x;
		}  
		//print_r($json);
		//echo json_encode($json);
		
		$topPub = $member_id = $functionObject->getPubForSection(1);
		$bottomPub = $member_id = $functionObject->getPubForSection(2);
		
		if($topPub == NULL){
			$topPub = array('image'=>'', 'url'=>'');
		}
		
		if($bottomPub == NULL){
			$bottomPub = array('image'=>'', 'url'=>'');
		}
		
		echo json_encode(array("resultat" => "true", "data" => array('publications'=>$json, 'topPub'=>$topPub, 'bottomPub'=>$bottomPub)));
	}
	catch(PDOException $e) {
    	//echo $e->getMessage();
		echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
	}

// a déplacer dans un fichier de fonction
function verifProductForAbonnement($productArray, $abonnementArray) {
	$productDate = date('Y-m-d', strtotime($productArray['datePublication']));
	$journauxId = $productArray['id_journal'];
	
	$journauxArray = $abonnementArray['journaux'];
	$dateArray = $abonnementArray['date'];
    
	for($x = 0; $x < count($journauxArray); ++$x) {
		if($journauxId == $journauxArray[$x]) {
			for($y = 0; $y < count($dateArray); ++$y) {
				
				$dateBegin = date('Y-m-d', strtotime($dateArray[$y]['date_achat']));
			    $dateEnd = date('Y-m-d', strtotime($dateArray[$y]['date_fin']));
				
				if (($productDate >= $dateBegin) && ($productDate <= $dateEnd)) {
					return 1;
				}
				
			}
		}
	}
	
	return 0;	
}
?>
