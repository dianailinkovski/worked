	
<?php
	
	$ini_array = parse_ini_file("../includes/config_bd.ini");
   
   $server = $ini_array['server'];
   $user = $ini_array['user'];
   $pass = $ini_array['pass'];
   $bd = $ini_array['bd'];
	
	//$user = "ngser_user";
	//$password = "+E2rEguB";
	//$dbname = "NGSER_01";
	
	$prod = filter_input(INPUT_POST, 'productid', FILTER_SANITIZE_NUMBER_INT);
	$udid = filter_input(INPUT_POST, 'udid', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_ENCODED);
    // Connect to database server

    $hd = mysql_connect($server, $user, $pass)

          or die ("Unable to connect");

    // Select database

    mysql_select_db ($bd, $hd)

          or die ("Unable to select database");

    // Execute sample query (insert it into mksync all data in customer table)

    $res = mysql_query("INSERT INTO NGSER_01.requests(udid, productid, email, message) VALUES ('".mysql_real_escape_string($udid)."', '".mysql_real_escape_string($prod)."', '".mysql_real_escape_string($email)."', '".mysql_real_escape_string($message)."')",$hd)

          or die ("Unable to insert :-(");
	
 	mysql_close($hd);
 	
 	echo "Done!";
?>
