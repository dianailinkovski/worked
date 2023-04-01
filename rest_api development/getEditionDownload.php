<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

//include "../includes/functions.php";
include "includes/db.php";
require_once( "Objects/InsertDownloadAchatClass.php");
require_once( "Objects/FunctionClass.php");

$username = isset($_GET['username']) ? $_GET['username'] : "";
$password = isset($_GET['password']) ? $_GET['password'] : "";
$edition_id = isset($_GET['editionid']) ? $_GET['editionid'] : "";
$subscription = isset($_GET['subscription']) ? $_GET['subscription'] : 0;

try {
	$functionObject = new FunctionClass('./');
	$user_id = $functionObject->getIdMembre($username, $password);
	//$edition_id = $functionObject->getEditionIdForUrl($url);
	$achat_id = $functionObject->getIssueForAchat($user_id, $edition_id);
	$url = $functionObject->getUrlForEditionId($edition_id);
	
	$download_id = $functionObject->getDownloadIdIfExist($user_id, $edition_id);
	
	$dateheure = date('Y-m-d H:i:s', time());
	
	if ($subscription) {
		$STH = $DBH->prepare("
		INSERT INTO `editions_download_member` (
		`member_id`, `edition_id`,`date`) 
		VALUES (
		:member_id, :edition_id, :date)
		");
		$STH->execute(array('member_id'=>$user_id, 'edition_id'=>$edition_id, 'date'=>$dateheure));
	}
	else {
		$STH = $DBH->prepare("
		INSERT INTO `editions_download_achat_member` (
		`member_id`, `achat_id`,`date`) 
		VALUES (
		:member_id, :achat_id, :date)
		");
		$downloadAchatClass = new InsertDownloadAchatClass($user_id, $achat_id, $dateheure);
		$STH->execute((array)$downloadAchatClass);
	}
	
	//$downloadEditionClass_affected = $STH->rowCount();
	
	
	$filename = $url;
	
	header('Content-type: application/zip');
	header('Content-Description: File Transfer');
	header('Content-Transfer-Encoding: binary');
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	header("Content-length: " . filesize($filename));  
	//ob_clean();
	ob_end_clean(); //turn off output buffering to decrease cpu usage
	flush();
	readfile($filename);
	
	exit;
	
	//print_r($json);
	//echo json_encode($json);
	//echo json_encode(array("resultat" => "true", "data" => $json));
}
catch(PDOException $e) {
	//echo $e->getMessage();
	//echo json_encode(array("resultat" => "false", "data" => $e->getMessage()));
}


function _readfileChunked($filename, $retbytes=true) {
    $chunksize = 1*(1024*1024); // how many bytes per chunk
    $buffer = '';
    $cnt =0;
    // $handle = fopen($filename, 'rb');
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
        return false;
    }
    while (!feof($handle)) {
        $buffer = fread($handle, $chunksize);
        echo $buffer;
        ob_flush();
        flush();
        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
}

?>
