<?php
/*
 *error_reporting(E_ALL);
 *ini_set('display_errors', '1');
 */

include "includes/db.php";


require_once( "Objects/InsertAchatEkCreditClass.php");
require_once( "Objects/FunctionClass.php");

//$json = json_decode($_GET['data']);

$data = isset($_POST['data']) ? json_decode($_POST['data']) : "";

$username = $data->username;
$password = $data->password;

$code = $data->code;



$functionObject = new FunctionClass('');
$user_id = $functionObject->getIdMembre($username, $password);

try {
	$DBH->query('BEGIN');

	$dateheure = date('Y-m-d H:i:s', time());


	/*
	enregistrer la transaction
	*/

	/////////////
	//faire un log des essai
	/////////////

	/*$STH = $DBH->prepare("
	INSERT INTO `achat_ekcredit` (
	`user_id`,`edition_id`, `package_id`, `quantite`, `date`)
	VALUES (
	:user_id, :edition_id, :package_id, :quantite, :date)
	");

	//print_r($json);
	$achatEkCreditClass = new InsertAchatEkCreditClass($user_id, $edition_id, NULL, $quantite, $dateheure);
	$STH->execute((array)$achatEkCreditClass);

	$achatEkCreditClass_affected = $STH->rowCount();
	*/



	/*
	updater le membre
	*/
	$quantite_au_compte = $functionObject->getQuantiteFromMember($user_id);
	$arrayCode = $functionObject->getIdAndQuantiteFromCode($code);
	if($arrayCode == null) {
		echo json_encode(array("resultat" => "false", "data" => "Code invalide"));
		exit;
	}

	if (!$arrayCode['subscription']) {
		$nouveau_total = $quantite_au_compte + $arrayCode['quantite'];

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
	}
	else {
		$updateSubscriptionClass_affected = 0;
		foreach ($arrayCode['id'] as $id){
			// Getting # of days of this subscription
			$STH = $DBH->prepare("
				SELECT days
				FROM virtual_currency_bundle_subscription
				WHERE id = (SELECT bundle_id FROM achats_monetique WHERE id = :achat_id LIMIT 1)
                        ");

                        $STH->bindParam(":achat_id", $id);
                        $STH->execute();

                        $STH->setFetchMode(PDO::FETCH_ASSOC);

                        $days = $STH->fetch();
			$days = $days['days'];

			// Getting other subscription of the same journal that this user might have
			$STH = $DBH->prepare("
                                SELECT id, until
                                FROM subscription
                                WHERE journal_id = (SELECT journal_id FROM subscription WHERE achat_id = :achat_id)
				AND member_id = :user_id
                                LIMIT 1
                        ");

                        $STH->bindParam(":achat_id", $id);
                        $STH->bindParam(":user_id", $user_id);
                        $STH->execute();

                        $STH->setFetchMode(PDO::FETCH_ASSOC);

                        $existingSub = $STH->fetch();

                        if (empty($existingSub)){
				// No other subscription so just setting the user and the date
				$STH = $DBH->prepare("
					UPDATE `subscription`
					SET `member_id` = :user_id,
					`until` = '".(date('Y-m-d H:i:s', strtotime('+'.$days.' days')))."'
					WHERE `achat_id` = :achat_id
					LIMIT 1
				");
				$STH->bindParam(':achat_id', $id);
				$STH->bindParam(':user_id', $user_id);

				$STH->execute();
                        }
                        else {
				// Update existing subscription with newer date and delete new one
				$STH = $DBH->prepare("
					DELETE FROM `subscription`
					WHERE `achat_id` = :achat_id
					LIMIT 1
				");
				$STH->bindParam(':achat_id', $id);

				$STH->execute();

				$STH = $DBH->prepare("
					UPDATE `subscription`
					SET `member_id` = :user_id,
					`until` = IF (until < NOW(), '".(date('Y-m-d H:i:s', strtotime('+'.$days.' days')))."', '".(date('Y-m-d H:i:s', strtotime($existingSub['until'].' +'.$days.' days')))."')
					WHERE `id` = :existing_id
					LIMIT 1
				");
				$STH->bindParam(':existing_id', $existingSub['id']);
				$STH->bindParam(':user_id', $user_id);

				$STH->execute();
                        }

			$updateSubscriptionClass_affected += $STH->rowCount();
		}
	}

	/*
	updater la date de l'utilisation du code
	*/

	$STH = $DBH->prepare("
	UPDATE `achats_monetique`
	SET `used_date` = :dateheure,
	`user_id` = :user_id
	WHERE `code` = :code AND used_date IS NULL
	");

	$STH->bindParam(':dateheure', $dateheure);
	$STH->bindParam(':user_id', $user_id);
	$STH->bindParam(':code', $code);

	$STH->execute();

	$updateMonetiqueClass_affected = $STH->rowCount();

	if(!$arrayCode['subscription'] && $updateMonetiqueClass_affected == 1 && $updateMemberClass_affected == 1 ) {
		$DBH->query('COMMIT');

		echo json_encode(array("resultat" => "true", "data" => array('total'=>$nouveau_total)));
	}
	elseif($arrayCode['subscription'] && $updateMonetiqueClass_affected == count($arrayCode['id']) && $updateSubscriptionClass_affected == count($arrayCode['id'])) {
		$DBH->query('COMMIT');

		echo json_encode(array("resultat" => "true", "data" => array()));
	}
	else {
		$DBH->query('ROLLBACK');
		echo json_encode(array("resultat" => "false", "data" => "Erreur d'enregistrement de la transaction".$updateMonetiqueClass_affected.'a'.count($arrayCode['id']).'a'.$updateSubscriptionClass_affected));
	}
}
catch(PDOException $e) {
	$DBH->query('ROLLBACK');
	echo json_encode(array("resultat" => "false", "data" => $e->getMessage()." erreur-1"));
}


?>
