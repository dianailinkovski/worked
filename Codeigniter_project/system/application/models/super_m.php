<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Super_m extends MY_Model 
{
    /**
     * Get a super admin row by its UUID.
     *
     * @author Christophe
     * @param string $uuid
     * @param int $active
     * @return array
     */
    public function get_super_admin_by_uuid($uuid, $active = 1)
    {
        $this->db->select('*');
        $this->db->from('admin_users');
        $this->db->where('uuid', $uuid);
        $this->db->where('active', $active);

        $query = $this->db->get();

        return $query->row_array();
    }
    
    /**
     * Insert new row into super_admin_store_user_logins table.
     *
     * @author Christophe
     * @param array $insert_data
     */
    public function insert_super_admin_store_user_login($insert_data)
    {
        $this->db->insert('super_admin_store_user_logins', $insert_data);
        
        return $this->db->insert_id();
    }  
    
}

?>