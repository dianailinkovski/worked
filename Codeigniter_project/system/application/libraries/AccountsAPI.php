<?php

/**
 *
 * @package   -   AccountsAPI
 *
 * @author    -   Purelogics
 *
 */
class AccountsAPI {
	private $apiUrl;
	private $apiUser;
	private $apiPassword;
	private $cookieName;
	private $sessionId;
	private $appName;
	private $adminCookie;
	private $isAdmin;

	function __construct() {
		$CI =& get_instance();
		$this->CI = $CI;

		$this->apiUrl = $CI->config->item('gsession_api_url');
		$this->apiUser = $CI->config->item('gsession_api_user');
		$this->apiPassword = $CI->config->item('gsession_api_password');
		$this->cookieName = $CI->config->item('global_cookie_name');
		$this->appName = $CI->config->item('sticky_app_name');
		$this->adminCookie = $CI->config->item('global_admin_cookie_name');

		if (isset($_COOKIE[$this->adminCookie])) {
			$this->isAdmin = 1;
		} else {
			$this->isAdmin = 0;
		}
	}

	/**
	 *
	 * function getSessionId
	 *
	 * - read session id from global cookie
	 *
	 */
	public function getSessionIdFromCookie() {
		return isset($_COOKIE[$this->cookieName]) ? $_COOKIE[$this->cookieName] : null;
	}

	/**
	 *
	 * function getSessionId
	 *
	 * @param <string>     $sessionId
	 *
	 *
	 */
	function setSessionId($session_id) {
		$this->sessionId = $session_id;
	}

	/**
	 * If not local environment will call API for accounts app and get user details from that system.
	 *
	 * @author unknown, Christophe
	 * @return array
	 */
	function getUser() 
	{	
		include(APPPATH.'config/database'.EXT);
		
		// for local test environments use Chris' production account details.
		if ($active_group == 'local')
		{
		    $user_data = array(
				    'id' => '678',
				    'first_name' => 'Chris',
		        'last_name' => 'Programmer',
		        'email' => 'chris@juststicky.com',
		        'terms_accepted' => 1,
		        'user_type' => '1',
		        'permission_id' => '1',
		        'owner_email' => '',
		        'owner_id' => '126',
		        'is_invited' => '1',
		        'app_id' => '3',
		        'allow' => 1,
		        'addons' => array(),
		        'user_teams' => array(
		            array(
		                'id' => '53',
		                'app_terms_id' => '2',
		                'member_id' => '678',
		                'team_id' => '126',
		                'product_id' => '3',
		                'permission_id' => '1',                                                                             
		            )                
		        ),
		        'is_admin' => '1',
		        'application_settings' => array()                                                                                                                                                                        			
		    );
		    
		    return $user_data;
		}
		else
		{
			$data = array(
					'action'  => 'get_user',
					'fields' => array('app_name' => $this->appName)
			);
			
			return $this->request($data);			
		}
	}

	/**
	 *
	 * function getUserPackages
	 *
	 *
	 */
	function getUserPackages($email) {
		$data = array(
			'action'  => 'get_user_packages',
			'fields' => array('app_name' => $this->appName, 'email' => $email)
		);

		return $this->request($data);
	}

	/**
	 *
	 * function getUserTeams
	 *
	 *
	 */
	function getUserTeams($email, $rights = 0) {
		$data = array(
			'action'  => 'get_user_teams',
			'fields' => array('email' => $email, 'app_name' => $this->appName, 'rights' => $rights)
		);

		return $this->request($data);
	}

	/**
	 * function getUserNotifications
	 *
	 */
	function getUserNotifications($email) {
		$data = array(
			'action'  => 'get_user_notifications',
			'fields' => array('email' => $email, 'app_name' => $this->appName)
		);

		return $this->request($data);
	}

	/**
	 * function getUserNotifications
	 *
	 */
	function readNotifications($id) {
		$data = array(
			'action'  => 'read_notification',
			'fields' => array('id' => $id)
		);

		return $this->request($data);
	}

	/**
	 *
	 * function saveUserAppplication
	 *
	 *
	 */
	function saveUserAppplication() {
		$data = array(
			'action'  => 'save_user_application',
			'fields' => array('app_name' => $this->appName)
		);

		return $this->request($data);
	}

	/**
	 * function setUserTerms
	 *
	 *
	 */
	function saveUserTerms($id) {
		$data = array(
			'action' => 'save_user_terms',
			'fields' => array('app_name' => $this->appName, 'term_id' => $id)
		);
		return $this->request($data);
	}

	/**
	 * function getUserTerms
	 *
	 *
	 */
	function getUserTerms() {
		$data = array(
			'action' => 'get_user_terms',
			'fields' => array('app_name' => $this->appName)
		);
		return $this->request($data);
	}

	/**
	 * function updateUserPassword
	 *
	 *
	 */
	function updateUserPassword($password, $repassword) {
		$data = array(
			'action' => 'update_password',
			'fields' => array('password' => $password, 'confirmPassword' => $repassword)
		);
		return $this->request($data);
	}

	/**
	 *
	 * function prepareUrl
	 *
	 * @param <string>     $action
	 *
	 *
	 */
	private function prepareUrl(&$action) {
		$action = $this->apiUrl.'/'.$action;
	}

	/**
	 *
	 * function prepareData
	 *
	 * @param <array>     $data
	 *
	 *
	 */
	private function prepareData(&$data) {
		if (!$this->sessionId) {
			$this->sessionId = $this->getSessionIdFromCookie();
		}

		$data['fields']['session_id'] = $this->sessionId;
		$data['fields']['is_admin'] = $this->isAdmin;
	}

	/**
	 *
	 * function request
	 *
	 * @param <array>     $data
	 *
	 *
	 */
	private function request($data) {
		// make url
		$this->prepareUrl($data['action']);

		// check session id
		$this->prepareData($data);
		
		//var_dump($data['fields']['session_id']); exit();

		// return null if no session
		if (!$data['fields']['session_id']) {
			return null;
		}

		return $this->_request($data);
	}

	/**
	 *
	 * function _request
	 *
	 * @param <array>     $data
	 *
	 *
	 */
	private function _request($data) {
		// send curl request
		//debug('DATA',$data,2);exit;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $data['action']);

		if (isset($data['fields'])) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data['fields']);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');
		curl_setopt($ch, CURLOPT_USERPWD, $this->apiUser . ':' . $this->apiPassword);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

		$result = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
		//var_dump($error); exit();
		
		
		/*echo "<br>Result : ".$result;
		echo "<br>Error : ".$error;
		exit;*/

		return json_decode($result, true);
	}
}