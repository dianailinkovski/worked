<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( !function_exists("my_generator_password")) {
	function my_generator_password($pw_length = 8, $user_en = true, $use_caps = true, $use_numeric = true, $use_specials = true) {
		if ( !$user_en && !$use_caps && !$use_numeric && !$use_specials ) {
			$user_en = true;
		}
		$chars = array();
		$caps = array();
		$numbers = array();
		$num_specials = 0;
		$reg_length = $pw_length;
		$pws = array();
		if ($user_en) for ($ch = 97; $ch <= 122; $ch++) $chars[] = $ch; // create a-z
		if ($use_caps) for ($ca = 65; $ca <= 90; $ca++) $caps[] = $ca; // create A-Z
		if ($use_numeric) for ($nu = 48; $nu <= 57; $nu++) $numbers[] = $nu; // create 0-9
		$all = array_merge($chars, $caps, $numbers);
		if ($use_specials) {
			$reg_length =  ceil($pw_length*0.75);
			$num_specials = $pw_length - $reg_length;
			if ($num_specials > 5) $num_specials = 5;
			for ($si = 33; $si <= 47; $si++) $signs[] = $si;
			$rs_keys = array_rand($signs, $num_specials);
			foreach ($rs_keys as $rs) {
				$pws[] = chr($signs[$rs]);
			}
		}
		$rand_keys = array_rand($all, $reg_length);
		foreach ($rand_keys as $rand) {
			$pw[] = chr($all[$rand]);
		}
		$compl = array_merge($pw, $pws);
		shuffle($compl);
		return implode('', $compl);
	}
}

if ( !function_exists("my_encrypt")) {
	function my_encrypt($data) {
		$str = "";
		if ( is_array($data) ) {
			$str = json_encode($data);
		} else {
			$str = $data;
		}

		$s_key = my_generator_password(16, true, true, true, true);
		$s_vector_iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB), MCRYPT_RAND);

		$en_str = mcrypt_encrypt(MCRYPT_3DES, $s_key, $str, MCRYPT_MODE_ECB, $s_vector_iv);
		
		$result = substr($s_key, 0, 8) . base64_encode($en_str) . substr($s_key, 8);
		
		return $result;
	}
}

if ( !function_exists("my_decrypt")) {
	function my_decrypt($data) {
		
		if(empty($data))
			return "";
		
		$s_vector_iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_ECB), MCRYPT_RAND);

		$s_key = substr($data, 0, 8) . substr($data, strlen($data) - 8);
		$str = substr($data, 8, strlen($data) - 16);
		//$de_str = pack("H*", $str);
		$de_str = base64_decode($str);

		return trim(mcrypt_decrypt(MCRYPT_3DES, $s_key, $de_str, MCRYPT_MODE_ECB, $s_vector_iv));
	}
}
