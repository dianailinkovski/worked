<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Marketplace_m extends MY_Model 
{
    // make use of a data cache for pages that hit the db multiple times
    public $is_retailer_cache;
    public $marketplace_cache;
    
    /**
     * Find all of our marketplaces that have listing of sellers that are part
     * of their marketplace network.
     * 
     * @author Christophe
     */
    public function get_all_active_marketplaces()
    {
        $query_str = "
            SELECT marketplace, COUNT(marketplace) as mcount
            FROM crowl_merchant_name_new
            GROUP BY marketplace
            HAVING mcount > 1
            ORDER BY mcount DESC
        ";
        
        return $this->db->query($query_str)->result_array();
    }

    /**
     * Determine if there are any sellers with a specific marketplace that sell products
     * for a customer's store.
     * 
     * $marketplace_name = 'walmart'
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $marketplace_name
     * @return array
     */
    public function marketplace_listings_for_store($store_id, $marketplace_name)
    {
        $store_id = intval($store_id);
        
        // if any, will return rows in the crowl_product_list_new table whose products
        // are being sold with sellers of a marketplace
        $query_str = "
            SELECT cpl.*
            FROM crowl_product_list_new cpl
            INNER JOIN products p ON p.upc_code = cpl.upc
            WHERE cpl.marketplace = '{$marketplace_name}'
            AND p.store_id = {$store_id}
        ";
        
        return $this->db->query($query_str)->result_array();
    }	
	
	/**
	 * Retrieve the display name of a marketplace from the DB
	 *
	 * @param String $marketplace
	 * @return Stirng
	 */
	public function display_name($marketplace) {
		$ret = ucwords($marketplace);

		$row = $this->get_marketplace_by_name(strtolower($marketplace));
		if (isset($row['display_name']))
			$ret = $row['display_name'];

		return $ret;
	}

	/**
	 * Retrieve a marketplace record by its lookup name
	 * @param String $name
	 * @return array
	 */
	public function get_marketplace_by_name($name) {
		return $this->_get_marketplace($name, 'name');
	}
	
	/**
	 * Retrieve a marketplace record by its URL (hostname)
	 *
	 * @param String $url
	 * @return Int id 
	 */
	public function get_marketplace_id_by_url($url)
	{
		$url_parts = parse_url($url);
		
		$host = $url_parts['host'];
		
		// bad code below - breaks with http://www.ndnsuperstore.com
		//$dotComDomain = str_replace('www.', '', $host);
		//$dotComDomain = str_replace('store.', '', $dotComDomain);
		
		// remove www. or other sub-domain
		$host_parts = explode('.', $host);
		
		if (count($host_parts) == 3)
		{
		    $dotComDomain = $host_parts[1] . '.' . $host_parts[2];
		}
		else if (count($host_parts) == 2)
		{
		    $dotComDomain = $host_parts[0] . '.' . $host_parts[1];
		}
		
		log_message('debug', 'get_marketplace_id_by_url() - $url: ' . $url);
		log_message('debug', 'get_marketplace_id_by_url() - $host: ' . $host);
		log_message('debug', 'get_marketplace_id_by_url() - $dotComDomain: ' . $dotComDomain);
		
		$mp = $this->get_marketplace_by_display_name($dotComDomain);
		
		if ($mp == FALSE || empty($mp))
		{
		    return false;
		}
		else
		{
		    return $mp['id'];
		}
	}
	
	/**
	 * Retrieve a marketplace record by its display name
	 *
	 * @param String $display_name
	 * @return array
	 */
	public function get_marketplace_by_display_name($display_name) {
		return $this->_get_marketplace($display_name, 'display_name');
	}

	/**
	 * Retrieve a marketplace record by its ID
	 *
	 * @param int $id
	 * @return array
	 */
	public function get_marketplace_by_id($id) {
		return $this->_get_marketplace($id, 'id');
	}

	public function get_search_url_by_api($api){
		$mp = $this->_get_marketplace($api, 'name');
		$domain = strtolower($mp['display_name']);
		if(preg_match('/^[store.,www.]/', $domain)){ // TODO: configure a list of known subdomains
			return "http://" . $domain;
		}
		else{
			return "http://www." . $domain;
		}
	}
	


	/**
	 * Get a marketplace record by an index
	 *
	 * @param String $key
	 * @param String $type { default : 'id' }
	 * @return array
	 */
	protected function _get_marketplace($key, $type = 'id') {
		switch ($type) {
			case 'name':
				$this->db->where('name', $key);
				break;
			case 'display_name':
				$this->db->where('display_name', $key);
				break;
			case 'id':
			default:
				$this->db->where('id', (int)$key);
		}
		$this->db->mysql_cache();
		return $this->db->get($this->_table_marketplaces)->row_array();
	}
	
	/**
	 * Check if a marketplace is a retailer
	 *
	 * @param String $marketplace
	 * @return boolean
	 */
	public function is_retailer($marketplace) {
		$ret = null;
		
		if(isset($this->is_retailer_cache[$marketplace])){
			return $this->is_retailer_cache[$marketplace];
		}

		$res = $this->db
			->select('is_retailer')
			->where('name', strtolower($marketplace))
			->get($this->_table_marketplaces)
			->row_array();

		if (isset($res['is_retailer'])){
			$ret = (int)$res['is_retailer'];
			$this->is_retailer_cache[$marketplace] = (boolean)$ret;
		}
		
		return (boolean)$ret;
	}

	/**
	 * Retrieve an array of marketplace names by Store ID
	 *
	 * @author unknown, Christophe
	 * @param int $storeId
	 * @param int $isRetailer
	 * @return array
	 */
	public function get_marketplaces_by_store_id($storeId, $isRetailer = 0)
	{
	  $isRetailer = intval($isRetailer);
	    
	  // removed: - Christophe 9/25/2015
	  // AND cpln.last_date > (unix_timestamp()-(60*60*24*30))
		$sql = "
		    SELECT DISTINCT mp.name 
		    FROM crowl_product_list_new AS cpln
				JOIN products p ON cpln.upc = p.upc_code
				JOIN store s ON p.store_id = s.id
				JOIN marketplaces mp ON cpln.marketplace = mp.name
				WHERE s.id = {$storeId}			
				AND cpln.marketplace IS NOT NULL
				AND mp.is_active = 1
				AND mp.is_retailer = {$isRetailer}
				ORDER BY name
    ";
				
		$markets = $this->db->query($sql)->result('array');
		
		return $markets;
	}
	
	
	/**
	 * Retrieve an array of marketplace rows by Store ID, via category
	 *
	 * @param int $storeId
	 * @param mixed $isRetailer: 0,1, or 'all' // not used yet
	 * @return array
	 */
	public function get_marketplaces_by_storeid_using_categories($storeId=0, $isRetailer=0) 
	{
		$storeId = (int)$storeId;
		$and_retailer = '';
		//if($isRetailer != 'all'){
		//	$and_retailer = 'AND mp.is_retailer='.$isRetailer; // todo: update existing code
		//}
		$sql = "SELECT SQL_CACHE mp.* FROM marketplaces mp 
					join categories_marketplaces cm on mp.id = cm.mId
					join categories_stores cs on cs.cId = cm.cId
				where cs.sId = {$storeId}
				{$and_retailer}
				order by mp.name";
				
		$markets = $this->db->query($sql)->result('array');
		return $markets;
	}


	/**
	 * Check if a marketplace is a upc_lookup crawler
	 *
	 * @param String $marketplace
	 * @return boolean
	 */
	public function is_upc_lookup($marketplace) {
		$ret = null;

		$res = $this->db
			->select('upc_lookup')
			->where('name', strtolower($marketplace))
			->get($this->_table_marketplaces)
			->row_array();

		if (isset($res['upc_lookup']))
			$ret = (int)$res['upc_lookup'];

		return (boolean)$ret;
	}

	/**
	 * Check if a marketplace is active
	 *
	 * @param String $marketplace
	 * @return boolean
	 */
	public function is_active($marketplace) {
		if(empty($marketplace))
			return false;
		
		$ret = null;

		$res = $this->db
			->select('is_active')
			->mysql_cache()
			->where('name', strtolower($marketplace))
			->get($this->_table_marketplaces)
			->row_array();

		if (isset($res['is_active']))
			$ret = (int)$res['is_active'];

		return (boolean)$ret;
	}
	
    /**
     * Get a setting row from the marketplace_store_settings table.
     * 
     * @author Christophe
     * @param string $marketplace_name
     * @param int $store_id
     * @param string $setting_name
     * @return array
     */	
    public function get_marketplace_store_setting($marketplace_name, $store_id, $setting_name)
    {
        $this->db->select('*');
        $this->db->from('marketplace_store_settings');
        $this->db->where('marketplace', $marketplace_name);
        $this->db->where('store_id', $store_id);
        $this->db->where('name', $setting_name);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }	

	/**
	 * Get an array of marketplace records
	 *
	 * @param mixed $fields { default : array('name') }
	 * @param boolean $include_retailers { default : true }
	 * @param boolean $include_inactive { default : false }
	 * @return array
	 */
	public function get_marketplaces($fields = array('name'), $include_retailers = true, $include_inactive = false) {
		if ( ! is_array($fields))
			$fields = array((string)$fields);
		if ( ! empty($fields) AND $fields[0] !== '*')
			$this->db->select(implode(',', $fields));
		if ( ! $include_retailers)
			$this->db->where('is_retailer', false);
		if ( ! $include_inactive)
			$this->db->where('is_active', true);
			
		$this->db->orderby('name');
		$this->db->mysql_cache();
		return $this->db->get($this->_table_marketplaces)->result_array();
	}

	/**
	 * Get an array of retailer records
	 *
	 * @param mixed $fields { default : array('name') }
	 * @param boolean $include_inactive { default : false }
	 * @return array
	 */
	public function get_retailers($fields = array('name'), $include_inactive = false) {
		if ( ! is_array($fields))
			$fields = array((string)$fields);
		if ( ! empty($fields) AND $fields[0] !== '*')
			$this->db->select(implode(',', $fields));
		if ( ! $include_inactive)
			$this->db->where('is_active', true);

		return $this->db
			->where('is_retailer', true)
			->get($this->_table_marketplaces)
			->result_array();
	}

	/**
	 * Get an array of marketplaces by the upc_lookup flag
	 *
	 * @param type $upc_lookup
	 * @param type $include_inactive
	 * @return type
	 */
	public function get_marketplaces_by_upc_lookup($upc_lookup = false, $include_inactive = false) {
		if ( ! $include_inactive)
			$this->db->where('is_active', 1);

		return $this->db
			->where('upc_lookup', $upc_lookup ? '1' : '0')
			->order_by('name')
			->get($this->_table_marketplaces)
			->result_array();
	}

	/**
	 * Retrieve a products_lookup record by its primary key
	 *
	 * @param int $product_id
	 * @param int $marketplace_id
	 * @return array/false
	 */
	public function get_product_lookup($product_id=false, $marketplace_id=false) {
		if(!empty($product_id)){
			$this->db->where('product_id', (int)$product_id);
		}
		if(!empty($marketplace_id)){
			$this->db->where('marketplace_id', (int)$marketplace_id);
		}
		return $this->db
			->get($this->_table_products_lookup)
			->row_array();
	}

	public function get_product_lookup_by_merchant_id_and_store_id($product_id=false, $marketplace_id=false, $store_id){
		if(!empty($product_id)){
			$this->db->where('pl.product_id', (int)$product_id);
		}
		if(!empty($marketplace_id)){
			$this->db->where('pl.marketplace_id', (int)$marketplace_id);
		}
		if(!empty($store_id)){
			$this->db->join('products p', 'p.id=pl.product_id');
			$this->db->where('p.store_id', (int)$store_id);
		}
		return $this->db
			->get($this->_table_products_lookup.' pl')
			->result_array();
	}

	/**
	 * Get the lookup URL for a marketplace product
	 *
	 * @param int $product_id
	 * @param int $marketplace_id
	 * @return String/false
	 */
	public function get_product_lookup_url($product_id, $marketplace_id) {
		$row = $this->get_product_lookup($product_id, $marketplace_id);

		return ! empty($row['url']) ? $row['url'] : false;
	}

    /**
     * Insert or update (into products_lookup table) a lookup URL for a product/marketplace combination.
     *
     * Updated:
     * 10/30/2015 - if UPC is provided then add UPC value to inserted row
     * 
     * @author unknown, Christophe
     * @param int $product_id
     * @param int $marketplace_id
     * @param String $url
     * @return boolean
     */
    public function set_product_lookup($product_id, $marketplace_id, $url, $upc = FALSE)
    {    
        if ($upc !== FALSE)
        {
            $this->db->query("DELETE FROM rejected WHERE mpId={$marketplace_id} and upc='{$upc}'");
        
            $query =
            'INSERT INTO ' . $this->_table_products_lookup . ' (product_id, upc, marketplace_id, url) ' .
            'VALUES (?, ?, ?, ?) ' . 
            'ON DUPLICATE KEY UPDATE url = ?;';
        
            $params = array(
            		(int)$product_id,
                $upc,            
            		(int)$marketplace_id,
            		$url,
            		$url
            );
        
            return $this->db->query($query, $params);			
        }
        else 
        {
            // interesting way to insert or update a row - Christophe
            $query = 
                'INSERT INTO ' . $this->_table_products_lookup . ' (product_id, marketplace_id, url) ' .
                'VALUES (?, ?, ?) ' . 
                'ON DUPLICATE KEY UPDATE url = ?;';
            
            $params = array(
                (int)$product_id,
                (int)$marketplace_id,
                $url,
                $url
            );
            
            return $this->db->query($query, $params);
        }
    }
	
    /**
     * Insert a new row into the marketplace_store_settings table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */	
    public function insert_marketplace_store_setting($insert_data)
    {
        $this->db->insert('marketplace_store_settings', $insert_data);
        
        return $this->db->insert_id();        
    }	
	
    /**
     * update fail flag for a given URL
     *
     * @param String $url
     * @param String $fails - default fails+1
     * @return boolean
     */
    public function update_product_lookup_fail_flag($url, $fails='fails+1') 
    {
        $query = 'UPDATE ' . $this->_table_products_lookup . ' SET fails='.$fails.' WHERE url="'.$url.'" ';
        
        return $this->db->query($query);
    }

    /**
     * Update a row in the marketplace_store_settings table.
     * 
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_marketplace_store_setting($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('marketplace_store_settings', $update_data);        
    }
}
