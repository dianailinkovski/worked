<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products_m extends MY_Model {

	public static $tableName = 'products';

	function add_product($product_info = array()) {
		return $this->insert($product_info);
	}

	function get_results() {
		return $this->get_all();
	}

	function update_product_info($product_info = array()) {
		return (boolean)$this->update_many($product_info['id'], $product_info);
	}

    /**
     * Get store product by UPC code.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $upc_code
     * @return array
     */
    public function get_product_by_upc($store_id, $upc_code)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('store_id', $store_id);
        $this->db->where('upc_code', $upc_code);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }
	
    /**
     * Check to see if a product exists for a store and return its product ID.
     * 
     * @param int $store_id
     * @param string $upc_code
     * @param string $sku
     * @return int
     */
    public function check_product_exist($store_id, $upc_code, $sku = NULL) 
    {
        /*
        // deprecated by Christophe on 12/1/2015
        // some customers don't consistently use SKU so could get duplicates
        $res = $this->db
        ->select('id')
        ->where_in('store_id', getStoreIdList($store_id))
        ->where('upc_code', $upc_code)
        ->where('sku', $sku)
        ->get($this->_table_products)
        ->result_array();
        
        return isset($res[0]['id']) ? $res[0]['id'] : 0;
        */
        
        $product = $this->get_product_by_upc($store_id, $upc_code);
        
        $product_id = empty($product) ? 0 : intval($product['id']);
        
        return $product_id;
    }

	function getBadUpcByStoreId($store_id = 0) {
		$no_find_thresh = $this->config->item('bad_upc_threshold') OR '2 weeks';
		$last_date = strtotime('-' . $no_find_thresh);

		$recently_crawled_res = $this->db
			->select('p.upc_code')
			->distinct()
			->join($this->_table_crowl_product_list . ' cpl', 'p.upc_code=cpl.upc', 'left')
			->where_in('p.store_id', getStoreIdList($store_id))
			->where('p.is_processed', 1)
			->where('cpl.last_date >', $last_date)
			->get($this->_table_products . ' p');

		$recently_crawled = array();
		for ($i = 0, $n = $recently_crawled_res->num_rows(); $i < $n; $i++)
			$recently_crawled[] = $recently_crawled_res->row($i)->upc_code;

		if ( ! empty($recently_crawled))
			$this->db->where_not_in('p.upc_code', $recently_crawled);
		$res = $this->db
			->select('p.*')
			->where_in('p.store_id', getStoreIdList($store_id))
			->where('(' . $this->db->protect_identifiers('p.is_processed') . ' = 1 OR length(' . $this->db->protect_identifiers('p.upc_code') . ') < 12)')
			->group_by('p.upc_code')
			->get($this->_table_products . ' p')
			->result_array();

		return $res;
	}
	
    /**
     * Get products for a specific store.
     * 
     * @author Christophe
     * @param unknown_type $store_id
     * @param unknown_type $is_archived
     */
    public function get_products_for_store($store_id, $is_archived = 0)
    {
        $store_id = intval($store_id);
        $is_archived = intval($is_archived);
        
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('store_id', $store_id);
        $this->db->where('is_archived', $is_archived);
        $this->db->order_by('title', 'asc');
         
        $query = $this->db->get();
         
        return $query->result_array();        
    }	

	function getByStore($store_id, $string = '') {
		if (!empty($string))
			$this->db->where_not_in('id', $string);
		$this->db->where_in('store_id', getStoreIdList($store_id));

		$res = $this->db
			->get($this->_table_products)
			->result_array();

		return $res;
	}

	function addGroup($group_name, $brandId = 0) {
		$this->db->insert('groups', array('name' => $group_name, 'store_id' => $brandId));
		$result = $this->db->get_where('groups', array('name' => $group_name, 'store_id' => $brandId))->result('array');
		;
		if (count($result) > 0) {
			return $result[0]['id'];
		}
	}

	function isGroupExist($group_name, $brandId) {
		$result = $this->db->get_where('groups', array('name' => $group_name, 'store_id' => $brandId))->result('array');

		return (count($result) > 0);
	}

	function addGroupItems($group_id, $itemList) {
		for ($i = 0, $n = count($itemList); $i < $n; $i++) {
			$this->db->insert('group_products', array('group_id' => $group_id, 'product_id' => $itemList[$i]));
		}
	}

	function getGroupByID($group_id) {
		return $this->db
				->where('id', (int)$group_id)
				->get($this->_table_groups)
				->row_array();
	}

	function getGroupMerchant($group_id) {
		$store_id = $this->getGroupStore($group_id);
		$res = $this->db
			->select('user_id')
			->where('id', (int)$store_id)
			->get($this->_table_store)
			->row();

		return !empty($res->user_id) ? $res->user_id : 0;
	}

	function getGroupStore($group_id) {
		$group = $this->getGroupByID($group_id);

		return $group ? $group['store_id'] : 0;
	}

	function getProductsByGroupId($group_id, $product_info = FALSE) {
		if ($product_info)
			$this->db
				->select('gp.*, p.title, p.upc_code, p.brand, p.sku, p.retail_price, p.price_floor, p.wholesale_price, p.is_tracked')
				->join($this->_table_products . ' p', 'gp.product_id=p.id');

		$result = $this->db
			->where('group_id', (int)$group_id)
			->get($this->_table_group_products . ' gp')
			->result('array');

		return $result;
	}

	function getGroups($brandId = 0, $perpage = 0, $offset = 0) {
		if ($perpage > 0)
			$this->db->limit($perpage, $offset);

		$result = $this->db
			->select('g.id as id, g.name as name, count(p.product_id) as count')
			->join($this->_table_group_products . ' p', 'g.id=p.group_id', 'left')
			->where_in('g.store_id', getStoreIdList($brandId))
			->group_by('g.name')
			->get($this->_table_groups . ' g')
			->result();
		return $result;
	}

	function getTotalGroups($brandId = 0) {

		$result = $this->db->query('SELECT g.id as id, g.name as name, count(p.product_id) as count FROM groups g, group_products p WHERE g.id = p.group_id AND g.store_id IN (' . getStoreIdList($brandId, TRUE) . ') group by g.name')->result('array');
		return count($result);
	}

	function deleteGroup($group_id) {
		$this->db->delete('groups', array('id' => $group_id));
		$this->db->delete('group_products', array('group_id' => $group_id));
	}

	function getGroupsHTML($brandId) {
		$result = $this->db
			->select('g.id AS id, g.name AS name, count(p.product_id) AS count')
			->join($this->_table_group_products . ' p', 'g.id=p.group_id')
			->where_in('g.store_id', getStoreIdList($brandId))
			->group_by('g.id')
			->get($this->_table_groups . ' g')
			->result_array();

		$html = '';
		for ($i = 0; $i < count($result); $i++) {
			$html .= '<li class="group_li" data-groupid="' . $result[$i]['id'] . '" data-groupname="' . $result[$i]['name'] . '"><span class="unmagnify_group">Cancel</span><img src="' . base_url() . 'images/magnify.png" alt="View" class="magnify_group" /><img src="' . base_url() . 'images/del.png" alt="Delete"  class="del_group" onclick="javascript:return del_group(' . $result[$i]['id'] . ');" />' . $result[$i]['name'] . ' (' . $result[$i]['count'] . ')</li>';
		}

		return $html;
	}

	function markTracked($store_id, $ids) {
		for ($i = 0; $i < count($ids); $i++) {
			$this->db->where(array('store_id' => $store_id, 'id' => $ids[$i]));
			$this->db->update('products', array('is_tracked' => '1'));
		}
	}

	function markUnTracked($store_id, $ids) {
		for ($i = 0; $i < count($ids); $i++) {
			$this->db->where(array('store_id' => $store_id, 'id' => $ids[$i]));
			$this->db->update('products', array('is_tracked' => '0'));
		}
	}

	function get_product_details($upccode, $store_id=false) {
		$where = array('upc_code' => $upccode);
		if($store_id){
			$where['store_id'] = $store_id;
		}
		$this->db->where($where);
		$result = $this->db->get('products')->result('array');
		return $result;
	}

	/* -----3rd March 2012----- */

	function getMultiSelectProduct($msid, $qry) {
		$res = $this->db
			->select('id,upc_code,title')
			->where_in('store_id', getStoreIdList($msid))
			->like('title', $qry)
			->order_by('title', 'asc')
			->limit(20);

		/** ADDED FOR removing &amp */
		$resultAr = array();
		foreach ($res as $result) {
			$resultAr[] = array(
				'id' => $result->id,
				'upc_code' => $result->upc_code,
				'title' => html_entity_decode($result->title)
			);
		}
		return $resultAr;
	}

	protected function setArchived($store_id, $ids, $archive_flag) {
		$archive_flag = $archive_flag ? 1 : 0;

		return $this->db
				->where_in('store_id', getStoreIdList($store_id))
				->where_in('id', $ids)
				->update(self::$tableName, array('is_archived' => $archive_flag));
	}

	/**
	 * function markArchived
	 */
	function markArchived($store_id, $ids) {
		$this->setArchived($store_id, $ids, TRUE);
	}

	/**
	 * function markUnArchived
	 */
	function markUnArchived($store_id, $ids) {
		$this->setArchived($store_id, $ids, FALSE);
	}

	function markDeleted($store_id, $ids) {
		return $this->db
				->where_in('store_id', getStoreIdList($store_id))
				->where_in('id', $ids)
				->update(self::$tableName, array('deleted_at' => date('Y-m-d H:i:s')));
	}

	/**
	 * function removeFromGroup
	 *
	 *
	 */
	function removeFromGroup($group, array $products) {
		// Remove the products from the group
		$ret = $this->db
			->where('group_id', (int)$group)
			->where_in('product_id', $products)
			->delete($this->_table_group_products);

		// Remove the group if there are no products in it
		$this->removeGroupIfEmpty($group);

		return $ret;
	}

	/**
	 * function removeGroupIfEmpty
	 *
	 *
	 */
	function removeGroupIfEmpty($groupID) {
		$products = $this->getProductsByGroupId($groupID);
		if (empty($products))
			return $this->db
					->where('id', (int)$groupID)
					->delete($this->_table_groups);

		return null;
	}

	/**
	 *
	 * function getProductsById
	 *
	 *
	 */
	function getProductsById($store_id = 0, $ids) {
		if ($store_id)
			$this->db->where_in('store_id', getStoreIdList($store_id));

		$this->db->where_in('id', $ids);

		return $this->db->get(self::$tableName)->result_array();
	}

	function get_product_id_from_upc($upc, $store_id = null) {
		if(!is_null($store_id) && is_numeric($store_id)) {
			$this->db->where('store_id', $store_id);
		}
		$this->db->where('upc_code', $upc)
			->order_by('created_at', 'desc')
			->limit(1);
		$result = $this->db->get(self::$tableName);
		if ($result->num_rows() == 1) {
			$row = $result->row_array();
			return $row['id'];
		}
		return null;
	}
	
    /**
     * Find a product record by the primary key ID.
     * 
     * @author Christophe
     * @param int $product_id
     * @return array
     */
    public function get_product_by_id($product_id)
    {
        $product_id = intval($product_id);
    
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('id', $product_id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }
    
    /**
     * Get all products for a specific store.
     * 
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_all_products_for_store($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('store_id', $store_id);
        
        $query = $this->db->get();
        
        return $query->result_array();        
    }
	
    /**
     * Find a product lookup by URL and UPC.
     * 
     * @author Christophe
     * @param string $upc
     * @param string $url
     * @return array
     */	
    public function get_lookup_by_url($upc, $url)
    {        
        $this->db->select('*');
        $this->db->from('products_lookup');
        $this->db->where('upc', $upc);
        $this->db->where('url', $url);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }	
	
    /**
     * Get row from product_violations_per_day table.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $date_str
     * @return array
     */	
    public function get_day_violation_row($store_id, $date_str)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('product_violations_per_day');
        $this->db->where('store_id', $store_id);
        $this->db->where('select_date', $date_str);
         
        $query = $this->db->get();
         
        return $query->row_array();        
    }	
        
	function get_product_title_by_upc($upc){
        $result = $this->db
				->select('title')
				->where('upc_code', $upc)
				->limit(1)
				->get(self::$tableName);
		if ($result->num_rows() == 1) {
			$row = $result->row_array();
			return $row['title'];
		}
		return null;
	}
	
        function get_product_id_from_detail($store_id,$upc,$sku) {
            $this->db->where('upc_code', $upc);
            $this->db->where('sku', $sku);
            $this->db->where('store_id', $store_id);
		$result = $this->db->get(self::$tableName);
		if ($result->num_rows() == 1) {
			$row = $result->row_array();
			return $row['id'];
		}
		return null;
        }

	/** function getProductsMonitoredCount * */
	function getProductsMonitoredCount($store_id) {
		// Let's move away from using marketplace flags so that the app can be expanded
		$row = $this->db
			->join($this->_table_crowl_product_list . ' cpl', 'cpl.upc=p.upc_code')
			->where_in('p.store_id', getStoreIdList($store_id))
			->where(array('p.is_tracked' => 1, 'p.status' => 1, 'p.is_archived' => 0))
			->group_by('p.id')
			->get($this->table . ' p')
			->num_rows();

		return $row ? $row : 0;
	}

	function getlqxListProducts($store_id) {
		$res = $this->db
			->select('id,upc_code,title')
			->where_in('store_id', getStoreIdList($store_id))
			->where('title !=', '')
			->where(array('deleted_at' => NULL))
			->order_by('title', 'ASC')
			->get($this->_table_products)
			->result();

		/* ADDED FOR removing &amp */
		$resultAr = array();
		foreach ($res as $result) {
			$resultAr[] = array(
				'id' => $result->id,
				'title' => html_entity_decode($result->title)
			);
		}

		return $resultAr;
	}

	/**
	 * Retrieve products by upc from products table where floor price is greater than specified value
	 *
	 * @param String $upc
	 * @param float $min_floor
	 *
	 * @return array
	 */
	public function get_products_by_floor($upc, $min_floor, $store_id) {
		$ret = false;

		$map = getPricePoint($upc, $store_id, 'price_floor', time());

echo "MAP: $map\n";

		if ($map > $min_floor) {
			$result = $this->db
				->select('p.id, p.store_id')
				->where('p.upc_code', $upc)
				->where_in('p.store_id', getStoreIdList($store_id))
				->get($this->_table_products . ' p');

//echo "MAP result: ";
//print_r($result->result_array());

			if ($result->num_rows() == 1) {
				$ret = $result->result_array();
			}
		}

		return $ret;
	}

	/**
	 * Check whether a upc is in the products table
	 *
	 * @param String $upc
	 *
	 * @return bool
	 */
	public function product_exists_by_upc($upc) {
		return $this->_product_exists($upc, 'upc_code');
	}

	/**
	 * Check whether a value is in the products table by column
	 *
	 * @param mixed $key
	 * @param String $type { default : 'upc_code' }
	 *
	 * @return bool
	 */
	protected function _product_exists($key, $type = 'upc_code') {
		if ($type === 'upc_code')
			$this->db->where('upc_code', $key);
		elseif ($type === 'sku')
			$this->db->where('sku', $key);

		$res = $this->db
			->select('count(*) as count')
			->get($this->table)
			->row();

		return ($res AND $res->count > 0);
	}

	public function insertBrandProduct($store_id, $product_id, $owner = FALSE) {
		$data = array(
			'store_id' => (int)$store_id,
			'product_id' => (int)$product_id,
			'owner' => $owner ? 1 : 0
		);
		return $this->db->insert($this->_table_brand_product, $data);
	}

	public function insertBrandProductAssociation($owner_brand_product, $competitor_brand_product) {
		$data = array(
			'owner_brand_product' => (int)$owner_brand_product,
			'competitor_brand_product' => (int)$competitor_brand_product
		);

		return $this->db->insert($this->_table_brand_product_product, $data);
	}

	public function removeBrandProduct($store_id, $product_id) {
		$data = array(
			'store_id' => (int)$store_id,
			'product_id' => (int)$product_id
		);

		$record = $this->db
			->select('id')
			->where($data)
			->get($this->_table_brand_product)
			->row_array();

		if (isset($record['id']))
			$success = $this->db->delete($this->_table_brand_product_product, array('competitor_brand_product' => (int)$record['id']));

		return $success ? $this->db->delete($this->_table_brand_product, $data) : FALSE;
	}

	public function removeCompetitorAssociation($owner_brand_product, $competitor_brand_product) {
		$data = array(
			'owner_brand_product' => (int)$owner_brand_product,
			'competitor_brand_product' => (int)$competitor_brand_product
		);

		$this->db->delete($this->_table_brand_product_product, $data);
	}

	/**
	 * Insert a row into the products_pricing table
	 *
	 * @param int $product_id
	 * @param String $type
	 * @param float $value
	 * @param String $start
	 * @param String $end
	 * @return boolean
	 */
	public function insertProductsPricing($product_id, $type, $value, $start, $end) {
		$data = array(
			'product_id' => (int)$product_id,
			'pricing_type' => $type,
			'pricing_value' => (float)$value,
			'pricing_start' => date('Y-m-d H:i:s', strtotime($start)),
			'pricing_end' => date('Y-m-d H:i:s', strtotime($end))
		);

		return $this->db->insert($this->_table_products_pricing, $data);
	}

	/**
	 * Insert a price_floor (MAP) record into the products_pricing table
	 *
	 * @param int $product_id
	 * @param float $map
	 * @param String $start
	 * @param String $end
	 * @return boolean
	 */
	public function insertMAP($product_id, $map, $start, $end) {
		return $this->insertProductsPricing($product_id, 'price_floor', $map, $start, $end);
	}

	/**
	 * Update a row in the products_pricing table
	 *
	 * @param array $where
	 * @param float $value { default : NULL }
	 * @param String $start { default : NULL }
	 * @param String $end { default : NULL }
	 * @return boolean/NULL
	 */
	protected function updateProductsPricing(array $where, $value = NULL, $start = NULL, $end = NULL) {
		$ret = NULL;
		$data = array();
		if (isset($value))
			$data['pricing_value'] = (float)$value;
		if (isset($start))
			$data['pricing_start'] = date('Y-m-d H:i:s', strtotime($start));
		if (isset($end))
			$data['pricing_end'] = date('Y-m-d H:i:s', strtotime($end));

		if (!empty($data))
			$ret = $this->db->update($this->_table_products_pricing, $data, $where);

		return $ret;
	}

	/**
	 * Update a row in the products_pricing table by its id
	 *
	 * @param int $pricing_id
	 * @param float $value { default : NULL }
	 * @param String $start { default : NULL }
	 * @param String $end { default : NULL }
	 * @return boolean
	 */
	public function updateProductsPricingById($pricing_id, $value = NULL, $start = NULL, $end = NULL) {
		$where = array(
			'pricing_id' => (int)$pricing_id
		);
		return $this->updateProductsPricing($where, $value, $start, $end);
	}

	/**
	 * Update a row in the products_pricing table by its product id and
	 * its time period. If both start and end are set the row's time period
	 * must contain the time period passed. If only start is set the row's time
	 * period must contain the date passed.
	 *
	 * @param int $product_id
	 * @param float $map
	 * @param String $start
	 * @param String $end { default : NULL }
	 * @return boolean
	 */
	public function updateMAPByTimeRange($product_id, $map, $start, $end = NULL) {
		if (!isset($end))
			$end = $start;

		$where = array(
			'product_id' => (int)$product_id,
			'pricing_type' => 'price_floor',
			'pricing_start' >= date('Y-m-d H:i:s', strtotime($start)),
			'pricing_end' <= date('Y-m-d H:i:s', strtotime($end))
		);
		return $this->updateProductsPricing($where, $map);
	}

	/**
	 * Update all price_floor (MAP) rows in the products_pricing table for a product
	 *
	 * @param int $product_id
	 * @param float $map
	 * @return boolean
	 */
	public function updateMAPByProduct($product_id, $map) {
		$where = array(
			'product_id' => (int)$product_id,
			'pricing_type' => 'price_floor'
		);
		return $this->updateProductsPricing($where, $map);
	}

	public function getProductsPricingById($id) {
		$this->db->where('pricing_id', (int)$id);
		$res = $this->getProductsPricing();

		return empty($res[0]) ? FALSE : $res[0];
	}

	public function isOwnerProductsPricing($id, $store_id) {
		$res = $this->db
			->select('count(*) as count')
			->join($this->_table_products . ' p', 'p.id=pp.product_id')
			->where('pp.pricing_id', (int)$id)
			->where_in('p.store_id', getStoreIdList($store_id))
			->get($this->_table_products_pricing . ' pp')
			->row();

		return ! empty($res->count);
	}

	protected function getProductsPricing() {
		return $this->db
			->get($this->_table_products_pricing)
			->result_array();
	}

	public function insertHistory($product_id, $field, $old_value, $new_value, $store_id = NULL, $user_id = NULL) {
		if (is_null($store_id))
			$store_id = $this->store_id;
		if (is_null($user_id))
			$user_id = $this->user_id;

		// store id might be 'all' in which case we need to look it up using product id
		if ( ! is_numeric($store_id)) {
			$products = $this->getProductsById($store_id, array($product_id));
			if (empty($products[0]['store_id']))
				return FALSE;

			$store_id = $products[0]['store_id'];
		}

		$data = array(
			'product_id' => (int)$product_id,
			'store_id' => (int)$store_id,
			'user_id' => (int)$user_id,
			'field' => $field,
			'old_value' => $old_value,
			'new_value' => $new_value,
			'created' => date('Y-m-d H:i:s')
		);

		return $this->db->insert($this->_table_products_history, $data);
	}

	// Search for / download / return a product image
	// return image name if exists
	// !! runs on production server, where the product images are !!
	function fetch_image($upc){
		$file = false;
		$img_path = $this->config->item('product_image_upload_path');
		$files = glob($img_path.$upc.'.*');
		if (count($files) > 0){
			$file = str_replace($img_path, '', $files[0]);
		}
		return $file;
	}
	
	//SHOW VARIABLES LIKE 'ft_min_word_len'
	//REPAIR TABLE products QUICK
	public function fulltext_search($txt, $limit=1){
		$txt = mysql_escape_string($txt);
		$sql = "
			SELECT *, MATCH(title,sku,search) AGAINST('{$txt}') AS score
			FROM products
			WHERE
				MATCH(title,sku,search) AGAINST('{$txt}')
			LIMIT {$limit}
		";
		return $this->db->query($sql)->result();
	}
	
	public function get_all_active_upcs(){
		$sql = "
			SELECT p.upc_code
			FROM {$this->_table_products} p
			JOIN {$this->_table_store} s ON (p.store_id = s.id)
			WHERE s.store_enable = '1'
			AND p.is_tracked = '1'
		";
		return $this->db->query($sql)->result();
	}
	
    /**
     * Get a row from the products table based on UPC code.
     * 
     * @author Christophe
     * @param string $upc
     * @param int $store_id
     * @return array
     */
    public function get_product_array_data_by_upc($upc, $store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('store_id', $store_id);
        $this->db->where('upc_code', $upc);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }	
	
    /**
     * Get notifications for a store for a date range.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $start_date
     * @param string $end_date
     */
    public function get_notifications_for_date_range($store_id, $start_date, $end_date)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notifications_history');
        $this->db->where('store_id', $store_id);
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->order_by('date', 'asc');
        
        $query = $this->db->get();
        
        return $query->result_array();        
    }
    
    /**
     * Get rows from products_trends_new table by product UPCs and date range.
     * 
     * @author Christophe
     * @param array|string $product_upcs
     * @param string $start
     * @param string $end
     * @return array
     */
    public function get_trend_data_by_upcs($product_upcs, $start, $end)
    {    
        $violation_query =
        'SELECT ptn.* ' .
        "FROM {$this->_table_products_trends} ptn " .
        "WHERE ptn.dt >= '" . $start . "' " .
        "AND ptn.dt <= '" . $end . "' " . 
        'AND ptn.upc IN ("' . implode('","', $product_upcs) . '")';
              
        //var_dump($violation_query); exit();
        
        return $this->db->query($violation_query)->result_array();
        
        /*
        if (is_array($product_upcs))
        {
            $product_upcs = implode(',', $product_upcs);
        }
        
        
        $this->db->select('*');
        $this->db->from('products_trends_new');
        $this->db->where_in('upc', $product_upcs);
        $this->db->where('dt >=', $start);
        $this->db->where('dt <=', $end);

        $this->db->order_by('id', 'desc');
        
        $query = $this->db->get();
        
        return $query->result_array(); 
        */       
    }
    
    /**
     * Get tracked and non-archived products
     * 
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_active_tracked_products_by_store($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('is_tracked', 1);
        $this->db->where('is_archived', 0);
        $this->db->where('store_id', $store_id);
        $this->db->order_by('id', 'desc');
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    /**
     * Get tracked and non-archived products
     *
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_active_tracked_product_upcs_by_store($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('upc_code');
        $this->db->from('products');
        $this->db->where('is_tracked', 1);
        $this->db->where('is_archived', 0);
        $this->db->where('store_id', $store_id);
        $this->db->order_by('id', 'desc');
        $this->db->distinct();
        
        $query = $this->db->get();
        
        return $query->result_array();
    }    
	
    /**
     * Get rows from product_violations_per_day by store and date range.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $start_date
     * @param string $end_date
     */
    public function get_violations_for_date_range($store_id, $start_date, $end_date)	
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('product_violations_per_day');
        $this->db->where('store_id', $store_id);
        $this->db->where('select_date >=', $start_date);
        $this->db->where('select_date <=', $end_date);
        $this->db->order_by('select_date', 'asc');
        
        $query = $this->db->get();
        
        return $query->result_array();        
    }
	
    /**
     * Get promotional pricing row by UUID.
     * 
     * @author Christophe
     * @param string $uuid
     * @return array
     */	
    public function get_promotional_pricing_by_uuid($uuid)
    {
        $this->db->select('*');
        $this->db->from('products_pricing_promotional');
        $this->db->where('uuid', $uuid);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }
	
    /**
     * Find overlapping promotional pricing periods for a product.
     * 
     * @author Christophe
     * @param int $product_id
     * @param string $start
     * @param string $end
     */
    public function get_overlapping_promotional_pricing($product_id, $start, $end)
    {
        $product_id = intval($product_id);
        
        $this->db->select('*');
        $this->db->from('products_pricing_promotional');
        
        $custom_query =
        "(" .
        "(period_end > '{$start}' AND period_start <= '{$start}') OR " .
        "(period_start >= '{$start}' AND period_start < '{$end}')" .
        ")";
        
        $this->db->where($custom_query);
        $this->db->where('product_id', $product_id);
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    /**
     * Insert new row into product_violations_per_day table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_day_violations($insert_data)
    {
        $this->db->insert('product_violations_per_day', $insert_data);
        
        return $this->db->insert_id();        
    }
	
    /**
     * Insert a new record into the products table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */	
    public function insert_product($insert_data)
    {
        $this->db->insert('products', $insert_data);
        
        return $this->db->insert_id();
    }    
    
    /**
     * Insert a new record into the products_pricing_promotional table.
     *
     * @author Christophe
     * @param array $insert_data
     * @return int
     */	
    public function insert_product_promotional_pricing($insert_data)
    {
        $this->db->insert('products_pricing_promotional', $insert_data);
        
        return $this->db->insert_id();        
    }
	
    /**
     * Update a single product row.
     *
     * @author Christophe
     * @param int $product_id
     * @param array $update_data
     */
    public function update_product($product_id, $update_data)
    {
        $product_id = intval($product_id);
        
        $this->db->where('id', $product_id);
        $this->db->update('products', $update_data);
    }	
    
    /**
     * Update record in the products_pricing_promotional table.
     * 
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_day_violations($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('product_violations_per_day', $update_data);        
    }

    /**
     * Delete row in products_pricing_promotional.
     * 
     * @author Christophe
     * @param int $id
     */
    public function delete_promotional_pricing($row_id)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->delete('products_pricing_promotional');
    }
    
    /**
     * Delete a row from the products_lookup table.
     * 
     * @author Christophe
     * @param int $id
     */
    public function delete_product_lookup($row_id)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->delete('products_lookup');        
    }
}
