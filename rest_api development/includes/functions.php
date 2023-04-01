<?php

function valid_1byte($char) {
        if(!is_int($char)) return false;
        return ($char & 0x80) == 0x00;
    }
   
    function valid_2byte($char) {
        if(!is_int($char)) return false;
        return ($char & 0xE0) == 0xC0;
    }

    function valid_3byte($char) {
        if(!is_int($char)) return false;
        return ($char & 0xF0) == 0xE0;
    }

    function valid_4byte($char) {
        if(!is_int($char)) return false;
        return ($char & 0xF8) == 0xF0;
    }
   
    function valid_nextbyte($char) {
        if(!is_int($char)) return false;
        return ($char & 0xC0) == 0x80;
    }
   
    function valid_utf8($string) {
        $len = strlen($string);
        $i = 0;   
        while( $i < $len ) {
            $char = ord(substr($string, $i++, 1));
            if(valid_1byte($char)) {    // continue
                continue;
            } else if(valid_2byte($char)) { // check 1 byte
                if(!valid_nextbyte(ord(substr($string, $i++, 1))))
                    return false;
            } else if(valid_3byte($char)) { // check 2 bytes
                if(!valid_nextbyte(ord(substr($string, $i++, 1))))
                    return false;
                if(!valid_nextbyte(ord(substr($string, $i++, 1))))
                    return false;
            } else if(valid_4byte($char)) { // check 3 bytes
                if(!valid_nextbyte(ord(substr($string, $i++, 1))))
                    return false;
                if(!valid_nextbyte(ord(substr($string, $i++, 1))))
                    return false;
                if(!valid_nextbyte(ord(substr($string, $i++, 1))))
                    return false;
            } // goto next char
        }
        return true; // done
    }

function valider_utf8 ($s) {
	/*
	 if ( mb_detect_encoding($s, "UTF-8") == "UTF-8")
	 	return $s;
	 else 
	 	return utf8_encode($s);
		*/
		
	if (valid_utf8($s)){
		return $s;
	}
	else{
		return utf8_encode($s);
	}
}

function delete_accent2($string) {
	return strtr(utf8_decode($string), utf8_decode('àáâãäåòóôõöøèéêëçìíîïùúûüÿñÀÁÂÉÈÊ'), 'aaaaaaooooooeeeeciiiiuuuuynAAAEEE');
}

function delete_accent($string){
	$string =  html_entity_decode($string, UTF-8);
	
	$trans = array(
				   /*"&agrave;" => "à",
				   "&Agrave;" => "À",
				   "&aacute;" => "á",
				   "&Aacute;" => "Á",
				   "&acirc;" => "â",
				   "&Acirc;" => "Â",
				   "&auml;" => "ä",
				   "&Auml;" => "Ä",
				   "&egrave;" => "è",
				   "&Egrave;" => "È",
				   "&eacute;" => "é",
				   "&Eacute;" => "É",
				   "&ecirc;" => "ê",
				   "&Ecirc;" => "Ê",
				   "&euml;" => "ë",
				   "&Euml;" => "Ë",
				   "&icirc;" => "î",
				   "&Icirc;" => "Î",
				   "&iuml;" => "ï",
				   "&Iuml;" => "Ï",
				   "&ocirc;" => "ô",
				   "&Ocirc;" => "Ô",
				   "&ouml;" => "ö",
				   "&ucirc;" => "û", 
				   "&Ucirc;" => "Û",
				   "&uuml;" => "ü",
				   "&Uuml;" => "Ü",
				   "&ccedil;" => "ç",
				   "&Ccedil;" => "Ç",*/
				   "&gt;" => ">",
				   "&lt;" => "<",
				   "&nbsp;" => " ",
				   "&rsquo;" => "'",
				   "&#39;" => "'",
				   "&hellip;" => "…",
				   "&raquo;" => "»",
				   "&laquo;" => "«"
				   );
 	//$string = str_replace("&amp;","&",$string);
	
	
	return strtr($string , $trans);
	
}

//function delete_html($string,$rc){
	/*if($rc){*/
//		$string = str_replace("&nbsp;</p>","\n",$string);
	/*}*/
//	return strip_tags(delete_accent($string));
//}

function StripHTML ($sString) {
    return preg_replace ('@<[\/\!]*?[^<>]*?>@si', '', str_replace('</p>','
',trim($sString)));
}

function delete_ascii($string){
	$text = array("\n", "\r", "\t");
	return preg_replace('/\s\s+/', ' ', str_replace($text, "", $string));
}

function delete_ascii_only($string){
	$text = array("\n", "\r", "\t");
	return str_replace($text, "", $string);
}

function add_accent($string){
$trans1 = array(
				   "à" => "&agrave;",
				   "À" => "&Agrave;",
				   "á" => "&aacute;",
				   "Á" => "&Aacute;",
				   "â" => "&acirc;",
				   "Â" => "&Acirc;",
				   "ä" => "&auml;",
				   "Ä" => "&Auml;",
				   "è" => "&egrave;",
				   "È" => "&Egrave;",
				   "é" => "&eacute;",
				   "É" => "&Eacute;",
				   "ê" => "&ecirc;",
				   "Ê" => "&Ecirc;",
				   "ë" => "&euml;",
				   "Ë" => "&Euml;",
				   "ì" => "&igrave;",
				   "Ì" => "&Igrave;",
				   "í" => "&iacute;",
				   "Í" => "&Iacute;",
				   "î" => "&icirc;",
				   "Î" => "&Icirc;",
				   "ï" => "&iuml;",
				   "Ï" => "&Iuml;",
				   "ò" => "&ograve;",
				   "Ò" => "&Ograve;",
				   "ó" => "&oacute;",
				   "Ó" => "&Oacute;",
				   "ô" => "&ocirc;",
				   "Ô" => "&Ocirc;",
				   "ö" => "&ouml;",
				   "õ" => "&otilde;",
				   "Õ" => "&Otilde;",
				   "ø" => "&oslash;",
				   "Ø" => "&Oslash;",
				   "ù" => "&ugrave;",
				   "Ù" => "&Ugrave;",
				   "ú" => "&uacute;",
				   "Ú" => "&Uacute;",
				   "û" => "&ucirc;", 
				   "Û" => "&Ucirc;",
				   "ü" => "&uuml;",
				   "Ü" => "&Uuml;",
				   "ÿ" => "&yuml;",
				   "Ÿ" => "&Yuml;",
				   "ñ" => "&ntilde;",
				   "Ñ" => "&Ntilde;",
				   "ç" => "&ccedil;",
				   "Ç" => "&Ccedil;",
				   "¿" => "&iquest;",
				   "Æ" => "&Aelig",
				   "æ" => "&aelig;",
				   "ý" => "&yacute;",
				   "€" => "&euro;");

	$string = str_replace("&amp;","&",$string);
	return strtr($string , $trans1);
	
}

function utf8_encode_mix($input, $encode_keys=false)
{
	if(is_array($input))
	{
		$result = array();
		foreach($input as $k => $v)
		{               
			$key = ($encode_keys)? utf8_encode($k) : $k;
			$result[$key] = utf8_encode_mix( $v, $encode_keys);
		}
	}
	else
	{
		$result = utf8_encode($input);
	}

	return $result;
}

function utf8_decode_mix($input, $decode_keys=false)
{
	if(is_array($input))
	{
		$result = array();
		foreach($input as $k => $v)
		{               
			$key = ($decode_keys)? utf8_decode($k) : $k;
			$result[$key] = utf8_decode_mix( $v, $decode_keys);
		}
	}
	else
	{
		$result = utf8_decode($input);
	}

	return $result;
}


function send_email($address, $subject, $body, $from, $from_name = ""){
	$mail = new PHPMailer();
					
	$mail->IsSMTP();
	$mail->Host = "mail.gnetix.com";
	$mail->SMTPAuth = true;
	$mail->Username = 'iphoneapp@gnetix.com';
	$mail->Password = '$E7ub2s!';
	$mail->Port = 25;
						
	$mail->From = $from;
	$mail->FromName = $from_name;
	$mail->AddAddress($address);
					
	$mail->IsHTML(true);
					
	$mail->CharSet = CHARSET;
					
	$mail->Subject = $subject;
	$mail->Body    = $body;
	$mail->AltBody = "Your e-mail program does not support HTML, the content of this email could not be displayed";
					
	if(!$mail->Send()){
		return $mail->ErrorInfo;
	} else {
		return true;	
	}
}


function getNextNumeroCommande($idrepresentant, $DBH) {
	try {
		
		$STH = $DBH->prepare("
			 SELECT COUNT(id)  
			 FROM t_commandes 
			 WHERE numerocommande LIKE :filtrecommande 
		");
		
		
		$filtrecommande = "";
		
		if($idrepresentant < 10) {
			$filtrecommande .= "0";
		}
		
		$filtrecommande .= $idrepresentant."-";
		
		$filtrecommande .= date('ymd',time())."-";
		
		$STH->bindParam(":filtrecommande", $filtrecommande."%");  
		$STH->execute();
		
		$STH->setFetchMode(PDO::FETCH_ASSOC);  
		$row = $STH->fetch();
		
		if(($row['id']+1) < 10) {
			$filtrecommande .= "0";
		}
		$filtrecommande .= ($row['id']+1);
		
		return $filtrecommande;
		
	}
	catch(PDOException $e) {
		//echo $e->getMessage();
		return NULL;
	}
	
}
/*
function getInventaireForIdRepresentant($idrepresentant, $DBH) {
	
	try {
		
		$STH = $DBH->prepare("
			 SELECT id 
			 FROM inventaires
			 WHERE inventaires.idrepresentant = :idrepresentant 
			 AND inventaires.reapprovisionner = 1 
			 ORDER BY inventaires.reapprovisionnerdate DESC 
			 LIMIT 1
		");
		
		$STH->bindParam(":idrepresentant", $idrepresentant);  
		$STH->execute();
		
		$STH->setFetchMode(PDO::FETCH_ASSOC);  
		$row = $STH->fetch();
		
		return $row['id'];
		
	}
	catch(PDOException $e) {
		//echo $e->getMessage();
		return NULL;
	}
	
}*/

?>