<?php

class FunctionClass {  
    
	public $DBH;
	
	function validateValue($array) {
		if(count($array)>1) {
			return $array[1];
		}
		else {
			return "";
		}
	}
	
	function __construct($basepath = null) {
		if($basepath === null) $basepath = '../';
		//$server = 'mysql.gnetix.net';
	    //$user = 'gofast_user';
	    //$pass = 'Cr28*s*U';
	    //$bd = 'go-fast_07';
	   
		$ini_array = parse_ini_file($basepath."includes/config_bd.ini");
   
		$server = $ini_array['server'];
		$user = $ini_array['user'];
		$pass = $ini_array['pass'];
		$bd = $ini_array['bd'];
	   
	    try {
		    $this->DBH = new PDO("mysql:host=$server;dbname=$bd", $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
		    $this->DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		   
		    //mysql_query("SET NAMES UTF8"); 
		    //$DBH->do('SET NAMES utf8');
		   
		    //$STH = $DBH->prepare('DELECT name FROM people');
		    //$STH->execute();
		    //$DBH->exec() or die(print_r($DBH->errorInfo(), true));
	    }
		catch(PDOException $e) {
			//echo "I'm sorry, Dave. I'm afraid I can't do that.";  
			echo $e->getMessage();
		}
	}
	
	
	
	function getIdMembre($username, $password) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM member 
				WHERE email = :username 
				AND password=:password 
				LIMIT 1
			");
			
			$STH->bindParam(":username", $username);
			$STH->bindParam(":password", $password);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			if (empty($id)){
				return NULL;
			}
			else {
				return $id['id'];
			}
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}

	function getIdMembreFromEmail($email) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM member 
				WHERE email = :username 
				LIMIT 1
			");
			
			$STH->bindParam(":username", $email);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			if (empty($id)){
				return NULL;
			}
			else {
				return $id['id'];
			}
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getPackageMembre($username, $password) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT package_id 
				FROM member 
				WHERE email = :username 
				AND password=:password 
				LIMIT 1
			");
			
			$STH->bindParam(":username", $username);
			$STH->bindParam(":password", $password);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['package_id'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getBoughtIssueMembre($memberId, $productId) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM achat_ekcredit 
				WHERE user_id = :memberId 
				AND edition_id=:productId 
				LIMIT 1
			");
			
			$STH->bindParam(":memberId", $memberId);
			$STH->bindParam(":productId", $productId);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['id'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getIssueForAchat($memberId, $productId) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM achat_ekcredit 
				WHERE user_id = :memberId 
				AND edition_id=:productId 
				ORDER BY date DESC 
				LIMIT 1
			");
			
			$STH->bindParam(":memberId", $memberId);
			$STH->bindParam(":productId", $productId);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			$achatId = $id['id'];
			
			return $achatId;
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getNbTelechargementIssueForAchat($memberId, $productId) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM achat_ekcredit 
				WHERE user_id = :memberId 
				AND edition_id=:productId 
				ORDER BY date DESC 
				LIMIT 1
			");
			
			$STH->bindParam(":memberId", $memberId);
			$STH->bindParam(":productId", $productId);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			$achatId = $id['id'];

			$STH = $this->DBH->prepare("
				SELECT id 
				FROM subscription
				WHERE member_id = :memberId 
				AND journal_id=(SELECT id_journal FROM editions WHERE id=:productId LIMIT 1) 
				LIMIT 1
			");
			
			$STH->bindParam(":memberId", $memberId);
			$STH->bindParam(":productId", $productId);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$subscriptionRow = $STH->fetch();
			
			$subscriptionId = $subscriptionRow['id'];

			if ($achatId == NULL && $subscriptionId == NULL) {
				return -1;
			}
			
			$STH2 = $this->DBH->prepare("
				SELECT count(*) as id 
				FROM editions_download_achat_member 
				WHERE member_id = :memberId 
				AND achat_id=:achatId 
			");
			
			$STH2->bindParam(":memberId", $memberId);
			$STH2->bindParam(":achatId", $achatId);
			$STH2->execute();
			
			$STH2->setFetchMode(PDO::FETCH_ASSOC);  
			
			$count = $STH2->fetch();
				
			$STH3 = $this->DBH->prepare("
				SELECT count(*) as id 
				FROM editions_download_member 
				WHERE member_id = :memberId 
				AND edition_id=:editionId 
			");
			
			$STH3->bindParam(":memberId", $memberId);
			$STH3->bindParam(":editionId", $productId);
			$STH3->execute();

			$STH3->setFetchMode(PDO::FETCH_ASSOC);  
			
			$count2 = $STH3->fetch();

			$reste = 3-$count['id']-$count2['id'];
			
			if($reste < 0) {
				$reste = 0;
			}
			
			return $reste;
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getJournauxForCurrentAbonnement($memberId) {
		try {
			// get journaux
			$STH = $this->DBH->prepare("
				SELECT DISTINCT package_association.journal_id 
				FROM member 
				LEFT JOIN package ON package.id = member.package_id
				LEFT JOIN package_item ON package_item.package_id = package.id
				LEFT JOIN package_association ON package_association.package_item_id = package_item.id
				WHERE member.id = :memberId  
				AND package_association.member_id = :memberId
			");
			
			$STH->bindParam(":memberId", $memberId);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$journaux = array();
			while($row = $STH->fetch()) { 
				$journaux[] = $row['journal_id'];
			}
			
			
			// get date for all abonnement
			$STH = $this->DBH->prepare("
				SELECT date_achat, date_fin 
				FROM achats_package 
				WHERE user_id = :memberId  
			");
			
			$STH->bindParam(":memberId", $memberId);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);
			$date = array();
			while($row = $STH->fetch()) { 
				$date[] = array('date_achat' => $row['date_achat'], 'date_fin' => $row['date_fin']);
			}
			
			
			
			
			return array('journaux' => $journaux, 'date' => $date);
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
	}
	
	function getEditionIdForUrl($url) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM editions 
				WHERE downloadPath = :url 
				LIMIT 1
			");
			
			$STH->bindParam(":url", $url);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['id'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getUrlForEditionId($edition_id) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT downloadPath 
				FROM editions 
				WHERE id = :edition_id 
				LIMIT 1
			");
			
			$STH->bindParam(":edition_id", $edition_id);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['downloadPath'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getDownloadIdIfExist($member_id, $edition_id) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM editions_download_member 
				WHERE member_id = :member_id 
				AND edition_id = :edition_id 
				LIMIT 1
			");
			
			$STH->bindParam(":member_id", $member_id);
			$STH->bindParam(":edition_id", $edition_id);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			if (empty($id)){
				return null;
			}
			else {
				return $id['id'];
			}
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getQuantiteFromIdTransaction($achat_id) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT quantite 
				FROM achats_itunes 
				WHERE id = :achat_id 
				LIMIT 1
			");
			
			$STH->bindParam(":achat_id", $achat_id);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['quantite'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	
	function getQuantiteFromMember($member_id) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT ek_credit 
				FROM member 
				WHERE id = :member_id 
				LIMIT 1
			");
			
			$STH->bindParam(":member_id", $member_id);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['ek_credit'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getIdAndQuantiteFromCode($codeActivation) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT achats_monetique.id, virtual_currency_bundle.quantite, achats_monetique.subscription  
				FROM achats_monetique 
				LEFT JOIN virtual_currency_bundle ON virtual_currency_bundle.id = achats_monetique.bundle_id 
				WHERE achats_monetique.code = :codeActivation 
				AND achats_monetique.used_date IS NULL 
			");
			
			$STH->bindParam(":codeActivation", $codeActivation);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$i = 0;
			$ids = array();
			while($row = $STH->fetch())
			{
				$lastRow = $row;
				if ($row['subscription'])
					$ids[] = $row['id'];
					
				$i++;
			}
			if ($i==0){
				return NULL;
			}
			else {
				return array('id' => ($lastRow['subscription'] ? $ids : $lastRow['id']), 'quantite' => $lastRow['quantite'], 'subscription' => $lastRow['subscription']);
			}
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getQuantiteFromIdTransactionAndroid($achat_id) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT quantite 
				FROM achats_android 
				WHERE id = :achat_id 
				LIMIT 1
			");
			
			$STH->bindParam(":achat_id", $achat_id);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['quantite'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getIdFromPayloadAndSkuTransactionAndroid($payload, $sku) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM achats_android 
				WHERE `payload` = :payload 
				AND `sku` = :sku 
				LIMIT 1
			");
			
			$STH->bindParam(":payload", $payload);
			$STH->bindParam(":sku", $sku);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['id'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getVirtualCurrencyBundleIdForAndroidWithQuantite($quantite) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM virtual_currency_bundle 
				WHERE `quantite` = :quantite 
				AND `visible` = 1 
				LIMIT 1
			");
			
			$STH->bindParam(":quantite", $quantite);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['id'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
		
	}
	
	function getActivatedMember($member_id) {
		
		try {
			
			$STH = $this->DBH->prepare("
				SELECT activated 
				FROM member 
				WHERE id = :member_id 
				LIMIT 1
			");
			
			$STH->bindParam(":member_id", $member_id);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['activated'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
	}
	
	
	function getPubForSection($section) {
		try {
			
			$STH = $this->DBH->prepare("
				SELECT id 
				FROM ads 
				WHERE date_debut <=  NOW() 
				AND (date_fin >= NOW() OR date_fin IS NULL) 
				AND (show_in_section = 0 OR show_in_section = :section) 
				ORDER BY RAND()
				LIMIT 1
			");
			
			$STH->bindParam(":section", $section);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			if(empty($id)) {
				return NULL;
			}
			else {
				return array('image'=>"http://api.ngser.gnetix.com/v1.1/getPubImage.php?ads_id=".$id['id'], 'url'=>"http://api.ngser.gnetix.com/v1.1/getPubLink.php?ads_id=".$id['id']);
			}
			
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
	}
	
	function getPubImage($ads_id) {
		
		try {
			
			$STH = $this->DBH->prepare("
			SELECT image 
			FROM ads
			WHERE id = :ads_id
			LIMIT 1			
			");
			
			$STH->bindParam(":ads_id", $ads_id);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['image'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
	}
	
	function getPubUrl($ads_id) {
		
		try {
			
			$STH = $this->DBH->prepare("
			SELECT url 
			FROM ads
			WHERE id = :ads_id
			LIMIT 1			
			");
			
			$STH->bindParam(":ads_id", $ads_id);
			$STH->execute();
			
			$STH->setFetchMode(PDO::FETCH_ASSOC);  
			
			$id = $STH->fetch();
			
			return $id['url'];
			
		}
		catch(PDOException $e) {
			echo $e->getMessage();
			return NULL;
		}
	}
	
}

?>
