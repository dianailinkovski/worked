<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Super_user extends Controller
{
    public $layout = null;
    
    public function Super_user()
    {
        parent::__construct();
        
        $this->_load_tables();
    }
    
    public function index()
    {
        echo 'Nothing here';
    }
    
    /**
     * Check to see if admin UUID cookie is set and display value.
     * 
     * @author Christophe
     */
    public function check_super_admin_cookie()
    {
        $this->load->helper('cookie');
        
        $admin_uuid = $this->input->cookie('admin_uuid', TRUE);
        
        var_dump($admin_uuid); exit();
    }
    
    public function check_timezone()
    {
        if (date_default_timezone_get()) {
            echo 'date_default_timezone_set: ' . date_default_timezone_get();
        }
        
        exit();
    }
    
    /**
     * Allow Sticky super admins to log in as a specific user. Record both bad and good log in attempts.
     * 
     * @author Christophe
     * @param int $user_id
     */
    public function login_as($user_id)
    {
        $this->load->library('Vision_users');
        $this->load->model('users_m');
        $this->load->model('super_m');
        $this->load->helper('cookie');
        
        //var_dump($_SERVER); exit();
        
        $user_id = intval($user_id);
        
        $admin_uuid = $this->input->cookie('admin_uuid', TRUE);
        
        $ip_address = $this->vision_users->get_ip_address();
        
        $http_cookie = isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : '';
        $http_referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        // allow for local log in
        if ($this->config->item('environment') == 'local')
        {
            $user = $this->users_m->get_user_by_id($user_id);
            
            // log in as user
            $this->session->set_userdata('user_uuid', $user['uuid']);
            
            redirect('/');
        }
        
        if ($admin_uuid == FALSE)
        {
            $insert_data = array(
                'super_admin_uuid' => '',   
                'store_user_id' => $user_id,
                'ip_address' => $ip_address,
                'http_cookie' => $http_cookie,
                'http_referrer' => $http_referrer,             
                'http_user_agent' => $http_user_agent,
                'login_attempt' => 'bad',
                'bad_login_reason' => 'Did not have super admin UUID cookie.',
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')                                                                       
            );
            
            $this->super_m->insert_super_admin_store_user_login($insert_data);
            
            echo 'Sorry, you do not have access to this. If you have a TrackStreet super admin account, please log in to your account and try again.';
            exit();
        }
        else
        {
            // check to see if super admin is active
            $super_admin = $this->super_m->get_super_admin_by_uuid($admin_uuid, 1);
            
            if (empty($super_admin))
            {
                $insert_data = array(
                		'super_admin_uuid' => $admin_uuid,
                		'store_user_id' => $user_id,
                		'ip_address' => $ip_address,
                    'http_cookie' => $http_cookie,
                    'http_referrer' => $http_referrer,             
                    'http_user_agent' => $http_user_agent,
                		'login_attempt' => 'bad',
                		'bad_login_reason' => 'Super admin account not found for UUID: ' . $admin_uuid,
                		'created' => date('Y-m-d H:i:s'),
                		'modified' => date('Y-m-d H:i:s')
                );
                
                $this->super_m->insert_super_admin_store_user_login($insert_data);
                
                echo 'Sorry, you do not have access to this.';
                exit();
            }
            else
            {
                $user = $this->users_m->get_user_by_id($user_id);
                
                $this->session->unset_userdata('store_id');
                
                if (empty($user))
                {
                    $insert_data = array(
                    		'super_admin_uuid' => $admin_uuid,
                    		'store_user_id' => $user_id,
                    		'ip_address' => $ip_address,
                        'http_cookie' => $http_cookie,
                        'http_referrer' => $http_referrer,             
                        'http_user_agent' => $http_user_agent,
                    		'login_attempt' => 'bad',
                    		'bad_login_reason' => 'Store user not foung for user ID: ' . $user_id,
                    		'created' => date('Y-m-d H:i:s'),
                    		'modified' => date('Y-m-d H:i:s')
                    );
                    
                    $this->super_m->insert_super_admin_store_user_login($insert_data);
                    
                    echo 'Error: User not found.';
                    exit();
                }
                else
                {
                    $insert_data = array(
                    		'super_admin_uuid' => $admin_uuid,
                    		'store_user_id' => $user_id,
                    		'ip_address' => $ip_address,
                        'http_cookie' => $http_cookie,
                        'http_referrer' => $http_referrer,             
                        'http_user_agent' => $http_user_agent,
                    		'login_attempt' => 'good',
                    		'bad_login_reason' => '',
                    		'created' => date('Y-m-d H:i:s'),
                    		'modified' => date('Y-m-d H:i:s')
                    );
                    
                    $this->super_m->insert_super_admin_store_user_login($insert_data);
                    
                    // log in as user
                    $this->session->set_userdata('user_uuid', $user['uuid']);
                    
                    redirect('/');
                }  
            }          
        }
    }
    
    /**
     * Login as Andrew and then go to specified services section.
     * 
     * @author Christophe
     * @param string $service
     */
    public function login_go_to_service($service = 'categories')
    {
        $this->load->model('users_m');
        
        //$user = $this->users_m->get_user_by_email('andrew@juststicky.com');
        $user = $this->users_m->get_user_by_email('christophe+august@trackstreet.com');
        
        $this->session->unset_userdata('store_id');
        
        if (empty($user))
        {
            echo 'Error: User not found.';
            exit();
        }
        else
        {
            $this->session->set_userdata('user_uuid', $user['uuid']);
            
            switch ($service)
            {
                case 'categories':
                    $redirect_uri = '/category/show';
                    break;
                case 'crawl_status':
                    $redirect_uri = '/srvchk/status.php';
                    break;
                case 'whack_prices':
                    $redirect_uri = '/whackprices';
                    break;
                case 'crawlers':
                    $redirect_uri = '/crawlers';
                    break;
                default: 
                    $redirect_uri = '/';
            }
            
            redirect($redirect_uri);
        }
    }
    
    public function _pre_display()
    {
        
    }
    
    public function _get_layout()
    {
    
    }
    
    public function _get_view()
    {
    
    }
    
    /**
     * Store all the tables in the Controller object
     * so that they may be accessed in the form $this->_table_*table_name*
     * and $this->_dynamo_*table_name*.
     */
    private function _load_tables()
    {
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
}

?>