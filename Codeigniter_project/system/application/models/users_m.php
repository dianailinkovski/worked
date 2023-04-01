<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users_m extends MY_Model 
{
    public function get_all_users()
    {
        $this->db->select('*');
        $this->db->from('users');
        
        $query = $this->db->get();
         
        return $query->result_array();
    }
    
	function get_all() {
		$this->db->select('*');

		return $this->db->get($this->_table_users)->result();
	}

	function getMerchantDataById($id) {
		$this->db->select('*');
		$this->db->where('id', $id);
		return $this->db->get($this->_table_users)->result('array');
	}

	function get_global_user($globalId){
		$this->db->select('*');
		$this->db->where('global_user_id', $globalId);
		$result = $this->db->get($this->_table_users)->result('array');

		return (count($result) == 1) ? $result[0]: false;
	}

	function check_old_password($password, $id) {
		$data = array('password' => $password, 'id' => $id);
		if ($this->count_by($data) > 0)
			return true;
		else
			return false;
	}

	function get_one($id) {
		$this->db->select('*');

		if (isset($id)) {
			$this->db->where('id', $id);
		}

		return $this->db->get($this->_table_users)->result();
	}

	function get_many_by($params = array()) {

		if (!empty($params['username'])) {
			$this->db->where('user.user_name', $params['username']);
		}

		if (!empty($params['email'])) {
			$this->db->where('email', $params['email']);
		}

		// Limit the results based on 1 number or 2 (2nd is offset)
		if (isset($params['limit']) && is_array($params['limit']))
			$this->db->limit($params['limit'][0], $params['limit'][1]);
		elseif (isset($params['limit']))
			$this->db->limit($params['limit']);

		return $this->get_all();
	}

	function count_all() {
		return $this->db->count_all_results($this->_table_users);
	}

	function insert($input = array()) {
		$input['signup_date'] = date('Y-m-d H:i:s');
		return parent::insert($input);
	}

	function update($id, $input) {
		return parent::update($id, $input);
	}

	function add_team_member($store_id, $user_id){
		return $this->db->insert($this->_table_users_store, array('store_id' => $store_id, 'user_id' => $user_id));
	}

	/**
	 * Get recent user
	 *
	 * @access public
	 * @param int $limit The amount of user to get
	 * @return array
	 */
	public function get_recent($limit = 10) {
		$this->db->order_by('created', 'desc');

		if ($limit > 0) {
			$this->db->limit($limit);
		}

		return $this->get_all();
	}

	function check_email_exist($email) {
		if ($this->count_by('email', $email) > 0)
			return true;
		else
			return false;
	}

	function get_violation_info($merchant_id, $section) {
		return $this->db->get_where('notifications_setting', array('merchant_id' => $merchant_id, 'section' => $section))->result();
	}

	function get_summaries_info($merchant_id, $section) {
		return $this->db->get_where('notifications_setting', array('merchant_id' => $merchant_id, 'section' => $section))->result();
	}

	function get_merchants_names() {
		return $this->db->query("SELECT merchant_name FROM {$this->_table_users} WHERE user_active = 1")->result('array');
	}

	function get_team_members($store_id, $user_role_id = FALSE) 
	{   
	    $store_id = intval($store_id);
	    $user_role_id = intval($user_role_id);
	    
	    $this->db->select('u.*');
	    $this->db->from('users u');
	    $this->db->join('users_store us', 'us.user_id = u.id');    
	    $this->db->where('u.user_active', 1);
	    $this->db->where('us.store_id', $store_id);
	    
	    if ($user_role_id != FALSE)
	    {
	        $this->db->where('u.role_id', $user_role_id);
	    }
	    
	    $query = $this->db->get();
	    
	    return $query->result_array();
	    
	    // old:
	    //return $this->db->query("SELECT m.* FROM {$this->_table_users} u left join users_store us WHERE u.user_active = '1' AND u.id = us.user_id AND us.store_id = '".$store_id."'")->result('array');
	}

	function validateEmail($merchantID, $email) {
		$rs = $this->db->query("SELECT * from {$this->_table_users} WHERE id !='".$merchantID."' AND email = '$email' ")->result('array');
		if (count($rs) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * function getMerchantsByEmail
	 *
	 * @param <string>   $email
	 *
	 */
	function getMerchantsByEmail($email) {
		$this->db->select('email as title, id, user_name');

		$this->db->like('email', $email, 'after');

		return $this->db->get($this->_table_users)->result();
	}

	/**
	 *
	 * function getUserByEmail
	 *
	 *
	 */
	function getUserByEmail($email) {
		$this->db->select('id')->where('email', $email);
		return $this->db->get($this->_table_users)->result('array');
	}

	/**
	 *
	 * function updateMerchangeInfo
	 *
	 *
	 */
	function updateMerchangeInfoById($data, $id) {
		return $this->db->update($this->_table_users, $data, array('id' => $id));
	}

	public function get_shortcuts($user_id) {
		$res = $this->db
		->where('user_id', (int) $user_id)
		->get($this->_table_shortcuts)
		->result_array();

		return $res;
	}
	
    /**
     * Get DataTables state_save.
     * 
     * @author Christophe
     * @param int $user_id
     * @param int $store_id
     * @param string $table_name
     * @return array
     */
    public function get_datatables_state_save($user_id, $store_id, $table_name)
    {
        $this->db->select('*');
        $this->db->from('datatables_state_save');
        $this->db->where('user_id', $user_id);
        $this->db->where('table_name', $table_name);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }	
	
    /**
     * Find all user rows in the table which have this email address.
     * 
     * @author Christophe
     * @param string $email
     * @return array
     */
    public function get_user_by_email($email)
    {
        $this->db->select('U.*');
        $this->db->from('users U');	
        $this->db->where('U.email', $email);
        
        $query = $this->db->get();
        
        return $query->row_array();			
    }

    /**
     * Find all user rows in the table which have this email address.
     *
     * @author Christophe
     * @param string $email
     * @return array
     */
    public function get_user_by_id($id)
    {
    	$this->db->select('U.*');
    	$this->db->from('users U');
    	$this->db->where('U.id', $id);
    
    	$query = $this->db->get();
    
    	return $query->row_array();
    }    
    
    /**
     * Find user row by their UUID.
     * 
     * @param string $uuid
     * @return array
     */
    public function get_user_by_uuid($uuid)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('uuid', $uuid);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }
	
    /**
     * Fetch all user records from Accounts db user table.
     * 
     * @author Christophe
     */
    public function get_users()
    {
        $this->db->select('U.*');
        $this->db->from('users U');
        
        $query = $this->db->get();
        
        return $query->result_array();	    
    }
    
    /**
     * Get a users_store record.
     * 
     * @author Christophe
     * @param int $user_id
     * @param int $store_id
     * @return array
     */
    public function get_user_store_record($user_id, $store_id)
    {
        $user_id = intval($user_id);
        $store_id = intval($store_id);
        
        $this->db->select('US.*');
        $this->db->from('users_store US');
        $this->db->where('US.user_id', $user_id);
        $this->db->where('US.store_id', $store_id);
        
        $query = $this->db->get();
        
        return $query->result_array();        
    }
    
    /**
     * Insert a new record into the users table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_user($insert_data)
    {
        $this->db->insert('users', $insert_data);
        
        return $this->db->insert_id();
    }
    
    /**
     * Insert a new record into user_login_records table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_user_login_record($insert_data)
    {
        $this->db->insert('user_login_records', $insert_data);
        
        return $this->db->insert_id();        
    }
    
    /**
     * Insert a new record into the users_store table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_user_store($insert_data)
    {
    	$this->db->insert('users_store', $insert_data);
    
    	return $this->db->insert_id();
    }    
    
    /**
     * Insert a new row into datatables_state_save.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_datatables_state_save($insert_data)
    {
        $this->db->insert('datatables_state_save', $insert_data);
        
        return $this->db->insert_id();        
    }
    
    /**
     * Insert a new row into data_changes table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_data_change($insert_data)
    {
        $this->db->insert('data_changes', $insert_data);
        
        return $this->db->insert_id();        
    }
    
    /**
     * Update row in datatables_state_save.
     * 
     * @author Christophe
     * @param int $dt_state_save_id
     * @param array $update_data
     */
    public function update_datatables_state_save($dt_state_save_id, $update_data)
    {
        $dt_state_save_id = intval($dt_state_save_id);
        
        $this->db->where('id', $dt_state_save_id);
        $this->db->update('datatables_state_save', $update_data);
    }
    
    /**
     * Update a single user row.
     * 
     * @author Christophe
     * @param int $user_id
     * @param array $update_data
     */
    public function update_user($user_id, $update_data)
    {        
        $user_id = intval($user_id);
        
        $this->db->where('id', $user_id);
        $this->db->update('users', $update_data);        
    }

}
