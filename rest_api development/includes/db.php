<?php
/************************************** local ****************************************/
/*
   $server = 'localhost';
   $user = 'root';
   $pass = '';
   $bd = 'clubunion_01';
   mysql_connect($server, $user, $pass) or die(mysql_error());
   mysql_select_db($bd) or die(mysql_error());
*/
/************************************* remote ****************************************/
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
   
   $ini_array = parse_ini_file("config_bd.ini");
   
   $server = $ini_array['server'];
   $user = $ini_array['user'];
   $pass = $ini_array['pass'];
   $bd = $ini_array['bd'];
   
   try {
	   $DBH = new PDO("mysql:host=$server;dbname=$bd", $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
	   $DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	   
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
   //$link = mysql_connect($server, $user, $pass) or die(mysql_error());
  // mysql_select_db($bd) or die(mysql_error());
   
   //mysql_query('SET CHARACTER SET utf8');
	
	
	
?>