<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Trackstreet_marketplaces
{    
    public function Trackstreet_marketplaces() 
    {
        $this->CI =& get_instance();
    }
    
    /**
     * Determine which marketplaces have sellers selling items for a store (TrackStreet customer).
     * 
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_active_marketplaces_for_store($store_id)
    {
        $this->CI->load->model('marketplace_m');
        
        $marketplaces = $this->CI->marketplace_m->get_all_active_marketplaces();
        
        $final_marketplace_array = array();
        
        foreach ($marketplaces as $marketplace)
        {
            $marketplace_listings = $this->CI->marketplace_m->marketplace_listings_for_store($store_id, $marketplace['marketplace']);
            
            if (empty($marketplace_listings) || count($marketplace_listings) == 0)
            {
                // don't add marketplaces that do not have any sellers selling customer's products  
            }
            else
            {
                $final_marketplace_array[] = $marketplace['marketplace'];
            }
        }
        
        return $final_marketplace_array;
    }
    
    /**
     * Get a saved marketplace setting for a store, or use a default value if set.
     * 
     * @author Christophe
     * @param string $marketplace_name
     * @param int $store_id
     * @param string $setting_name
     * @param mixed $default_value
     */
    public function get_marketplace_setting_value($marketplace_name, $store_id, $setting_name, $default_value = NULL)
    {
        $this->CI->load->model('marketplace_m');
        
        $marketplace_setting = $this->CI->marketplace_m->get_marketplace_store_setting($marketplace_name, $store_id, $setting_name);
        
        if (empty($marketplace_setting))
        {
            return $default_value;
        }
        else
        {
            return $marketplace_setting['value'];
        }
    }
    
    /**
     * Update existing or insert a new marketplace store setting row.
     *
     * @author Christophe
     * @param string $marketplace_name
     * @param int $store_id
     * @param string $setting_name
     * @param string $setting_value
     */
    public function save_marketplace_setting($marketplace_name, $store_id, $user_id, $setting_name, $setting_value)
    {
        $this->CI->load->model('marketplace_m');
        
        $store_id = intval($store_id);
        $user_id = intval($user_id);
        
        $existing_row = $this->CI->marketplace_m->get_marketplace_store_setting($marketplace_name, $store_id, $setting_name);
        
        if (!empty($existing_row))
        {
            $update_data = array(
            		'value' => $setting_value,
            		'modified' => date('Y-m-d H:i:s'),
            		'modified_by' => $user_id
            );
            
            $this->CI->marketplace_m->update_marketplace_store_setting($existing_row['id'], $update_data);
        }
        else
        {
            $insert_data = array(
            		'store_id' => $store_id,
            		'marketplace' => $marketplace_name,
            		'name' => $setting_name,
            		'value' => $setting_value,
            		'created' => date('Y-m-d H:i:s'),
            		'created_by' => $user_id,
            		'modified' => date('Y-m-d H:i:s'),
            		'modified_by' => $user_id
            );
            
            $this->CI->marketplace_m->insert_marketplace_store_setting($insert_data);
        }
    }    
}

?>