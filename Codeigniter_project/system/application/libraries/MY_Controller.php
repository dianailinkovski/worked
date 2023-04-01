<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Controller.php
 *
 * Add custom functionality to the CI controller
 */
class MY_Controller extends Controller 
{
    public $data;
    public $x_logon;
    public $record_per_page;
    protected $_bookmarks;
    protected $_layout;
    protected $_view;
    protected $_javascript_files = array();
    protected $_javascript_dir;
    protected $_acl = array('*' => 'user');
    protected $_controller;
    protected $_method;
    protected $_response_type = 'html';
    public $allBrands = null;
    protected $_tpl_data = array();

    protected $subscriber_addons = array();

    public $store_id = null;

    public function __construct() 
    {
		    parent::Controller();
		
		    $this->_controller = $this->router->fetch_class();
		    $this->_method = $this->router->fetch_method();

		    $this->_load_tables();
		    $this->_init_color_handler();

		    $this->_layout = 'default';
		    $this->_view = $this->router->fetch_directory() . '/' . $this->_controller . '/' . $this->_method;
		    
		    $this->page_title = 'TrackStreet';
		    
		    // another var to hold logged in user data
		    $this->user = FALSE;

		    $this->_javascript_dir = 'js/';
		    //$this->javascript('jqwidgets/jqx-all.js');
		    $this->javascript('ajaxtooltip.js');
		    $this->javascript('dynamic.js.php');

		    $this->record_per_page = $this->config->item("record_per_page");
                
		    $this->_login();
		    
		    $uri = $this->uri->uri_string();
		    
		    // pages that don't use the left menu - mainly public-facing pages
		    if (
		        $uri != '/logout' 
		        && $uri != '/login' 
		        && strstr($uri, '/cron/') == FALSE            
		        && strstr($uri, '/signup/confirm/') == FALSE
		        && strstr($uri, '/signup/password/') == FALSE
		        && strstr($uri, '/signup/forgot_password/') == FALSE
		        && strstr($uri, '/signup/reset_password/') == FALSE
		        && strstr($uri, '/signup/s/') == FALSE
		    )
		    {
		        $this->left_nav = $this->_get_left_nav();
		    }
		    
		    $this->success_msg = $this->session->flashdata('success_msg') ? $this->session->flashdata('success_msg') : '';
		    $this->error_msg = $this->session->flashdata('error_msg') ? $this->session->flashdata('error_msg') : '';
    }

    /**
     * Check to see if a login is required for this page. If it is, check to see if user
     * is logged in and load their data if they are.
	   * 
	   * @author unknown, Christophe
     */
    private function _login() 
    {
        $this->load->library('Vision_security');
        $this->load->model('users_m');
        
    		$required_login = NULL;
    		
    		if (isset($this->_acl[$this->_method]))
    		{
    			$required_login = $this->_acl[$this->_method];
    		}
    		else if (isset($this->_acl['*']))
    		{
    			$required_login = $this->_acl['*'];
    		}
    		  
        switch ($required_login) 
        {
    		    case 'user':
    		        
    		        // check if connection needs to be secure (https/ssl)
    		        $this->vision_security->do_ssl_check();
    		        
    		        $user_uuid = $this->session->userdata('user_uuid');
    		        //$user_uuid = '556cd63d-7908-42d4-a672-2d8000000000'; // Chris' test account
    		        
    		        if ($user_uuid != FALSE && $user_uuid != '')
    		        {
    		            $user = $this->users_m->get_user_by_uuid($user_uuid);
    		        }
    		        
    		        //var_dump($user); exit();  
    				
    				    if (empty($user) || $user == FALSE) 
    				    {
    				        $this->session->set_userdata('user_uuid', '');
    				        
                    $this->session->set_flashdata('error_msg', 'Error: Login details entered are not correct.');
                    
    					      redirect('/login');
    					      exit();
    				    }
    				    else 
    				    {
    				        // check to see if user is active
    				        if (intval($user['user_active']) != 1)
    				        {
    				            $this->session->set_userdata('user_uuid', '');
    				            
    				            $this->session->set_flashdata('error_msg', 'Error: Your account is no longer active.');
    				            
    				            redirect('/login');
    				            exit();
    				        }
    				        
    				        if ($user['email'] == '')
    				        {
    				            // Christophe: hack to skip terms check for demo users who do not have email address set
    				        }
    				        else
    				        {               
        				        // check to see if they have accepted the terms
        				        if (intval($user['terms_accepted']) != 1)
        				        {
        				            redirect('/signup/confirm/' . $user['uuid']);
        				        }
    				        }
    				        
    				        // check to see if they have set a password
    				        // hack: but for demo accounts that don't have an email address, skip this
    				        if ($user['password'] == '' && $user['email'] != '')
    				        {
    				            redirect('/signup/password/' . $user['uuid']);
    				        }
    				        			        
    				        $this->_load_user($user);
    				        
                    //now set all of the pertinent store data
                    $this->allVisibleStoreIds = array();
                    $this->_load_stores();
                    
                    //we need to create a store
                    if ( !$this->store_id && $this->_controller !== 'settings' && $this->_controller !== 'logout') redirect('settings');
                    
                    $this->_load_bookmarks();
                    $this->_store_tpl_vars();
                    $this->_store_view_vars();
                    $this->_is_global_admin = $this->_is_global_admin();
                    //I don't think this is necessary here...
                    //$this->check_permissions();
    				    }
    				    
    				    break;
    		    case 'public':    
            case 'cli':
        				//if ( ! $this->input->is_cli_request())
        				//	exit('You do not have permission to access this page.');
        				$this->allVisibleStoreIds = array();
        				break;
        }
    }

	protected function _is_global_admin() 
	{
		//return $this->input->cookie('sticky_admin_global_session') !== FALSE;
		return FALSE;
	}

	/**
	 * Add variables passed to the template
	 *
	 * @param String $key
	 * @param mixed $value
	 */
	protected function _add_tpl_var($key, $value) {
		$this->_tpl_data[$key] = $value;
	}

	/**
	 * Set user specific data used in the template
	 */
	protected function _store_tpl_vars() {
		$this->_tpl_data['user_name'] = $this->session->userdata('user_name');
		$this->_tpl_data['bookmarks'] = $this->_bookmarks;
	}

	/**
	 * Set user specific data used in every view
	 */
	protected function _store_view_vars() {
		if(empty($this->data)) $this->data = new stdClass;
		$this->data->controller = $this->_controller;
		$this->data->method = $this->_method;
		if(!empty($this->allBrands)) {
			$this->data->brands = $this->allBrands;
		}
		$this->data->brandName = getBrandName($this->store_id);
		$this->data->store_id = intval($this->store_id);
		$this->data->team_members = autocomp_get_team_members($this->store_id, false);
		//$this->data->user_type = $this->user_type;//user.user_type value, but doesn't necessarily represent permissions
		$this->data->permission_id = $this->permission_id;//0=owner, 1=report, 2=admin
		$this->data->interact_script_id = $this->get_interact_script_id();
	}
	
	private function get_interact_script_id(){
		$arrIds = $this->config->item('interact_scripts');
		if(isset($arrIds[$this->_controller])){
			return $arrIds[$this->_controller];
		}
		return false;
	}

	/**
	 * Return the template data array along with
	 * non-user specific template data
	 *
	 * @return array
	 */
	public function get_tpl_vars() {
		$this->_tpl_data['account'] = $this->config->item('gsession_base_url');
		$this->_tpl_data['controller'] = $this->_controller;

		return $this->_tpl_data;
	}

	/**
	 * Load the color class and a color manager.
	 * Default colors can be specified in config['report_colors'].
	 * Once the default colors are depleted they are randomly generated.
	 */
	private function _init_color_handler() {
		require_once(APPPATH . 'libraries/color_handler.php');
		$colors = $this->config->item('report_colors') ? $this->config->item('report_colors') : array();
		Color_handler::init($colors);
	}

	/**
	 * Get this users bookmarked pages from the shortcuts table
	 */
	private function _load_bookmarks() {
		$this->_bookmarks = $this->db
		->where('user_id', (int)$this->session->userdata('user_id'))
		->get($this->_table_shortcuts)
		->result();

		return $this->_bookmarks;
	}

	/**
	 * Check if a user has a specific addon
	 *
	 * @param type $addon
	 * @return type
	 */
	function check_addon($addon) {
		$addons = array_lookup(array_to_lower($this->subscriber_addons));

		return isset($addons[strtolower($addon)]);
	}

	/**
	 * Get a list of the users teammates
	 *
	 * @param int $store_id
	 * @return array
	 */
	function getTeamMates($store_id) {
		return $this->db
		->where('store_id', $store_id)
		->get($this->_table_users_store)
		->result();
	}

	//Getting team mates stores
	function getTeamMatesStores($teamMates) {
		$dataArray = array();
		if ($teamMates) {
			for ($i = 0, $n = count($teamMates); $i < $n; $i++) {
				$result = $this->db
				->where('user_id', $teamMates[$i]->user_id)
				->get($this->_table_users_store)
				->row();

				if ($result)
					$dataArray[] = $result->store_id;
			}
		}

		return $dataArray;
	}

	//Checking teams existance
	function checkTeams($user_id, $store_id) {
		$dataTeam = $this->getTeamMates($store_id);
		$dataStores = $this->getTeamMatesStores($dataTeam);
		if ($dataStores) {
			array_push($dataStores, $this->session->userdata('store_id'));
			if (!($this->session->userdata('owner_id'))) {
				if ($this->session->userdata('store_id') != $store_id)
					redirect(base_url());
			}elseif ($this->session->userdata('owner_id')) {
				if ($this->session->userdata('store_id') != $store_id)
					redirect(base_url());
			}
		} elseif ($this->session->userdata('store_id') != $store_id) {
			redirect(base_url());
		}

		return $store_id;
	}

	//Checking permissions
	function check_permissions() {
		$hasPerms = false;

		//allBrands contains owner & team brands
		for ($i = 0, $n = sizeof($this->allBrands); $i < $n; $i++) {
			$curBrand = $this->allBrands[$i];

			if (in_array($this->store_id, (array) $curBrand)) {
				$hasPerms = $this->allBrands[$i]['permissions'];
				$owner = $this->allBrands[$i]['owner_id'];
			}
		}

		if (!$hasPerms) {
			foreach ($this->allBrands as $key => $value) {
				//at least give a store_id so the redirect will work
				if ($value->id != $this->store_id) {
					$this->session->set_userdata('store_id', $value->id);
					redirect(base_url());
				}
			}
		}

		$this->user_type = $hasPerms;

		return $this->user_type;
	}

	//Checking permissions on the base of store
	function checkTeamStorePermissions($user_id, $store_id) {
		$dataMates = $this->getTeamMates($store_id);
		$dataStores = $this->getTeamMatesStores($dataMates);
		if ($dataStores) {
			for ($i = 0; $i < count($dataStores); $i++) {
				if ($store_id == $dataStores[$i] && $dataMates[$i]->rights == 2) {
					return 2;
				} else {
					return 1;
				}
			}
		} else {
			return 0;
		}
	}

	//Fetching reports
	function fetch_report($fileName = '', $type = '', $columns = array(), $items = array(), $reciever = array(), $temp_type = '') {
		$htmlData = '';
		$data['excel'] = $type;
		$data['columns'] = $columns;
		$data['items'] = $items;
		$data['fileName'] = $fileName;
		$testEmail = false;
		if ($type == 'excel') {
			$this->load->view('excelview', $data);
		} elseif ($type == 'pdf') {
			$htmlData = $this->load->view('pdfview_reports', $data, true);
			$this->load->helper('pdf');
			tcpdf_write($htmlData, $fileName);
		} else {
			$htmlData = $this->load->view('pdfview_reports', $data, true);
			for ($i = 0, $n = count($receiver); $i < $n; $i++) {
				if ($reciever[$i]) {
					if (template_email_send($temp_type, $this->session->userdata('user_id'), '', $reciever[$i], $htmlData)) {
						$testEmail = true;
					} else {
						$testEmail = false;
					}
				}
			}
			if ($testEmail) {
				$this->data->html = "Email successfully sent.";
				$this->data->div_class = "success";
				echo json_encode($this->data);
				exit;
			} else {
				$this->data->html = "Unable to send email.";
				$this->data->div_class = "error";
				echo json_encode($this->data);
				exit;
			}
		}
	}

	function fetch_report_new($fileName = '', $type = '', $columns = array(), $items = array(), $reciever = array(), $temp_type = '', $user_id) {
		$htmlData = '';
		$data['excel'] = $type;
		$data['columns'] = $columns;
		$data['items'] = $items;
		$data['fileName'] = $fileName;
		$testEmail = false;
		if ($type == 'excel') {
			$this->load->view('excelview', $data);
		} elseif ($type == 'pdf') {
			$htmlData = $this->load->view('pdfview_reports', $data, true);
			$this->load->helper('pdf');
			tcpdf_write($htmlData, $fileName);
		} else {
			$htmlData = $this->load->view('pdfview_reports', $data, true);
			if (template_email_send($temp_type, $user_id, '', $reciever, $htmlData)) {
				$testEmail = true;
			} else {
				$testEmail = false;
			}
			if ($testEmail) {
				$this->data->html = "Email successfully sent.";
				$this->data->div_class = "success";
				return "success";
			} else {
				$this->data->html = "Unable to send email.";
				$this->data->div_class = "error";
				return "error";
			}
		}
	}

	/**
	 * Store all the tables in the Controller object
	 * so that they may be accessed in the form $this->_table_*table_name*
	 * and $this->_dynamo_*table_name*.
	 */
	private function _load_tables() {
		$this->config->load('db_tables');
		$db_tables = $this->config->item('db_tables');
		$db_tables = $db_tables[$db_tables['environment']];
		$mysql_tables = $db_tables['mysql'];
		$dynamo_tables = $db_tables['dynamo'];

		foreach ($mysql_tables as $name => $table)
			$this->{'_table_' . $name} = $table;

		foreach ($dynamo_tables as $name => $table)
			$this->{'_dynamo_' . $name} = $table;
	}

    /**
     *  load all user information for use on all pages
     *  
     *  @todo move function to a library class
     *  @todo handle terms check here?
     *  @author unknown, Christophe
     */
    private function _load_user($user) 
    {
        if ($user['role_id'] === 0 || $user['role_id'] == '0')
        {
            $user['role_id'] = 2;
        }
        
        $this->user = $user;
        $this->user_id = intval($user['id']);
        $this->logged_in = TRUE;
        $this->role_id = intval($user['role_id']);
        $this->permission_id = $this->role_id;
        
        // set session
        //store.user_id can be viewed by anyone belonging to that particular team
        $session_data = array(
        		'user_id' => $user['id'],
        		'user_name' => $user['user_name'],
        		'user_email' => $user['email'],
        		//'user_type' => $this->api_user_info['user_type'],
            'rid' => $user['role_id'],        
        		'permission_id' => $user['role_id']
        );
        
        $this->session->set_userdata($session_data);
        
        //comment/remove this when the payment/lookup portion above works properly...
        $this->subscriber_addons = array('iherb', 'livamed', 'luckyvitamin', 'swansonvitamins', 'vitacost', 'vitaminshoppe', 'vitanherbs');
        $this->subscriber_retailer_addons = getRetailerArray();
    }

	/**
	 *  load all brand information for use on all pages
	 */
	private function _load_stores() {
	    $this->load->model("store_m", 'Store');
	    
		$store_results = $this->Store->get_results_by_id($this->user_id);

		if (empty($store_results)) return false;

		$allBrands = $allBrandIds = array();

		for ($i=0, $n=sizeof($store_results); $i<$n; $i++) {
			$curArray = array('store_id' => $store_results[$i]->store_id,
				'owner_id' => $store_results[$i]->user_id,
				'store_name' => $store_results[$i]->store_name,
				'store_enable' => $store_results[$i]->store_enable,
				'man_id' => $store_results[$i]->man_id);
			if (!$this->session->userdata('store_id') && $i==0) {
				$this->store_id = $store_results[$i]->store_id;
				$this->session->set_userdata('owner_id', $store_results[$i]->user_id);
				$this->session->set_userdata('store_id', intval($this->store_id));
				$this->session->set_userdata('store_data', $curArray);
			}

			array_push($allBrands, $curArray);
			$allBrandIds[] = $store_results[$i]->store_id;
		}

		$this->allBrands = $allBrands;

		$this->session->set_userdata('brands', base64_encode(json_encode($allBrands)));
		$this->session->set_userdata('allVisibleStoreIds', base64_encode(json_encode($allBrandIds)));

		if ($this->session->userdata('store_id')) $this->store_id = intval($this->session->userdata('store_id'));
		if ($this->session->userdata('store_data')) $this->store_data = $this->session->userdata('store_data');

		if ( ! empty($allBrands))
			foreach ($allBrands as $i => $brand)
				if ($brand['store_enable'])
					$this->allVisibleStoreIds[] = $brand['store_id'];

				if ( !$this->session->userdata('user_brand_default') && !$this->session->userdata('store_id')) {
					$default = $this->store_id;
					if (count($this->allVisibleStoreIds) > 1) {
						$default = 'all';
						$this->_switch_brand($default);
					}
					$this->session->set_userdata('user_brand_default', $default);
				}
	}

    /**
     * This function is called to switch a user to another store (a.k.a. brand) they may
     * have access to. Some companies have more than 1 store (brand).
     * 
     * Store = Brand
     * 
     * Current store ID that user is using is saved in $this->store_id and 
     * can be accessed via this anywhere in app code.
     * 
     * @author unknown, Christophe
     * @param int $store_id
     */
    protected function _switch_brand($store_id) 
    {
        $store_id = intval($store_id);
        
        // check to see if current user is joined to store
        $user_store = $this->Store->get_user_store($this->user_id, $store_id);
        
        if (empty($user_store))
        {
            $this->session->set_flashdata('error_msg', 'Error: You do not have access to this brand/store.');
            
            redirect('/');
            exit();
        }
        
		    $this->session->set_userdata('store_id', $store_id);
        $this->session->set_userdata('st_id', $store_id); // legacy? - Christophe
        
        // legacy code that was used by old user auth system:
        
        /*
		    //re-populate the brands session value
		    $ownerBrands = $allBrands = $storeData = array();
		
		    //get any owner brands
		    $store_results = $this->Store->get_results_by_id($this->user_id);
		
		    for ($i = 0, $n = sizeof($store_results); $i<$n; $i++) 
		    {
			      $curArray = array(
                'store_id' => $store_results[$i]->store_id,
                'user_id' => $store_results[$i]->user_id,
                'store_name' => $store_results[$i]->store_name,
                'store_enable' => $store_results[$i]->store_enable,
                'man_id' => $store_results[$i]->man_id
			      );
			        
			      if ($store_results[$i]->store_id == $this->store_id) {
				        $storeData = $curArray;
			      }

			      array_push($ownerBrands, $curArray);
			      array_push($allBrands, $curArray);
        }

		    // get any team brands
		    for ($i = 0, $n = sizeof($this->api_user_info['user_teams']); $i < $n; $i++) 
		    {
			      $store_results = $this->db
			          ->join($this->_table_users.' u', 's.user_id=u.id', 'left')
                ->where('u.global_user_id', $this->api_user_info['user_teams'][$i]['team_id'])
                ->where('s.store_enable', '1')
                ->select('s.*')
                ->get('store s')
                ->result();

            $teamBrands = array();
            
            for ($x=0, $y=sizeof($store_results); $x<$y; $x++) 
            {
                $curArray = array(
                	'store_id' => $store_results[$x]->store_id,
                	'user_id' => $store_results[$x]->user_id,
                	'store_name' => $store_results[$x]->store_name,
                	'store_enable' => $store_results[$x]->store_enable,
                	'man_id' => $store_results[$x]->man_id,
                	'permissions' => $this->api_user_info['user_teams'][$i]['permission_id']
                );
                
                array_push($teamBrands, $curArray);
                array_push($allBrands, $curArray);
            }
        }
    
    		$this->session->set_userdata('store_data', $storeData);
    		$this->session->set_userdata('brands', base64_encode(json_encode($allBrands)));
    		
        */
    }

	/**
	 * Return the name of the layout to put the view content in
	 *
	 * @return String { default : 'default' }
	 */
	public function _get_layout() {
		return $this->_layout;
	}

	/**
	 * Return the name of the view to load
	 *
	 * @return String { default : 'controller/method' }
	 */
	public function _get_view() {
		return $this->_view;
	}

	/**
	 * Add a javascript source to load at the end of the page body
	 *
	 * @param String $src
	 */
	public function javascript($src) {
		$this->_javascript_files[$src] = $this->_javascript_dir . $src;
	}

	/**
	 * Return the list of javascript sources to load at the
	 * end of the page body
	 *
	 * @return array
	 */
	public function _get_javascript_files() {
		return array_values($this->_javascript_files);
	}

	/**
	 * Set the response type to either HTML or JSON
	 *
	 * @param String $type
	 * @throws Exception
	 */
	public function _response_type($type) {
		$type = strtolower($type);
		switch ($type) {
		case 'html':
		case 'json':
			break;
		default:
			throw new Exception('Invalid response type ' . $type .'. HTML or JSON expected');
		}

		$this->_response_type = $type;
	}

	/**
	 * Get the response type
	 *
	 * @return String { default : 'html' }
	 */
	public function _get_response_type() {
		return $this->_response_type;
	}

	/**
	 * This is called right before the view content
	 * is placed in the template and output to the browser
	 */
	public function _pre_display() {
		if ($this->_response_type === 'html') {

		}
		elseif ($this->_response_type === 'json') {
			if ( ! $this->input->is_ajax_request())
				redirect($this->_controller);
			ajax_return($this->data);
		}
		else {
			show_error('Invalid response type');
		}
	}
	
    public function _get_left_nav()
    {   
        $left_nav = array(
        		'dashboard' => array(
        				'title' => 'Dashboard',
        				'url' => '/overview',
        				'icon' => 'fa-dashboard'
        		),
        		'product_catalog' => array(
        				'title' => 'Product Catalog',
        				'icon' => 'fa-tags',
        				'sub' => array(
        						'product_list' => array(
        								'title' => 'Product List',
        								'url' => '/catalog'
        						),       				                       				                
        				)
        		),
        		'market_visibility' => array(
        				'title' => 'Market Visibility',
        				'icon' => 'fa-bullseye',
        				'sub' => array(
        						'pricing_over_time' => array(
        								'title' => 'Pricing Over Time',
        								'sub' => array(
        										'by_product' => array(
        												'title' => 'By Product',
        												'url' => '/reports'
        										),
        										'by_merchant' => array(
        												'title' => 'By Merchant',
        												'url' => '/reports/bymerchant'
        										)       								                      								                           
        								)
        						),
        						'who_selling_now' => array(
        								'title' => 'Who\'s Selling Now',
        								'sub' => array(
    				                'whois_marketplaces' => array(
    				                		'title' => 'Marketplaces',
    				                		'url' => '/whois'
    				                ),
    				                'whois_retailers' => array(
    				                		'title' => 'Retailers',
    				                		'url' => '/whois/retailers'
    				                ),        								                
        								)
        						)
        				)
        		),
            'map_enforcement' => array(
            		'title' => 'MAP Enforcement',
            		'icon' => 'fa-check-square-o',
            		'sub' => array()
            ),
            'schedule_reports' => array(
            		'title' => 'Schedule Reports',
            		'url' => '/schedule',
            		'icon' => 'fa-clock-o'
            ), 
            'saved_reports' => array(
            		'title' => 'Saved Reports',
            		'url' => '/savedreports',
            		'icon' => 'fa-star'
            ),   
            'account_information' => array(
            		'title' => 'Account Information',
            		'icon' => 'fa-user',
            		'sub' => array(
            				'my_profile' => array(
            						'title' => 'My Profile',
            						'url' => '/account/profile'
            				),
            				'team_members' => array(
            						'title' => 'Team Members',
            						'url' => '/account/team'
            				),
            				'change_password' => array(
            						'title' => 'Change Password',
            						'url' => '/account/change_password'
            				),
            		)
            ),                                                                                            
        );
        
        // add admin user items to the menu
        if ($this->role_id == 2)
        {
            $left_nav['map_enforcement']['sub']['settings'] = array(
    		        'title' => 'MAP Settings',
    		        'sub' => array(
                    'enforcement_settings' => array(
                    		'title' => 'Enforcement Settings',
                    		'url' => '/enforcement/settings'
                    ),
                    'enforcement_emails' => array(
                    		'title' => 'Enforcement Emails',
                    		'url' => '/enforcement/templates'
                    ),
                    'marketplace_logins' => array(
                    		'title' => 'Marketplace Logins',
                    		'url' => '/enforcement/amazone_violator'
                    )          		                                 		                        
    		        )                            
    		    );
            
            $left_nav['product_catalog']['sub']['add_products'] = array(
		                    'title' => 'Add Products',
		                    'url' => '/settings/products'        				                    
        	  );
            
            $left_nav['product_catalog']['sub']['promotional_pricing'] = array(
            		'title' => 'Promotional Pricing',
            		'url' => '/catalog/promotional_pricing'
            );
            
            $left_nav['product_catalog']['sub']['add_brand'] = array(
            		'title' => 'Add Brand',
            		'url' => '/settings/add_store'
            );
            
            $left_nav['product_catalog']['sub']['edit_brand'] = array(
            		'title' => 'Edit Brand',
            		'url' => '/settings/edit_store'
            );
            
            $left_nav['market_visibility']['sub']['merchant_info'] = array(
            		'title' => 'Merchant Info',
                'sub' => array(
                		'view_merchants' => array(
                				'title' => 'View Merchants',
                				'url' => '/merchants'
                		),
                		'merchants_export' => array(
                				'title' => 'Export Data',
                				'url' => '/merchants/export'
                		),
                    'merchants_import' => array(
                    		'title' => 'Import Data',
                    		'url' => '/merchants/import'
                    ),                                
                ),
		        );
            
            
        }
        
        $left_nav['map_enforcement']['sub']['violation_dashboard'] = array(
        		'title' => 'Violation Dashboard',
        		'url' => '/violationoverview'
        );
        
        $left_nav['map_enforcement']['sub']['price_violations'] = array(
        		'title' => 'Violation Reports',
        		'sub' => array(
        				'price_violators' => array(
        						'title' => 'Price Violators',
        						'url' => '/violationoverview/price_violators'
        				),
        				'violated_products' => array(
        						'title' => 'Violated Products',
        						'url' => '/violationoverview/violated_products'
        				),
        				'violations_by_marketplace' => array(
        						'title' => 'Marketplace Violations',
        						'url' => '/violationoverview/violations_by_marketplace'
        				),
        				'retailer_violations' => array(
        						'title' => 'Retailer Violations',
        						'sub' => array(
        								'all_retailer_violations' => array(
        										'title' => 'All Retailer Violations',
        										'url' => '/violationoverview/all_retailer_violations'
        								),
        								'violations_by_retailer' => array(
        										'title' => 'Violations by Retailer',
        										'url' => '/violationoverview/violations_by_retailers'
        								)
        						)
        				),
        				'violation_notices' => array(
        						'title' => 'Violation Notices',
        						'url' => '/violationoverview/sent_notices'
        				)
        		)
        );
        
        $left_nav['map_enforcement']['sub']['do_not_sell'] = array(
        		'title' => 'Do Not Sell',
        		'sub' => array(
        				'do_not_sell_list' => array(
        						'title' => 'Do Not Sell List',
        						'url' => '/enforcement/do_not_sell'
        				)
        		)
        );
        
        // set selected item
        $uri = $this->uri->uri_string();
        
        if ($uri == '/overview' || $uri == '')
        {
            $left_nav['dashboard']['active'] = TRUE;
        }
        elseif ($uri == '/catalog' || strstr($uri, '/catalog/edit_product') != FALSE)
        {
            $left_nav['product_catalog']['sub']['product_list']['active'] = TRUE;
        }
        elseif ($uri == '/settings/products')
        {
            $left_nav['product_catalog']['sub']['add_products']['active'] = TRUE;
        }        
        elseif ($uri == '/catalog/promotional_pricing')
        {
            $left_nav['product_catalog']['sub']['promotional_pricing']['active'] = TRUE;
        }        
        elseif ($uri == '/settings/add_store')
        {
            $left_nav['product_catalog']['sub']['add_brand']['active'] = TRUE;
        }
        elseif ($uri == '/settings/edit_store')
        {
            $left_nav['product_catalog']['sub']['edit_brand']['active'] = TRUE;
        }
        elseif ($uri == '/reports' || $uri == '/reports/show')
        {
            $left_nav['market_visibility']['sub']['pricing_over_time']['sub']['by_product']['active'] = TRUE;
        }
        elseif ($uri == '/reports/bymerchant')
        {
            $left_nav['market_visibility']['sub']['pricing_over_time']['sub']['by_merchant']['active'] = TRUE;
        }
        elseif ($uri == '/reports/bymarket')
        {
            $left_nav['market_visibility']['sub']['pricing_over_time']['sub']['by_market']['active'] = TRUE;
        }
        elseif ($uri == '/reports/bygroup')
        {
            $left_nav['market_visibility']['sub']['pricing_over_time']['sub']['by_group']['active'] = TRUE;
        }
        elseif ($uri == '/reports/bycompetition')
        {
            $left_nav['market_visibility']['sub']['pricing_over_time']['sub']['by_competition']['active'] = TRUE;
        }
        elseif ($uri == '/whois' || $uri == '/whois/index' || strstr($uri, '/whois/report_marketplace'))
        {
            $left_nav['market_visibility']['sub']['who_selling_now']['sub']['whois_marketplaces']['active'] = TRUE;
        } 
        elseif (strstr($uri, '/whois/report_merchant') || $uri == '/whois/retailers')
        {
            $left_nav['market_visibility']['sub']['who_selling_now']['sub']['whois_retailers']['active'] = TRUE;
        }
        elseif ($uri == '/violationoverview')
        {
            $left_nav['map_enforcement']['sub']['violation_dashboard']['active'] = TRUE;
        }        
        elseif ($uri == '/violationoverview/price_violators')
        {
            $left_nav['map_enforcement']['sub']['price_violations']['sub']['price_violators']['active'] = TRUE;
        }  
        elseif ($uri == '/violationoverview/violated_products' || strstr($uri, '/violationoverview/violated_product') || strstr($uri, '/violationoverview/violator_report'))
        {
            $left_nav['map_enforcement']['sub']['price_violations']['sub']['violated_products']['active'] = TRUE;
        }               
        elseif ($uri == '/violationoverview/violations_by_marketplace' || strstr($uri, '/violationoverview/report_marketplace'))
        {
            $left_nav['map_enforcement']['sub']['price_violations']['sub']['violations_by_marketplace']['active'] = TRUE;
        }        
        elseif ($uri == '/violationoverview/violations_by_retailers' || strstr($uri, '/violationoverview/violations_by_retailer'))
        {
            $left_nav['map_enforcement']['sub']['price_violations']['sub']['retailer_violations']['sub']['violations_by_retailer']['active'] = TRUE;
        }   
        elseif ($uri == '/violationoverview/sent_notices')
        {
        	$left_nav['map_enforcement']['sub']['price_violations']['sub']['violation_notices']['active'] = TRUE;
        }        
        elseif ($uri == '/violationoverview/all_retailer_violations')
        {
            $left_nav['map_enforcement']['sub']['price_violations']['sub']['retailer_violations']['sub']['all_retailer_violations']['active'] = TRUE;
        }   
        elseif ($uri == '/enforcement/do_not_sell')
        {
            $left_nav['map_enforcement']['sub']['do_not_sell']['sub']['do_not_sell_list']['active'] = TRUE;
        }  
        /*
        elseif ($uri == '/enforcement/do_not_sell_settings')
        {
            $left_nav['map_enforcement']['sub']['do_not_sell']['sub']['do_not_sell_settings']['active'] = TRUE;
        } 
        */       
        elseif ($uri == '/enforcement/settings')
        {
            $left_nav['map_enforcement']['sub']['settings']['sub']['enforcement_settings']['active'] = TRUE;
        }
        elseif (
            $uri == '/enforcement/merchant' || 
            $uri == '/merchants' || 
            strstr($uri, '/merchants/profile') ||
            strstr($uri, '/merchants/profile_products') || 
            strstr($uri, '/merchants/profile_violations')
        )
        {
            $left_nav['market_visibility']['sub']['merchant_info']['sub']['view_merchants']['active'] = TRUE;
        }
        elseif ($uri == '/merchants/export')
        {
            $left_nav['market_visibility']['sub']['merchant_info']['sub']['merchants_export']['active'] = TRUE;
        }
        elseif ($uri == '/merchants/import' || $uri == '/merchants/import_merchant_data' || $uri == '/merchants/import_merchant_contact_data')
        {
            $left_nav['market_visibility']['sub']['merchant_info']['sub']['merchants_import']['active'] = TRUE;
        }    
        elseif ($uri == '/enforcement/templates' || strstr($uri, 'enforcement/template') != FALSE)
        {
            $left_nav['map_enforcement']['sub']['settings']['sub']['enforcement_emails']['active'] = TRUE;
        }
        elseif ($uri == '/enforcement/amazone_violator')
        {
            $left_nav['map_enforcement']['sub']['settings']['sub']['marketplace_logins']['active'] = TRUE;
        }
        elseif ($uri == '/enforcement/violator_settings')
        {
            $left_nav['map_enforcement']['sub']['settings']['sub']['violator_settings']['active'] = TRUE;
        }
        elseif ($uri == '/schedule')
        {
            $left_nav['schedule_reports']['active'] = TRUE;
        }
        elseif ($uri == '/savedreports')
        {
            $left_nav['saved_reports']['active'] = TRUE;
        }
        elseif ($uri == '/account/profile' || $uri == '/account/edit_profile')
        {
            $left_nav['account_information']['sub']['my_profile']['active'] = TRUE;
        }
        elseif ($uri == '/account/team' || $uri == '/account/team_add' || strstr($uri, '/account/team') != FALSE)
        {
            $left_nav['account_information']['sub']['team_members']['active'] = TRUE;
        }
        elseif ($uri == '/account/change_password')
        {
            $left_nav['account_information']['sub']['change_password']['active'] = TRUE;
        }
        
        return $left_nav;
    }

}

/* End of file MY_Controller.php */
/* Location: ./system/application/libraries/MY_Controller.php */