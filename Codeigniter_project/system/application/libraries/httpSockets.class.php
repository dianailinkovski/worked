<?php

/*
	------HTTPSOCKETS-CLASS--------------------------------
		Created by: Mel C.C. Maranan
		Date: March 06, 2007 GMT 8
		Email: ratfa2004@yahoo.com
	-------------------------------------------------------
*/

class HTTPsockets {
	var $CURHOST = '';
	var $CURLONSSL = false;
	var $CURPROTOCOL = 'http';
	var $USERAGENT = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3';
	var $CH = NULL;
	var $CODE = NULL;

	
	function implodeData( $data_r ){
		$ret = '';
		foreach( $data_r as $key=>$val ){
			$ret .= "$key=$val;";
		}
		return preg_replace( "/;$/i", $ret );
	}
	
	function curl_setopt( $arg1, $arg2 ){
		if( !$this->CH ){
			$this->CH = curl_init();
			$this->curl_setopt( CURLOPT_SSL_VERIFYPEER, 0 );  
            $this->curl_setopt( CURLOPT_USERAGENT, $this->USERAGENT );  
            $this->curl_setopt( CURLOPT_RETURNTRANSFER, 1 );  
            $this->curl_setopt( CURLOPT_FOLLOWLOCATION, 1 );  
            $this->curl_setopt( CURLOPT_HEADER, 1 );
		}
		
		curl_setopt( $this->CH, $arg1, $arg2 );
	}
	
	function curl_exec(){ 
        
		$retData = curl_exec( $this->CH );
		$info  = curl_getinfo($this->CH);
		$this->CODE = $info['http_code'];
		return $retData; 
    }
	
	function onSSL( $url ){
		return preg_match( '/^https:\/\//i', $url );
	}
	 
	function HTTPSockRequest2( $param ){
		$param['host'] = isset( $param['host'] ) ? $param['host'] : '';
		$param['connect_host'] = isset( $param['connect_host'] ) ? $param['connect_host'] : '';
		$param['ssl'] = isset( $param['ssl'] ) ? $param['ssl'] : false;
		$param['port'] = isset( $param['port'] ) ? $param['port'] : ( $param['ssl'] ? '443' : '80' );
		$param['page'] = isset( $param['page'] ) ? $param['page'] : '/';
		$param['method'] = isset( $param['method'] ) ? $param['method'] : 0;
		$param['formcontents'] = isset( $param['formcontents'] ) ? $param['formcontents'] : array();
		$param['cookies'] = isset( $param['cookies'] ) ? $param['cookies'] : array();
		$param['urlencode_form'] = isset( $param['urlencode_form'] ) ? $param['urlencode_form'] : false;
		$param['urlencode_cookie'] = isset( $param['urlencode_cookie'] ) ? $param['urlencode_cookie'] : false;
		$param['referer'] = isset( $param['referer'] ) ? $param['referer'] : '';
		$param['contenttail'] = isset( $param['contenttail'] ) ? $param['contenttail'] : '';
		$param['end_on_recieve'] = isset( $param['end_on_recieve'] ) ? $param['end_on_recieve'] : false;
		$param['files'] = isset( $param['files'] ) ? $param['files'] : array();
		
		$htr['cookies_req'] = '';
		$htr['form_req'] = '';
		$htr['form_req_len'] = 0;
		$htr['file_handle'] = 0;
		$htr['request'] = '';
		$htr['tmp_buff'] = '';
		$htr['ret_page'] = '';
		$is_multipart_form = false;
		$boundary = 'AaB03x';
		$form_content_type = 'application/x-www-form-urlencoded';
		
		foreach( $param['formcontents'] as $fname => $fval ){
			if( isset( $param['files'][$fname] ) ){
				$is_multipart_form = true;
				$form_content_type = 'multipart/form-data; boundary='.$boundary;
				break;
			}
		}
	
		foreach( $param['cookies'] as $cname => $cval ){
			if( trim( $cname ) ) 
				$htr['cookies_req'] .= ( $htr['cookies_req'] ? '; ' : '' ).$cname."=".( $param['urlencode_cookie'] ? urlencode( $cval ) : $cval );
		}
			
		foreach( $param['formcontents'] as $fname => $fval ){
			if( trim( $fname ) ) {
				if( $is_multipart_form ){
					if( isset( $param['files'][$fname] ) ){
						$htr['form_req'] .= "--$boundary"."\r\nContent-Disposition: form-data; name=\"{$fname}\"; filename=\"{$param['files'][$fname]['filename']}\"\r\nContent-Type: {$param['files'][$fname]['content-type']}\r\n\r\n{$fval}\r\n";
					}
					else {
						$htr['form_req'] .= "--$boundary"."\r\nContent-Disposition: form-data; name=\"{$fname}\"\r\n\r\n{$fval}\r\n";
					}
				}
				else {
					$htr['form_req'] .= ( $htr['form_req'] ? '&' : '' ).$fname."=".( $param['urlencode_form'] ? urlencode( $fval ) : $fval );
				}
			}
		}
		if( $is_multipart_form )
			$htr['form_req'] .= "--$boundary--\r\n";
				
		$htr['form_req'] .= $param['contenttail'];
		$htr['form_req'] = trim( $htr['form_req'] );
		$htr['form_req_len'] = strlen( $htr['form_req'] );
		
		if( !( $htr['file_handle'] = fsockopen( ( $param['ssl'] ? 'ssl://' : '' ).$param['host'] , $param['port'], $errno, $errstr ) ) ) 
			return 0;
		else {
			$htr['request'] = ( $param['method'] ? "POST" : "GET" )." ".$param['page']." HTTP/1.1\r\n";
			$htr['request'] .= "Host: ".$param['host']."\r\n";
			$htr['request'] .= "{$this->USERAGENT}"."\r\n";
			$htr['request'] .= $param['referer'] ? "Referer: $param[referer]\r\n" : "";
			$htr['request'] .= $param['method'] ? "Content-Type: $form_content_type\r\n" : "";
			$htr['request'] .= $param['method'] ? "Content-Length: ".$htr['form_req_len']."\r\n" : "";
			$htr['request'] .= $htr['cookies_req'] ? ( "Cookie: ".$htr['cookies_req']."\r\n" ) : '';
			$htr['request'] .= $param['method'] ? "\r\n".$htr['form_req'] : "";
			$htr['request'] .= "Connection: close\r\n\r\n";
			$numbytes = fwrite( $htr['file_handle'], $htr['request'] );	
			
			$response = fgets($htr['file_handle'], 1024);
			$this->CODE = substr($response,9,3);
			//echo( "<!-- \n\n\n{$htr['request']} \n\n\n-->" );
			
			if( $param['end_on_recieve'] ) {
				fclose( $htr['file_handle'] );
				$htr['ret_page'] = "$numbytes"; // Bytes Written
			}
			else {
				while( ( $htr['tmp_buff'] = fgets( $htr['file_handle'] ) ) ){
					$htr['ret_page'] .= $htr['tmp_buff'];
					if( strstr( $htr['tmp_buff'], "HTTP/1.1 200 OK" ) ) $htr['ret_page'] = "";
					if( count( $param['formcontents'] ) && $param['host'] == "mail.myspace.com" && strlen( $htr['ret_page'] ) >= 100 ) break;
					if( trim( $htr['tmp_buff'] ) == "0" ) { break; }
				}
					
				if( $param['method'] == 2 ) die( "REQ:".$htr['request'] );
				fclose( $htr['file_handle'] );
			}
		}
		return $htr['ret_page'];
	}
	
	function openPage( $url, &$cookie_var, $form_contents = array(), $config = array(), $files = array() ){
		preg_match( '/(http[s]?):\/\/([^\/]*)\/?(.*)?/i', $url, $match );
		if( !( $host = isset( $match[2] ) ? $match[2] : '' ) ) return 0;
		$protocol = isset( $match[1] ) ? $match[1] : $this->CURPROTOCOL;
		$page = isset( $match[3] ) ? '/'.$match[3] : '/';
		if( !is_array( $form_contents ) ) $form_contents =  array();
		$this->CURHOST = $host;
		$this->CURPROTOCOL = $protocol;
		
		if( ( isset( $config['use_curl'] ) && $config['use_curl'] ) || ( $this->CURLONSSL && $this->CURPROTOCOL == 'https' ) ){
			if(  isset( $config['use_curl'] ) && $config['use_curl'] ) $this->curl_setopt( CURLOPT_COOKIE, $this->build_cookies_string( $cookie_var ) ); 
			// $use_curl
			$this->curl_setopt( CURLOPT_URL, $url );  
			
			if( count( $form_contents ) > 0 ) {                 
                $this->curl_setopt( CURLOPT_POST, 1 );  
                $this->curl_setopt( CURLOPT_POSTFIELDS, $this->build_form_string( $form_contents ) );  
			}
			else 
				$this->curl_setopt( CURLOPT_POST, 0 );  
			
			$output = $this->curl_exec();
			
		}
		else
		$output = $this->HTTPSockRequest2( 
			array( 
				'host'=>$host,  
				'ssl'=>is_numeric( strpos( strtolower( $url ), 'https' ) ) != false ? true : false,  
				'page'=>$page,  
				'method'=>count( $form_contents ) ? 1 : 0, 
				'urlencode_form'=> isset( $config['urlencode_form'] ) ? $config['urlencode_form'] : true,
				'urlencode_cookie'=>false,
				'formcontents'=>$form_contents,
				'referer'=>isset( $config['referer'] ) ? $config['referer'] : '',
				'cookies'=>$cookie_var, 
				'files'=>$files, 
				'end_on_recieve' => isset( $config['end_on_recieve'] ) ? $config['end_on_recieve'] : false,
			)
		);

		$this->captureCookies( $output, $cookie_var, ( isset( $config['use_curl'] ) && $config['use_curl'] ) || ( $this->CURLONSSL && $this->CURPROTOCOL == 'https' ) );
		return $this->follow_location( $output, $cookie_var );
	}
		
	function follow_location( $page, &$cookie_var ){
		preg_match( '/[\t\n\r]Location: ([^\n\r]*)[\t\n\r]/i', $page, $match );
		
		$url = isset( $match[1] ) ? $match[1] : '';

		if( $url && !preg_match( '/http[s]?/i', $url ) ){
			$url = "{$this->CURPROTOCOL}://{$this->CURHOST}/{$url}";
		}
		return !$url ? $page : $this->openPage( $url, $cookie_var );
	}

	function errorMsg( $text ){

		die( $text );
	}

	function captureCookies( $page, &$varstore, $use_curl = false ){
		preg_match_all( "/Set-Cookie: ([^\=]*)=([^;]*);/i", $page, $matches );
		
		for( $i = 0; $i < count( $matches[1] ); $i++ ){
			if( $matches[1][$i] == 'MYUSERINFO' && $matches[2][$i] == "" ){
				//
			}
			else {
				$varstore[$matches[1][$i]] = $matches[2][$i];
			}
			
			if( $matches[2][$i] == "" ){
				unset( $varstore[$matches[1][$i]] );
			}
		}
		
		if( $use_curl ) $this->curl_setopt( CURLOPT_COOKIE, $this->build_cookies_string( $varstore ) ); 
	}
	
	function build_cookies_string( $cookies = array() ){  
          $cookieze = '';  
            
          foreach( $cookies as $name => $val ){    
               if( $cookieze != "" ) $cookieze .= ";"; 
               $cookieze .= "{$name}={$val}"; 
          } 
           
          return $cookieze; 
    }
	 
	function build_form_string( $forms = array() ){  
          $formize = '';  
            
          foreach( $forms as $name => $val ){    
               if( $formize != "" ) $formize .= "&"; 
               $formize .= "{$name}=".urlencode( $val ); 
          } 
           
          return $formize; 
     }
}
