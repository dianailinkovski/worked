<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Store_m extends MY_Model
{
	/**
	 * Get the Store Name from the store table by store id
	 *
	 * @param int $id
	 *
	 * @return String
	 */
	public function get_brand_by_store($store_id){
		return $this->_get_brand($store_id, 'store');
	}

	/**
	 * Get the Store Name from the store table by user id
	 *
	 * @param int $id
	 *
	 * @return String
	 */
	public function get_brand_by_user($user_id){
		return $this->_get_brand($user_id, 'users');
	}

	/**
	 * Get the Store Name from the store table by store id or user id
	 *
	 * @param int $id
	 * @param String $type
	 *
	 * @return String
	 */
	protected function _get_brand($id, $type = 'store'){
		if ($type === 'users') {
			$this->db
			->join($this->_table_users . ' u', 's.user_id=u.id')
			->where('s.user_id', (int)$id);
		}
		else {
			$this->db->where('s.id', (int)$id);
		}

		$ret = $this->db
		->select('s.store_name')
		->get($this->_table_store . ' s')
		->row();

		return $ret ? $ret->store_name : '';
	}

	function get_store_track($store_id){
		return $this->db
		->where('id', $store_id)
		->get($this->_table_store)
		->row();
	}

	function add_store($store_info=array()){
		$store_id = $this->insert($store_info);
		$this->_add_columns($store_id);

		return $store_id;
	}

	function get_results(){
		return $this->get_all();
	}

	function get_all_store_with_users(){
		return $this->db
		->select('s.id as store_id, s.store_name, s.brand_logo, u.user_name, u.id, u.email')
		->join($this->_table_users . ' u', 's.user_id = u.id', 'inner')
		->get($this->_table_store . ' s')
		->result_array();
	}
	
    /**
     * Get all enabled (active) stores from the database.
     * 
     * @author Christophe
     */	
    public function get_enabled_stores()
    {
        $this->db->select('*');
        $this->db->from('store');
        $this->db->where('store_enable', '1');
        
        $query = $this->db->get();
        
        return $query->result_array();        
    }	
	
	/**
	 * Get users_store row by user/store ID combo.
	 * 
	 * @author Christophe
	 * @param int $store_id
	 * @param int $user_id
	 * @return array
	 */
	function get_user_store_record($store_id, $user_id)
	{
	    $this->db->select('US.*');
	    $this->db->from('users_store US');
	    $this->db->where('US.user_id', $user_id);
	    $this->db->where('US.store_id', $store_id);
	     
	    $query = $this->db->get();
	     
	    return $query->row_array();	    
	}
	
	function get_users_by_store_id($store_id)
	{
	    $this->db->select('US.*');
	    $this->db->from('users_store US');
	    $this->db->where('US.store_id', $store_id);
	    
	    $query = $this->db->get();
	    
	    return $query->result_array();
	}

	function get_stores_by_userid($id){
		return $this->db
		->where('user_id', $id)
		->get($this->_table_store)
		->result();
	}

	function get_store_by_productid($id){
		return $this->db
		->select('s.id')
		->join($this->_table_products.' p', 'p.store_id=s.id')
		->where('p.id', $id)
		->order_by('id','desc')
		->limit(1)
		->get($this->_table_store.' s')
		->result();
	}
	
	function get_results_by_id($user_id){
		return $this->db->select('s.user_id, s.store_name, s.has_product, s.store_enable, s.created_at, s.tracked_at, s.last_violation_count, s.last_violation_product_count, s.man_id, s.brand_logo, us.store_id')
										->where('us.user_id', $user_id)
										->join($this->_table_users_store.' us', 's.id=us.store_id', 'left')
										->get($this->_table_store.' s')
										->result();
	}

	function get_store_name($store_id=0){
		$result = $this->get($store_id);
		$res = (array) $result;
		if($res)
			return $res['store_name'];
	}

	function get_store_url($store_id=0){
		$result = $this->get($store_id);
		$res = (array) $result;
		if($res)
			return $res['url'];
	}

	function get_store_info($store_id=0){
		$result = $this->get($store_id);
		return $res = (array) $result;
	}

	function update_store_info($store_info=array()){
		if($this->update_many($store_info['id'], $store_info))
			return true;
		else
			return false;
	}

	function check_store_edit($store_id, $store_name){
		$res = $this->db
		->where('store_name', $store_name)
		->where('id !=', $store_id)
		->get($this->_table_store);

		return $res->num_rows > 0;
	}


	function getMerchantItems($msid, $limit=array(), $search=''){
		$qry = '';
		$qry_1 = '';
		if(isset($limit[0]))
			$qry = "LIMIT ".$limit[1].", ".$limit[0]."";
		if($search!='')
			$qry_1 = " AND sku LIKE '%".$search."%'";
		$res = $this->db->query("SELECT * FROM " . $this->_table_products . " WHERE store_id={$msid} ".$qry_1." ORDER BY ID ASC ".$qry)->result('array');

		return $res;

	}

	function getMerchantItemsMerchant($store_id, $user_id, $search='', $offset = 0, $limit = 10, $sort_column='', $sort_type=''){
		if(isset($limit))
			$this->db->limit($limit, $offset);
		if( ! empty($sort_column))
			$this->db->order_by('mi.' . $sort_column, $sort_type === 'DESC' ? 'DESC' : 'ASC');
		else
			$this->db->order_by('mi.id', 'ASC');
		if( ! empty($search))
			$this->db->like('mi.sku', $search);

		$res = $this->db
		->select('mi.*')
		->join($this->_table_store . ' ms', 'mi.store_id=ms.id')
		->where('ms.user_id', $user_id)
		->get($this->_table_products . ' mi')
		->result_array();

		return $res;
	}
	function getMerchantItemsAllMerchant($store_id, $user_id, $search='', $sort_column='', $sort_type=''){
		if( ! empty($sort_column))
			$this->db->order_by('mi.' . $sort_column, $sort_type === 'DESC' ? 'DESC' : 'ASC');
		else
			$this->db->order_by('mi.id', 'ASC');
		if( ! empty($search))
			$this->db->like('mi.sku', $search);

		$res = $this->db
		->select('mi.*')
		->join($this->_table_store . ' ms', 'mi.store_id=ms.id')
		->where('ms.user_id', $user_id)
                ->where('ms.id', $store_id)
		->get($this->_table_products . ' mi')
		->result_array();

		return $res;
	}

	function getBrandCompetitorProducts($store_id, $keyWords = false, $owner = NULL){
		$result = array();

		if($keyWords){
			$this->db->like('mi.upc_code', $keyWords);
			$this->db->or_like('mi.title', $keyWords);
		}
		if (isset($owner)) $this->db->where('bp.owner', (boolean)$owner);

		$res = $this->db
		->select('mi.*')
		->join($this->_table_brand_product . ' bp', 'bp.product_id=mi.id')
		->where_in('bp.store_id', getStoreIdList($store_id))
		->get($this->_table_products . ' mi')
		->result_array();

		for($i=0, $n=sizeof($res); $i<$n; $i++){
			$ass = $this->db
			->select('p.id as owner_product_id, p.title as owner_product_title')
			->join($this->_table_brand_product_product.' bpp', 'bpp.owner_brand_product=p.id')
			->where('bpp.competitor_brand_product', (int)$res[$i]['id'])
			->get($this->_table_products.' p')
			->result_array();

			if(count($ass) == 1){
				$res[$i]['competing_product_id'] = $ass[0]['owner_product_id'];
				$res[$i]['competing_product_title'] = $ass[0]['owner_product_title'];
			}else{
				$res[$i]['competing_product_id'] = '';
				$res[$i]['competing_product_title'] = '';
			}

			array_push($result, $res[$i]);
		}

		return $result;
	}

	function getBrandPromotionalPricing($store_id, $type, $keywords = NULL) {
		if( ! empty($keywords)) {
			$like = array(
				'p.upc_code' => $keywords,
				'p.title' => $keywords
			);
			$this->db->or_like($like);
		}

		$res = $this->db
		->select('p.title, pp.*')
		->join($this->_table_products_pricing . ' pp', 'pp.product_id=p.id')
		->where_in('p.store_id', getStoreIdList($store_id))
		->where('pp.pricing_type', $type)
		->where('pp.pricing_end IS NOT NULL')
		->get($this->_table_products . ' p')
		->result_array();

		return $res;
	}

	function getBrandCompetitorUPCs($store_id)
	{
		$ret = array();

		$prods = $this->getBrandCompetitorProducts($store_id);
		if ($prods)
		{
			for ($i = 0, $n = count($prods); $i < $n; $i++)
			{
				$ret[$prods[$i]['upc_code']] = $prods[$i]['upc_code'];
			}
		}

		return $ret;
	}

	function getBrandCompetitorProductByProductId($store_id, $product_id, $owner = NULL)
	{
		if (isset($owner))
			$this->db->where('bp.owner', (boolean)$owner);

		$res = $this->db
		->select('bp.*')
		->where('bp.store_id', (int)$store_id)
		->where('bp.product_id', (int)$product_id)
		->get($this->_table_brand_product . ' bp')
		->row_array();

		return $res;
	}

	function getCompetitorMap($store_id, array $competitor_products = NULL)
	{
		if ( ! empty($competitor_products)) // only get these competitor products
			$this->db->where_in('bp.product_id', $competitor_products);

		$res = $this->db
		->select('bpp.*')
		->join($this->_table_brand_product . ' bp', 'bpp.competitor_brand_product=bp.product_id', 'left')
		->where_in('bp.store_id', getStoreIdList($store_id))
		->get($this->_table_brand_product_product . ' bpp')
		->result_array();

		return $res;
	}

	function getAssociatedProduct($storeId, $compId){
		$res = $this->db
		->select('bpp.*')
		->join($this->_table_brand_product . ' bp', 'bpp.competitor_brand_product=bp.product_id', 'left')
		->where('bp.store_id', (int)$storeId)
		->where('bpp.competitor_brand_product', (int)$compId)
		->get($this->_table_brand_product_product . ' bpp')
		->result_array();

		return $res;
	}


	function delete_store($store_id){
		$this->db->delete($this->_table_store, array('id' => $store_id));
		$this->db->delete($this->_table_products, array('store_id' => $store_id));
		$this->db->delete('products_trends', array('store_id' => $store_id));
		$this->db->where('store_id', $store_id);
		$dataGroups = $this->db->get($this->_table_groups)->result('array');
		for($i=0;$i<count($dataGroups);$i++){
			$this->db->where('group_id', $dataGroups[$i]['id']);
			$this->db->delete($this->_table_group_products);
		}
		$this->db->where('store_id', $store_id);
		$this->db->delete($this->_table_groups);
		if($store_id == $this->session->userdata('store_id'))
			$this->session->unset_userdata('user_store_id');
	}

	private function _add_columns($store_id){
		$cols = $this->db->select('id')
										->order_by('id', 'asc')
										->get($this->_table_columns)
										->result();

		$s = 0;
		foreach($cols as $col){
			$this->db->insert($this->_table_brand_columns, array('column_id' => $col->id, 'user_store_id' => $store_id, 'sort' => $s));
			$s++;
		}
	}

	function get_excluded_excluded_columns_by_store($msid){
		$res = $this->db
			->select('column_id')
										->distinct()
										->where_in('user_store_id', getStoreIdList($msid))
										->get($this->_table_brand_columns)
										->result();

		$brand_columns = array();
		
		if ( ! empty($res))
		{
			foreach ($res as $row)
			{
				$brand_columns[] = $row->column_id;
			}
		}

    $this->db->select('id, display_name, db_name');
		$this->db->from($this->_table_columns);
		
		if (!empty($brand_columns))
		{
		    $this->db->where_not_in('id', $brand_columns);
		}
		
		$query =$this->db->get();

		$rowData = $query->result();
			
		return $rowData;
	}

	function get_column_sort_order($msid, $column_id){
		$where = array(
			'user_store_id' => (int)$msid,
			'column_id' => (int)$column_id
		);
		$ret = $this->db
			->where($where)
			->get($this->_table_brand_columns)
			->row();

		return $ret;
	}

	function get_all_columns(){
		$ret = $this->db
		->select('id, display_name, db_name')
		->get($this->_table_columns)
		->result();

		return $ret;
	}

	function get_column_by_name($name){
		$ret = $this->db
			->where('db_name', $name)
			->get($this->_table_columns)
			->row();

		return $ret;
	}

	function get_columns_by_store($msid)
	{
		$rowData = $this->db
    		->select('c.id, c.display_name, c.db_name')
    		->distinct()
    		->join($this->_table_brand_columns . ' bc', 'bc.column_id=c.id', 'left')
    		->where_in('bc.user_store_id', getStoreIdList($msid))
    		->order_by('bc.sort', 'ASC')
    		->get($this->_table_columns . ' c')
    		->result();

		return $rowData;
	}

	function update_columns_order(array $order){
		foreach ($order as $col => $sort){
			$column = $this->get_column_by_name($col);
			$this->db
				->where('user_store_id', $this->store_id)
				->where('column_id', $column->id)
				->update($this->_table_brand_columns, array('sort' => (int)$sort));
		}
	}

	/**
	 * Get all the stores from the database.
	 * If enabled is TRUE only enabled stores are returned.
	 * If enabled is FALSE only disabled stores are returned.
	 * If enabled is NULL all stores are returned.
	 *
	 * @param boolean $enabled { default : TRUE }
	 * @return array
	 */
	public function get_stores($enabled = TRUE)
	{
		if ( ! is_null($enabled))
			$this->db->where('store_enable', '1'); //(int)$enabled

		return $this->db
			->get($this->_table_store)
			->result();
	}
	
	/**
	 * Return all records in stores table.
	 * 
	 * @author Christophe
	 */
	public function get_all_stores()
	{
	    $this->db->select('S.*');
	    $this->db->from('store S');
	    
	    $query = $this->db->get();
	    
	    return $query->result_array();	    
	}
	
	/**
	 * Get single store record.
	 * 
	 * @author Christophe
	 */
	public function get_store_by_id_array($store_id)
	{
	    $store_id = intval($store_id);
	    
	    $this->db->select('S.*');
	    $this->db->from('store S');
	    $this->db->where('S.id', $store_id);
	     
	    $query = $this->db->get();
	     
	    return $query->row_array();	    
	}
	
	/**
	 * Find all rows tied to a specific store.
	 * 
	 * @author Christophe
	 * @param int $store_id
	 * @return array
	 */
	public function get_store_members($store_id)
	{
	    $this->db->select('US.*');
	    $this->db->from('users_store US');
	    $this->db->where('store_id', $store_id); 
	    
	    $query = $this->db->get();
	     
	    return $query->result_array();	    
	}
	
	/**
	 * Note: there is a chance that in the future we may have stores with the same name
	 * DO NO USE - ONLY use with migration script
	 * 
	 * @param string $store_name
	 * @return array
	 */
	public function get_store_by_name_array($store_name)
	{
	    $this->db->select('S.*');
	    $this->db->from('store S');
	    $this->db->where('store_name', $store_name);
	     
	    $query = $this->db->get();
	     
	    return $query->row_array();	    
	}
	
	/**
	 * Get all store IDs that a user is linked to in users_store.
	 * 
	 * @author Christophe
	 * @param int $user_id
	 * @return array
	 */
	public function get_store_ids_for_user($user_id)
	{
	    $user_id = intval($user_id);
	    
	    $this->db->select('US.*');
	    $this->db->from('users_store US');
	    $this->db->where('US.user_id', $user_id);
	     
	    $query = $this->db->get();
	    
	    $results = $query->result_array();

	    if (empty($results))
	    {
	        return array();
	    }
	    else
	    {
	        $store_ids = array();
	        
	        foreach ($results as $result)
	        {
	            $store_ids[] = intval($result['store_id']);
	        }
	        
	        return $store_ids;
	    }
	}
	
	/**
	 * Get a users_store row.
	 * 
	 * @author Christophe
	 * @param int $user_id
	 * @param int $store_id
	 */
	public function get_user_store($user_id, $store_id)
	{
	    $user_id = intval($user_id);
	    $store_id = intval($store_id);
	     
	    $this->db->select('US.*');
	    $this->db->from('users_store US');
	    $this->db->where('US.user_id', $user_id);
	    $this->db->where('US.store_id', $store_id);
	    
	    $query = $this->db->get();
	     
	    return $query->row_array();	    
	}

	/**
	 * Add a violator_notifications row to the db
	 *
	 * @param array $data
	 * @return int/FALSE
	 */
	public function create_violator_notification(array $data) {
		$ret = $this->db->insert($this->_table_violator_notifications, $data);

		return $ret ? $this->db->insert_id() : FALSE;
	}

	/**
	 * Edit a violator_notifications row in the db
	 *
	 * @param int $id
	 * @param array $data
	 * @return boolean
	 */
	public function update_violator_notification($id, array $data) {
		return $this->db
		->where('id', (int)$id)
		->update($this->_table_violator_notifications, $data);
	}

	/**
	 * Remove a violator_notifications row from the db
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function delete_violator_notification($id) {
		return $this->db
		->where('id', (int)$id)
		->delete($this->_table_violator_notifications);
	}

	private function _get_violator_notification() {
		return $this->db
		->get($this->_table_violator_notifications)
		->row_array();
	}

	/**
	 * Lookup a single violator_notifications row by its ID
	 *
	 * @param int $id
	 * @return array/FALSE
	 */
	public function get_violator_notification_by_id($id) {
		$this->db->where('id', (int)$id);

		return $this->_get_violator_notification();
	}

	/**
	 * Lookup a single violator_notifications row by its merchant name id and store id
	 *
	 * @param int $merchant_name_id
	 * @param int $store_id
	 * @return array/FALSE
	 */
	public function get_violator_notification_by_seller($merchant_name_id, $store_id) {
		$this->db->where(array(
			'store_id' => (int)$store_id,
			'crowl_merchant_name_id' => (int)$merchant_name_id
		));

		if(($email = $this->_get_violator_notification())){
			$email['default'] = false;
			return $email;
		}else{
			//if we don't have a record, populate with defaults
			$this->db->where('store_id', (int)$store_id);

			//$email = $this->db->get($this->_table_violator_notification_settings)->row_array();
			$email = $this->db->get($this->_table_violator_notification_email_settings)->row_array();
			//hack to know when we're using defualt or existing record
			$email['default'] = true;
			$email['notification_type'] = '';

			return $email;
		}
	}

	/**
	 * Add a violator_notifications_history row to the db
	 *
	 * @param array $data
	 * @return int/FALSE
	 */
	public function create_violator_notifications_history(array $data) {
		$ret = $this->db->insert($this->_table_violator_notifications_history, $data);

		return $ret ? $this->db->insert_id() : FALSE;
	}

	private function _get_violator_notifications_history() {
		return $this->db
		->order_by('date', 'DESC')
		->get($this->_table_violator_notifications_history)
		->result_array();
	}

	/**
	 * Edit a violator_notifications_history row in the db
	 *
	 * @param int $id
	 * @param array $data
	 * @return boolean
	 */
	public function update_violator_notifications_history($id, array $data) {
		return $this->db
		->where('id', (int)$id)
		->update($this->_table_violator_notifications_history, $data);
	}
	
	/**
	 * Lookup a single violator_notifications_history row by its ID
	 *
	 * @param int $id
	 * @return array/FALSE
	 */
	public function get_violator_notifications_history_by_id($id) {
		$this->db->where('id', (int)$id);

		$res = $this->_get_violator_notifications_history();

		return empty($res[0]) ? FALSE : $res[0];
	}

	/**
	 * Lookup violator_notifications_history rows by merchant name id and store id
	 *
	 * @param int $merchant_name_id
	 * @param int $store_id
	 * @return array/FALSE
	 */
	public function get_violator_notifications_history_by_merchant_name_id($merchant_name_id, $store_id) {
		$this->db->where(array(
				'store_id' => (int)$store_id,
				'crowl_merchant_name_id' => (int)$merchant_name_id
			));

		return $this->_get_violator_notifications_history();
	}

	/**
	 * Add a violator_notifications_history row using the
	 * violator_notification data
	 *
	 * @param int $id
	 * @return int/FALSE
	 */
	public function record_violator_notification($id, $email_level, $regarding = NULL) {
		$vn = $this->get_violator_notification_by_id($id);
		if ( ! $vn)
			return FALSE;

		$fields = getTableFields($this->_table_violator_notifications_history, array('id'));

		$data = array();
		foreach ($fields as $field) {
			if (isset($vn[$field]))
				$data[$field] = $vn[$field];
		}
		$data['email_level'] = (int)$email_level;
		$data['regarding'] = $regarding;
		$data['date'] = date('Y-m-d H:i:s');

		return $this->create_violator_notifications_history($data);
	}

	// begin violation_streaks section
	
	// TODO: use the violation_streak table.  Currently it's unused.
	// TODO:  or, delete these functions and table entirely
	private function _get_violation_streak() {
		return $this->db
		->get($this->_table_violation_streaks)
		->row_array();
	}

	/**
	 * Lookup a single violation_streak row by its ID
	 *
	 * @param int $id
	 * @return array/FALSE
	 */
	public function get_violation_streak_by_id($id) {
		$this->db->where('id', (int)$id);

		return $this->_get_violation_streak();
	}

	/**
	 * Lookup a single violation_streak row by merchant name id and store id
	 *
	 * @param int $merchant_name_id
	 * @param int $store_id
	 * @return array/FALSE
	 */
	public function get_violation_streak_by_seller($merchant_name_id, $store_id) {
		$this->db->where(array(
				'store_id' => (int)$store_id,
				'crowl_merchant_name_id' => (int)$merchant_name_id
			));

		return $this->_get_violation_streak();
	}

	/**
	 * Record the date of the start of a violation streak in
	 * violation_streaks table or do nothing if a streak is
	 * already in progress
	 *
	 * @param int $merchant_name_id
	 * @param int $store_id
	 * @return boolean
	 */
	public function start_violation_streak($merchant_name_id, $store_id)
	{
		$streak = $this->get_violation_streak_by_seller($merchant_name_id, $store_id);
		if ( ! empty($streak['streak_start'])) // the streak has already started
			return TRUE;

		$ret = FALSE;

		$this->db->set('streak_start', date('Y-m-d H:i:s'));
		if ( ! empty($streak['id'])) {
			$ret = $this->db
			->where('id', $streak['id'])
			->update($this->_table_violation_streaks);
		}
		else {
			$ret = $this->db
			->set('store_id', (int)$store_id)
			->set('crowl_merchant_name_id', (int)$merchant_name_id)
			->insert($this->_table_violation_streaks);
		}

		return $ret;
	}

	/**
	 * End a violation streak by setting the date to NULL
	 *
	 * @param int $merchant_name_id
	 * @param int $store_id
	 * @return boolean
	 */
	// TODO:  add streak_end field to _table_violation_streaks?
	public function end_violation_streak($merchant_name_id, $store_id)
	{
		$streak = $this->get_violation_streak_by_seller($merchant_name_id, $store_id);
		if (empty($streak['id'])) // no streak has been started
			return TRUE;

		return $this->db
		->set('streak_start', NULL)
		->where('id', (int)$streak['id'])
		->update($this->_table_violation_streaks);
	}
	
	// end violation_streaks section

	/**
	 * Edit a store_smtp row in the db
	 *
	 * @param int $id
	 * @param array $data
	 * @return boolean
	 */
	public function set_store_smtp($store_id, array $data) {
		// Encrypt the password
		$this->load->library('encrypt');
		if (isset($data['password']))
			$data['password'] = $this->encrypt->encode($data['password']);

		// Check if the record exists and update it
		$smtp = $this->get_store_smtp_by_store($store_id);

		if ( ! empty($smtp['store_id']))
			return $this->db
			->where('store_id', (int)$store_id)
			->update($this->_table_store_smtp, $data);

		// The record doesn't exist so we need to insert it
		return $this->db->insert($this->_table_store_smtp, $data);
	}

	/**
	 * Remove a store_smtp row from the db
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function delete_store_smtp($store_id) {
		return $this->db
		->where('store_id', (int)$store_id)
		->delete($this->_table_store_smtp);
	}

	private function _get_store_smtp() {
		$row = $this->db
		->get($this->_table_violator_notification_email_settings)
		->row_array();

		// Decrypt the password
		$this->load->library('encrypt');
		if (isset($row['smtp_password']))
			$row['password'] = $this->encrypt->decode($row['smtp_password']);

		return $row;
	}

	/**
	 * Lookup a single store_smtp row by its ID
	 *
	 * @param int $id
	 * @return array/FALSE
	 */
	public function get_store_smtp_by_id($id) {
		$this->db->where('id', (int)$id);

		return $this->_get_store_smtp();
	}

	/**
	 * Lookup a single store row by its ID
	 *
	 * @param int $id
	 * @return array/FALSE
	 */
	public function get_store_by_id($id) {
		return $this->db
		->select()
		->mysql_cache()
		->where('id', (int)$id)
		->get($this->_table_store)
		->row();
	}
	
	/**
	 * Lookup a single store row by its ID
	 *
	 * @param int $id
	 * @return array/FALSE
	 */
	public function get_store_by_name($name) {
		return $this->db
		->select()
		->mysql_cache()
		->where('store_name', (string)$name)
		->get($this->_table_store)
		->row();
	}

	/**
	 * Lookup a single store_smtp row by its store id
	 *
	 * @param int $store_id
	 * @return array/FALSE
	 */
	public function get_store_smtp_by_store($store_id) {
		$this->db->where('store_id', (int)$store_id);

		return $this->_get_store_smtp();
	}

	/**
	 * Assume an email address from SMTP settings
	 *
	 * @param array $smtp
	 * @return String
	 */
	public function get_smtp_email(array $smtp) {
		if (empty($smtp['username']))
			return FALSE;

		if (valid_email($smtp['username']))
			return $smtp['username'];

		if (empty($smtp['host']))
			return FALSE;

		// The username is not an email address so we need to build it
		$domain = extract_domain($smtp['host']);
		$username = array_pop(explode('\\', $smtp['username']));

		return $username . '@' . $domain;
	}

	private function _get_violator_notification_settings($key, $value) {
		$row = $this->db
		->where($key, $value)
		//->get($this->_table_violator_notification_settings)
		->get($this->_table_violator_notification_email_settings)
		->row_array();

		return $row;
	}

	/**
	 * Lookup a single violator_notification_settings row by its ID
	 *
	 * @param int $id
	 * @return array/FALSE
	 */
	public function get_violator_notification_settings_by_id($id) {
		return $this->_get_violator_notification_settings('id', (int)$id);
	}

	/**
	 * Lookup a single violator_notification_settings row by its store id
	 *
	 * @param int $id
	 * @return array/FALSE
	 */
	public function get_violator_notification_settings_by_store($store_id) {
		return $this->_get_violator_notification_settings('store_id', (int)$store_id);
	}

	/** 
	* Get smtp with notification level settings
	*
	* @param int $store_id
	* @return array/FALSE
	*/
	public function get_violator_notification_email_settings($store_id) {
		$row = $this->db
			   ->mysql_cache()
			   ->where('store_id', $store_id)
			   ->get($this->_table_violator_notification_email_settings)
			   ->row_array();

		return $row;
	}

	/**
	* Get email template for current level 
	*
	* @param int $email_settings_id
	* @param int $notification_level
	* @return array/FALSE
	*/
	public function get_violator_email_template($email_settings_id, $notification_level) {
		$row = $this->db
			   ->mysql_cache()
			   ->where('email_settings_id', $email_settings_id)
			   ->where('notification_level', $notification_level)
			   ->get($this->_table_violator_notification_email_templates)
			   ->row_array();

		return $row;
	}

	public function tracked_at($store_id){
		$this->db
			->where('id', (int)$store_id)
			->update($this->_table_store, array('tracked_at' => date('Y-m-d H:i:s')));
	}

	
	public function get_categories_by_storeId($storeId){
        $sql = ('SELECT c.name FROM categories c, categories_stores cs WHERE c.id = cs.cId AND cs.sId = '.$storeId);
        $cat_array = $this->db->query($sql)->result_array();
		return $cat_array;
	}
	
    /**
     * Insert a new row into the store table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_store($insert_data)
    {
        $this->db->insert('store', $insert_data);
        
        return $this->db->insert_id();        
    }
    
    /**
     * Update a single row in the store table.
     * 
     * @author Christophe
     * @param int $store_id
     * @param array $update_data
     */
    public function update_store($store_id, $update_data)
    {
        $store_id = intval($store_id);
        
        $this->db->where('id', $store_id);
        $this->db->update('store', $update_data);        
    }
}
