<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class App_terms_m extends MY_Model 
{
    public function insert_app_term($insert_data)
    {        
        $this->db->insert('application_terms', $insert_data);
        
        return $this->db->insert_id();        
    }
}