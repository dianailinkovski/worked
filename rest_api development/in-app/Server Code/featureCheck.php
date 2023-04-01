<?php

$ini_array = parse_ini_file("../includes/config_bd.ini");
   
   $server = $ini_array['server'];
   $user = $ini_array['user'];
   $pass = $ini_array['pass'];
   $bd = $ini_array['bd'];
   
	//$dbname = "NGSER_01";
	//$user = "ngser_user";
	//$password = "+E2rEguB";
	
	header('Content-Type: text/plain'); 
	$prod = filter_input(INPUT_POST,'productid',FILTER_SANITIZE_STRING); // varchar(255)
	$udid = filter_input(INPUT_POST,'udid',FILTER_SANITIZE_STRING); // varchar(255)
	
	// Connect to database server
	$hd = mysql_connect($server, $user, $pass) or die ("Unable to connect = ".mysql_error());

	// Select database
	mysql_select_db ($bd, $hd) or die ("Unable to select database");

	// Execute sample query (insert it into mksync all data in customer table)

	$res = mysql_query("SELECT * FROM NGSER_01.requests where udid='".mysql_real_escape_string($udid)."' AND productid='".mysql_real_escape_string($prod)."' AND status = 1",$hd) or die ("Unable to select :-(");

	
	$num = mysql_num_rows($res);
		
	if($num == 0)
		$returnString = "NO";
	else
		$returnString = "YES";
		
	mysql_close($hd);
	echo $returnString;
?>
