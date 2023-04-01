<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Vision_users 
{
	public function Vision_users() 
	{
		$this->CI =& get_instance();
	}
	
	/**
	 * Change the password for a user.
	 * 
	 * @author Christophe
	 * @param string $email
	 * @param string $old_password
	 * @param string $new_password
	 * @return array
	 */
	public function change_password($email, $old_password, $new_password)
	{
	    $this->CI->load->model('users_m');
	    
	    $user = $this->CI->users_m->get_user_by_email($email);
	    
	    // @todo check to see if old password matches what they entered
	    
	    $new_password = sha1($new_password) . $user['salt'];
	    
	    // update user
	    $update_data = array(
	        'password' => $new_password                
	    );
	    
	    $this->CI->users_m->update_user($user['id'], $update_data);
	    
	    $return_msg = array(
	        'type' => 'success',
	        'message' => 'Password has been successfully updated and saved.'                            
	    );
	    
	    return $return_msg;
	}
	
	/**
	 * Validate login credentials entered by user.
	 * 
	 * @author Christophe
	 * @param string $email
	 * @param string $password
	 * @return array
	 */
	public function check_credentials($email, $password)
	{
	    $this->CI->load->model('users_m');
	     
	    $user = $this->CI->users_m->get_user_by_email($email);
	    	    
	    // check to see if we can find user record
	    if (empty($user))
	    {
	        $return_msg = array(
	            'type' => 'error',
	            'message' => 'User account with entered email address could not be found.'                             
	        );
	    }
	    else
	    {
	        $hashed_password = sha1($password) . $user['salt'];
	        
	        // check to see if passwords match
	        if ($hashed_password == $user['password'])
	        {
	            // update last login
	            $update_data = array(
	                'last_login' => date('Y-m-d H:i:s')                
	            );
	            
	            $this->CI->users_m->update_user($user['id'], $update_data);
	            
	            $return_msg = array(
	            		'type' => 'success',
	            		'message' => $user['uuid']
	            );
	        }
	        else
	        {
	            $return_msg = array(
	            		'type' => 'error',
	            		'message' => 'Password entered is not accurate and does not match user record.'
	            );
	        }
	    }
	    
	    return $return_msg;
	}
	
	/**
	 * Try to get the IP address for the web user.
	 * 
	 * @return string
	 */
	public function get_ip_address()
	{
	    $ip_address = '';
	    
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	    {
	        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
	    }
	    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	    {
	        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else if (isset($_SERVER['HTTP_X_FORWARDED']))
	    {
	        $ip_address = $_SERVER['HTTP_X_FORWARDED'];
	    }
	    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
	    {
	        $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
	    }
	    else if (isset($_SERVER['HTTP_FORWARDED']))
	    {
	        $ip_address = $_SERVER['HTTP_FORWARDED'];
	    }
	    else if (isset($_SERVER['REMOTE_ADDR']))
	    {
	        $ip_address = $_SERVER['REMOTE_ADDR'];
	    }
	    else if (isset($_SERVER['REMOTE_HOST']))
	    {
	        $ip_address = $_SERVER['REMOTE_HOST'];
	    }
	    else
	    {
	        $ip_address = 'UNKNOWN';
	    }
	    
	    return $ip_address;
	}
	
	/**
	 * 
	 * @author Christophe
	 * @param unknown_type $email
	 * @param unknown_type $subject
	 * @param unknown_type $htmlMessage
	 * @param unknown_type $txtMessage
	 */
	public function sendSESEmail($email, $subject, $htmlMessage, $txtMessage = '')
	{
		$CI = & get_instance();
		
		//$SES_Config = $CI->config->item('SES');
		
		$SES_Config = array(
				'service_key' => 'Sticky_Account',
				'url' => 'http://ses.juststicky.com/api/index.php',
				'from' => 'support@juststicky.com',
				'error_email'=>'mrclean73@gmail.com'
		);
	
		if (!is_array($email))
		{
			$email = explode(',', $email);
		}
	
		$userInfo = array(
				'email' => $email,
				'confirmed' => 1
		);
	
		$urltopost = $SES_Config['url'];
		$datatopost = array(
				"service_key" => $SES_Config['service_key'],
				"action" => "sendEmail",
				"email" => $email,
				"from" => $SES_Config['from'],
				'user_info' => $userInfo,
				"message_content" => array(
						"Subject" => $subject,
						"txtMessage" => $txtMessage,
						"htmlMessage" => $htmlMessage
				)
		);
	
		// send curl request
		$ch = curl_init($urltopost);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datatopost));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$returndata = curl_exec($ch);
		curl_close($ch);
	}	
	
}

?>