<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

//include "../includes/functions.php";
include "includes/db.php";
require_once( "Objects/FunctionClass.php");

$username = isset($_GET['username']) ? $_GET['username'] : "";
$password = isset($_GET['password']) ? $_GET['password'] : "";

$ads_id = isset($_GET['ads_id']) ? $_GET['ads_id'] : "";

try {
	
	$functionObject = new FunctionClass('./');
	
	$member_id = $functionObject->getIdMembre($username, $password);
	$image = $functionObject->getPubImage($ads_id);
	$date = date( 'Y-m-d', time() );
	
	$STH = $DBH->prepare("
	SELECT id, count 
	FROM ads_displayed 
	WHERE ads_id = :ads_id 
	AND date = :date 
	LIMIT 1");
	
	$STH->bindParam(":ads_id", $ads_id);
	$STH->bindParam(":date", $date);
		
	$STH->execute();
	$STH->setFetchMode(PDO::FETCH_ASSOC);  
	$row = $STH->fetch();
	
	if(empty($row)) {
		$STH = $DBH->prepare("
		INSERT INTO `ads_displayed` (`ads_id`, `date`) 
		VALUES (:ads_id, :date) 
		");
		
		$STH->bindParam(":ads_id", $ads_id);
		$STH->bindParam(":date", $date);
		
		$STH->execute();
		
		$tacking_id = $DBH->lastInsertId();
		$count = 0;
		
	}else {
		$tacking_id = $row['id'];
		$count = $row['count'];
	}
	
	
	$count = $count+1;
	
	
	
	$DBH->query('BEGIN');
	
	$STH = $DBH->prepare("
	UPDATE ads_displayed 
	SET count = :count 
	WHERE id = :tacking_id 
	LIMIT 1
	");
	
	$STH->bindParam(":tacking_id", $tacking_id);
	$STH->bindParam(":count", $count);
	
	$STH->execute();
	
	$insertDisplayedCount = $STH->rowCount();
	
	if($insertDisplayedCount == 1) {
		$DBH->query('COMMIT');
		
		if(strpos($image, ".png") !== false) {
			$image = "http://ngser.gnetix.com/files/_user/ads/".$image;
			$image = str_replace(".png", "_m.png", $image);
		
			header("Content-Type: image/png");  
			$im = imagecreatefrompng($image);
			imagepng($im);
			imagedestroy($im);
			exit;
		}
		else if(strpos($image, ".jpg") !== false) {
			$image = "http://ngser.gnetix.com/files/_user/ads/".$image;
			$image = str_replace(".jpg", "_m.jpg", $image);
		
			header("Content-Type: image/jpg");
			$im = imagecreatefromjpeg($image);
			imagepng($im);
			imagedestroy($im);
			exit;
		}
		else if(strpos($image, ".jpeg") !== false) {
			$image = "http://ngser.gnetix.com/files/_user/ads/".$image;
			$image = str_replace(".jpeg", "_m.jpeg", $image);
		
			header("Content-Type: image/jpeg");
			$im = imagecreatefromjpeg($image);
			imagepng($im);
			imagedestroy($im);
			exit;
		}
		
	}
	else {
		$DBH->query('ROLLBACK');
	}
	
	
	
	
	//print_r($json);
	//echo json_encode($json);
	//echo json_encode(array("resultat" => "true", "data" => $json));
}
catch(PDOException $e) {
	$DBH->query('ROLLBACK');
	//echo $e->getMessage();
	//echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
}

?>