<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Terms_m extends MY_Model
{
    /**
     * Get terms row for a specific user type.
     * 
     * @author Christophe
     * @param int $user_type_id
     * @return array
     */
    public function get_terms_by_user_type($user_type_id)
    {
        $user_type_id = intval($user_type_id);
        
        $user_type_id = $user_type_id == 2 ? 0 : $user_type_id;
        
        $this->db->select('AT.*');
        $this->db->from('application_terms AT');
        $this->db->where('AT.terms_type', $user_type_id);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }
}

?>