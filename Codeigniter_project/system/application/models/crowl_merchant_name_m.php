<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Crowl_merchant_name_m extends MY_Model
{
	function __construct(){
	}
	
    /**
     * Get merchant row from crowl_merchant_name_new.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @return array
     */
    public function get_merchant_by_id($merchant_id)
    {
        $merchant_id = intval($merchant_id);
        
        $this->db->select('*');
        $this->db->from('crowl_merchant_name_new');
        $this->db->where('id', $merchant_id);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }
    
    // cache seller names in xpath_helper
    public function get_all_by_marketplace_indexed_by_seller_id($marketplace){
        $sql = "SELECT seller_id, merchant_name
            FROM {$this->_table_crowl_merchant_name}
            WHERE marketplace='{$marketplace}'
            AND DATE(created) > DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
            ORDER BY created ASC";
        $arr = $this->db->query($sql)->result_object();
        $merchants = array();
        foreach($arr as $id => $m){
            $merchants[$m->seller_id] = array('name' => $m->merchant_name, 'id' => $id);
        }
        return $merchants;
    }

    /**
     * Check to see if merchant has any violated products with a store.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $marketplace_name
     * @param string $start_time
     * @param string $end_time
     * @return boolean
     */
    public function is_merchant_in_violation($store_id, $marketplace_name, $start_time, $end_time)
    {
        $start_time_int = strtotime($start_time);
        $end_time_int = strtotime($end_time);
        
        $violation_query = "
            SELECT DISTINCT cpl.merchant_name_id
            FROM crowl_product_list_new cpl
            INNER JOIN products p on p.upc_code = cpl.upc
            WHERE p.store_id = {$store_id}
            AND cpl.marketplace = '{$marketplace_name}'
            AND cpl.violated = 1
            AND cpl.last_date >= {$start_time_int}
            AND cpl.last_date <= {$end_time_int}
        ";
        
        $violations = $this->db->query($violation_query)->result_array();
        
        if (empty($violations))
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }    
    
    /**
     * Check to see if a retailer has any violated products with a store.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $marketplace_name
     * @param string $start_time
     * @param string $end_time
     * @return boolean
     */
    public function is_retailer_in_violation($store_id, $marketplace_name, $start_time, $end_time)
    {
        $start_time_int = strtotime($start_time);
        $end_time_int = strtotime($end_time);
        
        $violation_query = "
            SELECT DISTINCT cpl.merchant_name_id
            FROM crowl_product_list_new cpl
            INNER JOIN products p on p.upc_code = cpl.upc
            INNER JOIN marketplaces m ON m.name = cpl.marketplace
            WHERE p.store_id = {$store_id}
            AND m.name = '{$marketplace_name}'
            AND m.is_retailer = 1
            AND m.is_active = 1
            AND cpl.violated = 1
            AND cpl.last_date >= {$start_time_int}
            AND cpl.last_date <= {$end_time_int}
        ";
        
        $violations = $this->db->query($violation_query)->result_array();
        
        if (empty($violations))
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
    
    /**
     * Get array of retailer merchant IDs that are currently in violation for a store.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $start_time
     * @param string $end_time
     * @return array
     */
    public function get_violated_retailers($store_id, $start_time, $end_time)
    {
        $start_time_int = strtotime($start_time);
        $end_time_int = strtotime($end_time);
        
        /*
        
        SELECT DISTINCT cpl.merchant_name_id
        FROM crowl_product_list_new cpl
        INNER JOIN products p on p.upc_code = cpl.upc
        INNER JOIN marketplaces m ON m.name = cpl.marketplace
        WHERE p.store_id = 621
        AND m.is_retailer = 1
        AND m.is_active = 1
        AND cpl.violated = 1
        AND cpl.last_date >= 1442839411      
        AND cpl.last_date <= 1442962728
        
        */
        
        $violation_query = "
            SELECT DISTINCT cpl.merchant_name_id
            FROM crowl_product_list_new cpl
            INNER JOIN products p on p.upc_code = cpl.upc
            INNER JOIN marketplaces m ON m.name = cpl.marketplace
            WHERE p.store_id = {$store_id}
            AND m.is_retailer = 1
            AND m.is_active = 1
            AND cpl.violated = 1
            AND cpl.last_date >= {$start_time_int}      
            AND cpl.last_date <= {$end_time_int}                 
        ";
        
        $violations = $this->db->query($violation_query)->result_array();
        
        //echo $this->db->last_query(); exit();
        
        return $violations;
    }

	/**
	 * Get a crowl_merchant_name record by an index
	 *
	 * @param String $key
	 * @param String $type { default : 'id' }
	 * @return array
	 */
	protected function _get_crowl_merchant_name($key, $type = 'id') {
		switch ($type) {
			case 'marketplace':
				$this->db->where('marketplace', $key);
				break;
			case 'seller_id':
				$this->db->where('seller_id', $key);
				break;
			case 'id':
			default:
				$this->db->where('id', (int)$key);
		}

		return $this->db->get($this->_table_crowl_merchant_name)->row_array();
	}

}





?>