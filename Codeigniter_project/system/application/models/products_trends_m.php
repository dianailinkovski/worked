<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This accesses the products_trends table, which will be pretty huge, with millions of records.
 * This "noSql" data is redundant (ie, you can find it in other tables).
 * 	This is purposely so that we do not need to do any table joins, in order to speed access.
 * The table must be carefully indexed (um, dt), and carefully selected
 * Scalability Optimizations:
 * - mysql partitioning by id (hash)
 * - fixed length columns
 * - mysql cache
 * - no table joins on this table!
 */

class Products_trends_m extends MY_Model
{
	/**
	 * Insert a products_trends record 
	 *
	 * @param array (or object)$crawled_product . 1D array
	 * @param str $marketplace
	 * @return obj Active Record or FALSE 
	 */
	public function insertData($crawled_product, $marketplace)
	{
		$p = (object)$crawled_product;
		
		// TODO: put proper error handling here
		if(empty($p->um)){
			echo "empty hashkey \n"; 
			return false;
		}
		if(empty($marketplace)){
			$marketplace = !empty($p->ar) ? $p->ar : '';
		}
		if(empty($marketplace)){
			echo "empty marketplace \n"; 
			return false;
		}
			
		$ret = array(
			'um'  => 				   (string)$p->um,				//hash_key
			'dt'  => !empty($p->dt)  ? (int)$p->dt : '',			//timestamp
			'ap'  => !empty($p->ap)  ? (float)$p->ap : '',			//MAP
			'ar'  => 				   (string)$marketplace,		//marketplace
			'il'  => !empty($p->il)  ? (string)$p->il : '',			//product_image
			'l'   => !empty($p->l)   ? (string)$p->l : '',			//violation_url
			'mil' => !empty($p->mil) ? (string)$p->mil : '',		//merchant_logo
			'mpo' => !empty($p->mpo) ? (float)$p->mpo : 0,			//merchant_price
			'msp' => !empty($p->msp) ? (float)$p->msp : 0,			//merchant_shipping_price
			'mu'  => !empty($p->mu)  ? (string)$p->mu : '',			//merchant_url  // TODO: necessary data?
			't'   => !empty($p->t)   ? (string)$p->t : '',			//product_title
			'pid' => !empty($p->pid) ? (int)$p->pid : 0,			//product_id
			'upc' => !empty($p->upc) ? (string)$p->upc : '',		//upc_code
			'mid' => !empty($p->mid) ? (int)$p->mid : '',			//merchant_id // TODO: put this here to remove lookups from ?
			'ss'  => !empty($p->ss)  ? (string)$p->ss : '',			//screenshot  // TODO: put this here or not (ie : '', put in violations table)?
			'rp'  => !empty($p->rp)  ? (float)$p->rp : 0,			// retail price
			'wp'  => !empty($p->wp)  ? (float)$p->wp : 0			// wholesale price
		);
		//echo "products_trends->insertData:\n";
		//print_r($ret); 
		return $this->db->insert($this->_dynamo_products_trends, $ret, false, true);
	}
	
	/**
	 * Retrieve a products_trends record by its Hash Key and Date Range
	 *
	 * @param str $hashkey
	 * @param int $start_date - beginning date of range
	 * @param int $end_date - ending date of range
	 * @param int $limit default false
	 * @return obj Active Record
	 */
	public function get_by_hashkey_and_date_range($hashkey, $start_date, $end_date, $limit=false, $orderBy=false)
	{
		$this->_and_date_range($start_date, $end_date);
		$crawled_product = $this->_get($hashkey, 'hashkey', $limit, $orderBy);
		return $crawled_product;
	}

	/**
	 * Retrieve a products_trends record by its Hash Key and Date Range
	 *
	 * @param str $hashkey
	 * @param int $start_date - beginning date of range
	 * @param int $end_date - ending date of range
	 * @param array $marketFilter - list of acceptable markets
	 * @param int $limit default false
	 * @return obj Active Record
	 */
	public function get_by_hashkey_and_date_range_and_marketplace($hashkey, $start_date, $end_date, $marketFilter='', $limit=false, $orderBy=false)
	{
		$this->_and_date_range($start_date, $end_date);
		$this->_and_market_filter($marketFilter);
		$crawled_product = $this->_get($hashkey, 'hashkey', $limit, $orderBy);
		return $crawled_product;
	}

	public function get_all_by_hashkeys_and_date_range($hashes, $from, $to, $limit = '1000', $orderBy='t')
	{
		$this->_and_date_range($from, $to);
		$crawled_product = $this->_get($hashes, 'hashArray', $limit, $orderBy);
		//_get($key, $type = 'hashkey', $limit=false, $orderBy=false)
		return $crawled_product;
	}
	
	public function get_latest_whack_prices(){
		$sql = "
			SELECT ptn.*, (ptn.mpo/ptn.ap)*100 as percent, p.title, p.sku, s.store_name
			FROM products_trends_new ptn
				JOIN products p ON (p.id = ptn.pid)
				JOIN store s ON (s.id = p.store_id)
			WHERE ptn.dt > (unix_timestamp()-(24*60*60))
			AND (ptn.mpo/ptn.ap) < 0.3
			GROUP BY ptn.um
			ORDER BY s.id, percent
		";
		return $this->db->query($sql)->result_object();
	}
	
	/**
	 * Retrieve the latest products_trends record by its Hash Key
	 *
	 * @param int $hashkey
	 * @return obj Active Record
	 */
	public function get_latest_by_hashkey($hashkey){
		return $this->get_by_hashkey($hashkey, 1, 'dt DESC');
	}
	
	/**
	 * Helper function to set a date range for the current query
	 * @param int $start_date - beginning date of range
	 * @param int $end_date - ending date of range
	 * @return null
	 */
	private function _and_date_range($start_date, $end_date)
	{
		$dateRange = "`dt` BETWEEN $start_date and $end_date";
		$this->db->where($dateRange, NULL, FALSE);
	}
	
	/**
	 * Helper function to set a date range for the current query
	 * @param string $marketFilter - sql format string list of acceptable markets. Example: "IN ('foo', 'bar')"
	 * @return null
	 */
	private function _and_market_filter($marketFilter)
	{
		if(empty($marketFilter)){
			return;
		}
		$mf = "`ar` {$marketFilter}";
		$this->db->where($mf, NULL, FALSE);
	}
	    
    /**
     * 
     * @author Christophe
     * @param unknown_type $merchant_id
     * @param unknown_type $store_id
     * @return array
     */
    public function get_last_violation($merchant_id, $store_id)
    {
        $query_str = "
            SELECT ptn.*
            FROM products_trends_new ptn
            JOIN products p ON p.upc_code = ptn.upc
            WHERE p.store_id = {$store_id}
            AND ptn.mid = {$merchant_id}
            AND ptn.mpo < ptn.ap
            ORDER BY ptn.dt DESC
            LIMIT 1
        ";
        
        return $this->db->query($query_str)->row_array();        
    }
	
	/**
	 * Retrieve a products_trends record by its lookup UPC code
	 * @param String $upc_code
	 * @param int $limit default false
	 * @return obj Active Record
	 */
	public function get_by_upc_code($upc_code, $limit=false, $orderBy=false) {
		return $this->_get($upc_code, 'upc_code', $limit, $orderBy);
	}

	/**
	 * Retrieve a products_trends record by its Hash Key
	 *
	 * @param int $hashkey
	 * @param int $limit default false
	 * @return obj Active Record
	 */
	public function get_by_hashkey($hashkey, $limit=false, $orderBy=false) {
		return $this->_get($hashkey, 'hashkey', $limit, $orderBy);
	}

	/**
	 * Retrieve a products_trends record by its product id
	 *
	 * @param String $product_id
	 * @param int $limit default false
	 * @return obj Active Record
	 */
	public function get_by_product_id($product_id, $limit=false, $orderBy=false) {
		return $this->_get($product_id, 'product_id', $limit, $orderBy);
	}

	/**
	 * Get a products_trends record by an index
	 *
	 * @param String $key
	 * @param String $type { default : 'hashkey' }
	 * @return obj Active Record
	 */
	protected function _get($key, $type = 'hashkey', $limit=false, $orderBy=false) 
	{
		if (empty($key))
		{
			// echo "Key $type is empty \n"; // commented out as I don't think it should be shown to users - Christophe 11/4/2015
			return false;
		}
		
		switch ($type) 
		{
			case 'upc_code':
				$this->db->where('upc', $key);
				break;
			case 'product_id':
				$this->db->where('pid', $key);
				break;
			case 'hashArray':
				$this->db->where_in('um', $key);
				break;
			case 'hashkey':
			default:
				$this->db->where('um', $key);
		}
		
		if($limit)
			$this->db->limit(intval($limit));
			
		if($orderBy)
			$this->db->orderBy((string)$orderBy);
		
		//$this->db->mysql_cache(); // only on production server?
		return $this->db->get($this->_table_products_trends, null, null, false);
	}

	public function get_images($upc){
		$sql = "
			SELECT distinct SUBSTRING_INDEX(il, '?', 1) as img
			FROM products_trends_new 
			where upc = '{$upc}'
			and il like 'http:%'
			and (il like '%.jpg' || il like '%.jpeg')
			and il not like '%thumb%'
			and il not like '%110_%'
			and il not like '%_sm.%'
			and il not like '%error%'
		";
		return $this->db->query($sql)->result_object();
	}
	
	// used to populate products.search fulltext
	public function get_concat_title_by_upc($upc){
		$this->db->query("SET SESSION group_concat_max_len = 1000000");
		$sql = "
			SELECT pid, GROUP_CONCAT(t) AS seeds FROM products_trends_new 
			where um like '%#{$upc}'
			and id > (
				select 
				cast(max(id) - (max(id) / 8) as UNSIGNED) as ptn_8
				from products_trends_new
			)
			LIMIT 1
		"; // always only 1
		$res = $this->db->query($sql)->result_object();
		$res = $res[0];
		$seeds = $res->seeds;
		return $seeds;
	}
}
/*
-- run this then figure out why rp and wp are 0

update products_trends_new ptn
join products p on (ptn.pid = p.id)
set ptn.wp = p.wholesale_price
where 
ptn.dt > UNIX_TIMESTAMP()-(31*24*60*60)
and ptn.dt < UNIX_TIMESTAMP()-(24*24*60*60)
and ptn.wp=0 
and p.wholesale_price > 0;

update products_trends_new ptn
join products p on (ptn.pid = p.id)
set ptn.rp = p.retail_price
where 
ptn.dt > UNIX_TIMESTAMP()-(24*24*60*60)
and ptn.dt < UNIX_TIMESTAMP()-(*24*60*60)
and ptn.rp=0
and p.retail_price > 0;

SELECT count(*) FROM `test_mv2`.`products_trends_new` where rp=0 and dt > UNIX_TIMESTAMP()-(30*24*60*60);
--SELECT count(*) FROM `test_mv2`.`products_trends_new` where wp=0 and dt > UNIX_TIMESTAMP()-(15*24*60*60);

*/

?>