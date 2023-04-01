<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends MY_Controller
{
    public function Ajax() 
    {
    		parent::__construct();
    		
    		$this->load->model("store_m", 'Store');
    		$this->load->model('Users_m', "User");
    		$this->load->model("report_m", 'Report');
    		$this->load->model('account_m', "account");
    		$this->load->model("products_m");
    
    		$this->load->library('validation');
    }

    public function autoUser($email) 
    {
        $this->_response_type('json');
        
        $this->data = array();
        
        if (trim($email))
        {
            $this->data = $this->User->getMerchantsByEmail($email);
        }
    }

    public function autocomplete($store_id, $query) 
    {
		    $this->_response_type('json');
		    
		    $this->data = $this->products_m->getMultiSelectProduct($store_id, $query);
    }

    public function getProductsListbox($store_id) 
    {
		    $this->_response_type('json');
		    
        $this->data = $this->products_m->getlqxListProducts($store_id);
    }
    
    /**
     * Check to see if user has a saved state for a table.
     *
     * @author Christophe
     */
    public function table_state_load($table_name)
    {
        $this->load->model('users_m');
        
        $dt_state_save = $this->users_m->get_datatables_state_save($this->user_id, $this->store_id, $table_name);
         
        if (empty($dt_state_save))
        {
            echo json_encode(array());
        }
        else
        {
            echo $dt_state_save['state_json'];
        }
         
        exit();
    }  

    /**
     * Save the state for the DataTables plugin.
     *
     * @author Christophe
     */
    public function table_state_save($table_name)
    {
        $this->load->model('users_m');
         
        //var_dump(json_encode($_POST)); exit();
         
        $state_json = json_encode($_POST, JSON_NUMERIC_CHECK);
         
        $dt_state_save = $this->users_m->get_datatables_state_save($this->user_id, $this->store_id, $table_name);
         
        if (empty($dt_state_save))
        {
            $insert_data = array(
            		'user_id' => $this->user_id,
            		'table_name' => $table_name,
            		'state_json' => $state_json,
            		'created' => date('Y-m-d H:i:s', time()),
            		'modified' => date('Y-m-d H:i:s', time())
            );
            
            $this->users_m->insert_datatables_state_save($insert_data);
        }
        else
        {
            $update_data = array(
            		'state_json' => $state_json,
            		'modified' => date('Y-m-d H:i:s', time())
            );
             
            $this->users_m->update_datatables_state_save($dt_state_save['id'], $update_data);
        }
         
        echo 'true';
        exit();
    }    
}