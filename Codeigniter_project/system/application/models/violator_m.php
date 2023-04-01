<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Violator_m extends MY_Model {

	public $crawlStart;
	public $crawlEnd;

	function Violator_m() {
		parent::MY_Model();
		$this->ci->load->model('products_trends_m', 'ProductsTrends');

		if ( ! isset($this->ci->Crawl_data))
			$this->ci->load->model('crawl_data_m', 'Crawl_data');

		$this->crawlStart = $this->crawlEnd = time();

		// TODO:  This is totally wrong.  It should be per marketplace, otherwise it will return whatever the last data is, using the default "all" param
		// might be better to fetch ->last_crawl() first, then deallocate the marketplace name index as needed.
		// maybe check for URL/router arguments here and set crawl range for this object now.
		
		if ($this->config->item('environment') == 'local')
		{
		    $this->crawlStart = '2015-08-01 00:00:00';
		    $this->crawlEnd = '2015-08-02 00:00:00';
		}
		else
		{
		    $range = $this->ci->Crawl_data->last_crawl_range();
		    
		    $this->crawlStart = $range['from'];
		    $this->crawlEnd = $range['to'];
		}
	}

	function getViolatedProducts($store_id) 
	{
		$sql = "SELECT p.*
				FROM {$this->_table_crowl_product_list} cpl
				LEFT JOIN {$this->_table_products} p ON p.upc_code = cpl.upc
				WHERE cpl.last_date >= ? AND cpl.last_date <= ? AND p.store_id IN (" . getStoreIdList($store_id, TRUE) . ") AND cpl.violated=1
				GROUP BY cpl.upc";
		
		$params = array(
			strtotime($this->crawlStart),
			strtotime($this->crawlEnd)
		);
		
		//var_dump($params); exit();
		
		//var_dump($this->crawlStart);
		//var_dump($this->crawlEnd); exit();
		
		$result = $this->db->query($sql, $params);
		
		

		return $result->result_array();
	}

	function countViolatedProducts($store_id) {
		$sql = "SELECT count(distinct(cpl.upc)) as count
				FROM {$this->_table_crowl_product_list} cpl
				LEFT JOIN {$this->_table_products} p ON p.upc_code = cpl.upc
				WHERE cpl.last_date >= ? AND cpl.last_date <= ? AND p.store_id IN (" . getStoreIdList($store_id, TRUE) . ") AND cpl.violated=1";
		$params = array(
			strtotime($this->crawlStart),
			strtotime($this->crawlEnd),
			(int)$store_id
		);
		$count = $this->db
		->query($sql, $params)
		->row();

		return $count ? $count->count : 0;
	}

	// @TODO - need to consolidate similar functions?? ^^countViolatedProducts^^ ??
	function _countPriceViolations() {
		$violators = $this->lastCrawlViolators($this->ci->store_id);
		if ( ! empty($violators)) {
			foreach ($violators['violators'] as $id => $violations) {
				$violators['violators'][$id] = array(
					//'crowl_merchant' => $crowl_merchant,
					'total_violations' => isset($violations['products']) ? $violations['products'] : 0,
					'total_products' => isset($violations['violations']) ? $violations['violations'] : 0
				);
			}
		}

		return $violators['violators'];
	}

	/**
	 * 
	 * THis is retarded!
	 * last_date is invalidated during a new crawl, it's unreliable data.
	 * 
	 * @author unknown, Chris
	 * @param unknown_type $storeId
	 * @param unknown_type $append
	 * @param unknown_type $marketplace
	 * @param unknown_type $startTime
	 * @param unknown_type $endTime
	 * @return multitype:number
	 */
	function getViolatedMarkets($storeId, $append = '_violations', $marketplace = 'all', $startTime = NULL, $endTime = NULL) 
	{
		$ret = array();
		
		$markets = ($marketplace == 'all') ? array_keys(get_market_lookup(TRUE)) : array($marketplace);

		$sql = "
		    SELECT COUNT(cpl.id) as count
				FROM {$this->_table_crowl_product_list} cpl
				LEFT JOIN {$this->_table_products} p ON p.upc_code = cpl.upc
				WHERE cpl.last_date >= ? 
		    AND cpl.last_date <= ? 
		    AND p.store_id IN (" . getStoreIdList($storeId, TRUE) . ") 
		    AND cpl.violated = 1 
		    AND cpl.marketplace = ?
		";

		$start = ($startTime) ? strtotime($startTime) : strtotime($this->crawlStart);
		$end = ($endTime) ? strtotime($endTime) : strtotime($this->crawlEnd);
		
		foreach ($markets as $market) 
		{
			$params = array(
				$start,
				$end,
				$market
			);
			
			$count = $this->db->query($sql, $params)->row();
			
			//echo $this->db->last_query()."\n";
			
			$ret[$market . $append] = $count ? (int)$count->count : 0;
		}

		return $ret;
	}
	
	/**
	 * Used with Reportinfo.php - report_overview()
	 * 
	 * @author Christophe
	 * @param unknown_type $storeId
	 * @param unknown_type $append
	 * @param unknown_type $marketplace
	 * @return multitype:number
	 */
	function getViolatedMarketsNoDateRange($storeId, $append = '_violations', $marketplace = 'all')
	{
		$ret = array();
	
		$markets = ($marketplace == 'all') ? array_keys(get_market_lookup(TRUE)) : array($marketplace);
	
		$sql = "
    		SELECT COUNT(cpl.id) as count
    		FROM {$this->_table_crowl_product_list} cpl
    		LEFT JOIN {$this->_table_products} p ON p.upc_code = cpl.upc
    		WHERE
    		p.store_id IN (" . getStoreIdList($storeId, TRUE) . ")
    		AND cpl.violated = 1
    		AND cpl.marketplace = ?
		";
	
    foreach ($markets as $market)
		{
			$params = array($market);
				
			$count = $this->db->query($sql, $params)->row();
				
			//echo $this->db->last_query()."\n";
				
			$ret[$market . $append] = $count ? (int)$count->count : 0;
		}
	
	  return $ret;
	}	

	function getViolatedMarketsProducts($storeId, $market) {
		$market = strtolower($market);
		$range = $this->Crawl_data->last_crawl_range($market);
		$this->crawlStart = $range['from'];
		$this->crawlEnd = $range['to'];
		$from = strtotime($this->crawlStart);
		$to   = strtotime($this->crawlEnd);

		//
		// Get the violated product information from crowl_product_list
		//
		$productQuery = "
			SELECT SQL_CACHE
				concat(cmn.seller_id,'#',cpl.upc) as hashKey,
				p.title,
				cmn.original_name
			FROM
				{$this->_table_crowl_product_list} cpl
			LEFT JOIN
				{$this->_table_crowl_merchant_name} cmn ON cmn.id = cpl.merchant_name_id
			LEFT JOIN
				{$this->_table_products} p ON p.upc_code = cpl.upc
			WHERE
				p.store_id IN (" . getStoreIdList($storeId, TRUE) . ") AND cpl.marketplace='$market' AND cpl.violated=1
				AND cpl.last_date >='".$from."' AND cpl.last_date<='".$to."'
			GROUP BY hashKey";
		//$productQuery.=" LIMIT 15";
		//echo "<pre>$productQuery";exit;
		$products = $this->db->query($productQuery)->result();
		
		echo $this->db->last_query(); exit;

		// get the "nosql" data
		$finalProductsArray = $hashes = array();
		foreach($products as $product){
			$hashes[] = $product->hashKey;
			$products_assoc[$product->hashKey] = $product;
			//continue;
		}
			
		$priceTrends = $this->ProductsTrends->get_all_by_hashkeys_and_date_range($hashes, $from, $to);
		
		foreach($priceTrends->result_object() as $priceTrend){
		//foreach($priceTrends as $priceTrend){
			//safety hack to not show incorrect violations
			if((float)$priceTrend->mpo >= (float)$priceTrend->ap)
				continue;
			
			if ((string)$priceTrend->ar !== $market)
				continue;
			
			list($seller_id, $upc) = explode('#', $priceTrend->um);
			$violationArray = array(
				'productId' 	=> (int)   $priceTrend->pid,
				'upc_code' 		=> (string)$priceTrend->upc,
				'retail' 		=> (float) $priceTrend->rp,
				'wholesale' 	=> (float) $priceTrend->wp,
				'price' 		=> (float) $priceTrend->mpo,
				'map' 			=> (float) $priceTrend->ap,
				'title' 		=> (string)$priceTrend->t,
				'title2' 		=> (string)$products_assoc[$priceTrend->um]->title, //$priceTrend->t
				'marketplace' 	=> (string)$priceTrend->ar,
				'url' 			=> (string)$priceTrend->l,
				'timestamp'		=> (int)   $priceTrend->dt,
				'hash_key'		=> (string)$priceTrend->um,
				'merchant_id' 	=> (string)$seller_id,
				'original_name' => (string)$products_assoc[$priceTrend->um]->original_name,
				'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend->dt),
				'shot' 			=> (string)$priceTrend->ss
			);
			$finalProductsArray[] = $violationArray;
		}
//print_r($finalProductsArray); exit;
		return $finalProductsArray;
	}
	
    /**
     * Get a row from the violator_notifications table by store ID and merchant ID.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_id
     * @return array
     */
    public function get_violator_notification_by_store_merchant($store_id, $merchant_id)
    {
        $store_id = intval($store_id);
        $merchant_id = intval($merchant_id);
           
        $this->db->select('*');
        $this->db->from('violator_notifications');
        $this->db->where('store_id', $store_id);
        $this->db->where('crowl_merchant_name_id', $merchant_id);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }	
    
    /**
     * Find all violations for all retailers.
     * 
     * @author Christophe
     * @param int $storeId
     * @return array
     */	
    function get_retailer_violations_by_store($storeId) 
    {
        $storeId = intval($storeId);
        
        //var_dump($this->crawlStart);
        //var_dump($this->crawlEnd);
        //exit();
        
        $from = strtotime($this->crawlStart);
        $to   = strtotime($this->crawlEnd);
        
        /*
        $violation_query =
        'SELECT ptn.* ' .
        "FROM {$this->_table_products_trends} ptn " .
        "LEFT JOIN {$this->_table_products} p ON p.upc_code = ptn.upc " .
        "WHERE ptn.dt >= '" . $from . "' " .
        "AND ptn.dt <= '" . $to . "' " . 
        "AND p.store_id IN ($storeId)";
        
        $violations = $this->db->query($violation_query)->result_array();
        
        var_dump($this->db->last_query());
        
        var_dump('here');
        var_dump(count($violations)); exit();
        
        
        
        $final_array = array();
        
        foreach ($violations as $violation)
        {
            var_dump($violation); exit();
            
            if ((float)$priceTrend->mpo >= (float)$priceTrend->ap)
            	continue;            
        }
     
        return $final_array;
        */
        
        // from getViolatedMarketsProducts():
        $productQuery = "
        	SELECT SQL_CACHE
        		concat(cmn.seller_id,'#',cpl.upc) as hashKey,
        		p.title,
        		cmn.original_name,
        		cmn.id as cmn_id
        	FROM {$this->_table_crowl_product_list} cpl
        	LEFT JOIN {$this->_table_crowl_merchant_name} cmn ON cmn.id = cpl.merchant_name_id
        	LEFT JOIN {$this->_table_products} p ON p.upc_code = cpl.upc
        	LEFT JOIN marketplaces m ON m.name = cmn.marketplace
        	WHERE p.store_id IN (" . getStoreIdList($storeId, TRUE) . ") 
        	AND m.is_retailer = 1
        	AND m.is_active = 1                
        	AND cpl.violated = 1
          AND cpl.last_date >= {$from} 
          AND cpl.last_date <= {$to}
        	GROUP BY hashKey
        ";         

//         $productQuery = "
//         SELECT SQL_CACHE
//         concat('#',cpl.upc) as hashKey,
//         p.title
//         FROM {$this->_table_crowl_product_list} cpl
//         LEFT JOIN {$this->_table_products} p ON p.upc_code = cpl.upc
//         WHERE
//         p.store_id IN (" . getStoreIdList($storeId, TRUE) . ") AND cpl.violated = 1
//         AND cpl.last_date >='".$from."' AND cpl.last_date<='".$to."'
//         GROUP BY hashKey";

        $products = $this->db->query($productQuery)->result();
        
        //echo $this->db->last_query(); exit();
        
        $finalProductsArray = array();
        $hashes = array();
        
        foreach ($products as $product)
        {
            $hashes[] = $product->hashKey;
            
            $products_assoc[$product->hashKey] = $product;
        }
        	
        $priceTrends = $this->ProductsTrends->get_all_by_hashkeys_and_date_range($hashes, $from, $to);
        
        //var_dump($priceTrends); exit();
        //var_dump(count($priceTrends)); exit();
        
        if ($priceTrends != FALSE)
        {       
            foreach ($priceTrends->result_object() as $priceTrend)
            {
                // safety hack to not show incorrect violations
                if ((float)$priceTrend->mpo >= (float)$priceTrend->ap)
            	      continue;
            			
            		list($seller_id, $upc) = explode('#', $priceTrend->um);
            		
            		$violationArray = array(
            				'productId' 	=> (int)$priceTrend->pid,
            				'upc_code' 		=> (string)$priceTrend->upc,
            				'retail' 		=> (float) $priceTrend->rp,
            				'wholesale' 	=> (float) $priceTrend->wp,
            				'price' 		=> (float) $priceTrend->mpo,
            				'map' 			=> (float) $priceTrend->ap,
            				'title' 		=> (string)$priceTrend->t,
            				'title2' 		=> (string)$products_assoc[$priceTrend->um]->title, //$priceTrend->t
            				'marketplace' 	=> (string)$priceTrend->ar,
                    'url' 			=> (string)$priceTrend->l,
                    'timestamp'		=> (int)   $priceTrend->dt,
                    'hash_key'		=> (string)$priceTrend->um,
                    'merchant_id' 	=> (string)$seller_id,
            		    'cmn_id' => (int)$products_assoc[$priceTrend->um]->cmn_id,
                    'original_name' => (string)$products_assoc[$priceTrend->um]->original_name,
            				'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend->dt),
            				'shot' 			=> (string)$priceTrend->ss
            		);
            		
            		$finalProductsArray[] = $violationArray;
            }
        }
        
        //var_dump(count($finalProductsArray)); exit();
        
        return $finalProductsArray;         
    }	

	/**
	 * Get violations of the last crawl by seller and store
	 *
	 * @param int $storeId
	 * @param int $crowlMerchantNameId
	 */
	public function getSellerViolations($storeId, $crowlMerchantNameId, $start, $end) {
		$productQuery = "
			SELECT
				concat(cmn.seller_id,'#',cpl.upc) as hashKey,
				cmn.seller_id,
				p.title,
				p.upc_code,
				p.id,
				p.price_floor,
				p.retail_price,
				p.wholesale_price,
				cmn.merchant_name,
				cmn.original_name,
				cpl.last_date,
                cpl.id as cplid,
                cpl.marketplace
			FROM
				{$this->_table_crowl_product_list} cpl
			LEFT JOIN {$this->_table_crowl_merchant_name} cmn ON cmn.id = cpl.merchant_name_id
			LEFT JOIN {$this->_table_products} p ON p.upc_code = cpl.upc
			WHERE p.store_id IN (" . getStoreIdList($storeId, TRUE) . ")
				AND cmn.id=".$crowlMerchantNameId."
				AND cpl.violated=1
				AND cpl.last_date >= $start
				AND cpl.last_date <= $end
			GROUP BY hashKey
			ORDER BY cpl.last_date DESC";
//echo "$productQuery\n"; exit;
		return $this->db->query($productQuery)->result();
	}

	/**
	 * 
	 * @author unknown
	 * @param int $storeId
	 * @param array $merchantInfo
	 * @return array
	 */
	public function getViolatorReport($storeId, $merchantInfo) 
	{
	  /*
		// TODO: make this marketplace modification for all Violator_m class functions
		$range = $this->ci->Crawl_data->last_crawl_range($merchantInfo['marketplace']);

		//var_dump($range); exit(); 

		$this->crawlStart = $range['from'];
		$this->crawlEnd = $range['to'];
		*/
	    
		$start = strtotime($this->crawlStart);
		$end = strtotime($this->crawlEnd);
		
		// now setup the violation data
		$products = $this->getSellerViolations($storeId, $merchantInfo['id'], $start, $end);
		
		$finalProductsArray = array();
		
		foreach ($products as $product)
		{		
			//TODO: store these two data inside the product_trends table instead
			//$retailPricePoint 	 = getPricePoint($product->upc_code, $storeId, 'retail_price');
			//$wholesalePricePoint = getPricePoint($product->upc_code, $storeId, 'wholesale_price');
			
			// get the "nosql" data
			$priceTrends = $this->ProductsTrends->get_by_hashkey_and_date_range($product->hashKey, $start, $end);
			
		
			foreach($priceTrends->result_object() as $priceTrend)
			{			
				// safety hack to not show incorrect violations
				if ((float)$priceTrend->mpo >= (float)$priceTrend->ap)
				{
					continue;
				}
				
				$violationArray = array(
					'productId' 	=> (int)   $product->id,
					'upc_code' 		=> (string)$product->upc_code,
					'retail' 		=> (float) $priceTrend->rp,
					'wholesale' 	=> (float) $priceTrend->wp,
					'price' 		=> (float) $priceTrend->mpo,
					'map' 			=> (float) $priceTrend->ap,
					'title' 		=> (string)$product->title, //$priceTrend->t
					'marketplace' 	=> (string)$priceTrend->ar,
					'url' 			=> (string)$priceTrend->l,
					'timestamp'		=> (int)   $priceTrend->dt,
					'hash_key'		=> (string)$product->hashKey,
					'merchant_id' 	=> (string)$product->seller_id,
					'original_name' => (string)$product->original_name,
					'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend->dt),
					'shot' 			=> (string)$priceTrend->ss
				);
				
				$finalProductsArray[] = $violationArray;
			}
		}
		
		return $finalProductsArray;
	}

	function getProductViolations($storeId, $productId) 
	{
		//now setup the violation data
		$productQuery = "
    		SELECT 
    		    concat(cmn.seller_id,'#',cpl.upc) as hashKey, 
    		    cmn.seller_id,
    		    p.title,
    		    cmn.original_name
    		FROM {$this->_table_crowl_product_list} cpl
    		LEFT JOIN {$this->_table_crowl_merchant_name} cmn ON cmn.id = cpl.merchant_name_id
    		LEFT JOIN {$this->_table_products} p ON p.upc_code = cpl.upc
    		WHERE 
    		    p.store_id IN (" . getStoreIdList($storeId, TRUE) . ") 
        		AND p.id= ? 
    		    AND cpl.violated=1 
        		AND cpl.last_date >= ? 
        		AND cpl.last_date <= ?
    		ORDER BY cpl.last_date DESC
		";

		$params = array(
			(int)$productId,
			strtotime($this->crawlStart),
			strtotime($this->crawlEnd)
		);
		
		$products = $this->db->query($productQuery, $params)->result();

		//echo "<pre>".$this->db->last_query(); exit;
		
		$finalProductsArray = array();
		
		foreach ($products as $product)
		{
			//TODO: store these two data inside the product_trends table instead
			//$retailPricePoint 	 = getPricePoint($product->upc_code, $storeId, 'retail_price');
			//$wholesalePricePoint = getPricePoint($product->upc_code, $storeId, 'wholesale_price');
			
			// get the "nosql" data
			$priceTrends = $this->ProductsTrends->get_by_hashkey_and_date_range($product->hashKey, strtotime($this->crawlStart), strtotime($this->crawlEnd));
			
			foreach ($priceTrends->result_object() as $priceTrend)
			{	
				//safety hack to not show incorrect violations
				if ((float)$priceTrend->mpo >= (float)$priceTrend->ap)
				{
					continue;
				}
				
				$violationArray = array(
					'productId' 	=> (int)   $priceTrend->pid,
					'upc_code' 		=> (string)$priceTrend->upc,
					'retail' 		=> (float) $priceTrend->rp,
					'wholesale' 	=> (float) $priceTrend->wp,
					'price' 		=> (float) $priceTrend->mpo,
					'map' 			=> (float) $priceTrend->ap,
					'title' 		=> (string)$product->title, //$priceTrend->t
					'marketplace' 	=> (string)$priceTrend->ar,
					'url' 			=> (string)$priceTrend->l,
					'timestamp'		=> (int)   $priceTrend->dt,
					'hash_key'		=> (string)$priceTrend->um,
					'merchant_id' 	=> (string)$product->seller_id,
					'original_name' => (string)$product->original_name,
					'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend->dt),
					'shot' 			=> (string)$priceTrend->ss
				);
				
				$finalProductsArray[] = $violationArray;
			}
		}
		
		return $finalProductsArray;
	}

	/**
	 * Returns a list of crowl_merchant_names and the number of products that
	 * violate map price for a store
	 *
	 * @param int $storeId
	 * @param int $violatedFlag
	 * @return array
	 */
	public function lastCrawlViolators($storeId, $violatedFlag = TRUE) 
	{
		
	    return $this->getPriceViolatorsProductCountByStoreId($storeId, $this->crawlStart, $this->crawlEnd);
		
	    //return $this->getPriceViolatorsProductCountByStoreId($storeId);
		
    	/*
      return array(
    		'violators' => $this->getPriceViolatorsByStoreId($storeId, $this->crawlStart, $this->crawlEnd, $violatedFlag),
    		'totals' => $this->getPriceViolatorsProductCountByStoreId($storeId, $this->crawlStart, $this->crawlEnd, null)
    	);
      */
	}

	/**
	 * Sum the "violated" flag for each price violator by store
	 *
	 * @todo fix this screwed up code - Chris
	 * @author past developers
	 * @param int $storeId
	 * @param String $dateStart { default : null }
	 * @param String $dateEnd { default : null}
	 * @param bool $violatedFlag { default : true }
	 * @return array
	 */
	protected function getPriceViolatorsProductCountByStoreId($storeId, $dateStart = null, $dateEnd = null) 
	{
		$ret = array();

		$sql = "SELECT DISTINCT
					cp.merchant_name_id
				FROM
					{$this->_table_crowl_product_list} cp
				LEFT JOIN
					{$this->_table_products} p ON p.upc_code=cp.upc
				WHERE
					p.store_id IN (" . getStoreIdList($storeId, TRUE) . ") AND cp.violated=1";

		$variables = array();
		if (isset($dateStart)) {
			$sign_and_date = $this->signAndDate($dateStart, 'Y-m-d H:i:s', '>=');
			$sql .= ' AND cp.last_date' . $sign_and_date['sign'] . '?';
			$variables[] = strtotime($sign_and_date['date']);
		}

		if (isset($dateEnd)) {
			$sign_and_date = $this->signAndDate($dateEnd, 'Y-m-d H:i:s', '<=');
			$sql .= ' AND cp.last_date' . $sign_and_date['sign'] . '?';
			$variables[] = strtotime($sign_and_date['date']);
		}

		$result = $this->db->query($sql, $variables);
		//echo $this->db->last_query()."<br>\n";

		$ret = array();
		foreach ($result->result() as $row) {
			$pSql = "SELECT SQL_CACHE
						count(distinct(cp.upc)) as count
					FROM
						{$this->_table_crowl_product_list} cp
					LEFT JOIN
						{$this->_table_products} p ON p.upc_code=cp.upc
					WHERE
						cp.merchant_name_id=? AND p.store_id IN (" . getStoreIdList($storeId, TRUE) . ") AND cp.violated=1";
			$variables = array(
				(int)$row->merchant_name_id,
			);

			if (isset($dateStart)) {
				$sign_and_date = $this->signAndDate($dateStart, 'Y-m-d H:i:s', '>=');
				$pSql .= ' AND cp.last_date' . $sign_and_date['sign'] . '?';
				$variables[] = strtotime($sign_and_date['date']);
			}

			if (isset($dateEnd)) {
				$sign_and_date = $this->signAndDate($dateEnd, 'Y-m-d H:i:s', '<=');
				$pSql .= ' AND cp.last_date' . $sign_and_date['sign'] . '?';
				$variables[] = strtotime($sign_and_date['date']);
			}

			$count = $this->db
			->query($pSql, $variables)
			->row()
			->count;
			
// TODO: remove unecessary 2nd query under the right conditions
//echo "<PRE>1\n";
//echo $this->db->last_query()."<br>\n";

			$vSql = "SELECT SQL_CACHE
						count(distinct(cp.upc)) as count
					FROM
						{$this->_table_crowl_product_list} cp
					LEFT JOIN
						{$this->_table_products} p ON p.upc_code=cp.upc
					WHERE
						cp.merchant_name_id=? AND p.store_id IN (" . getStoreIdList($storeId, TRUE) . ") AND cp.violated=1";
			$variables = array(
				(int)$row->merchant_name_id,
			);

			if (isset($dateStart)) {
				$sign_and_date = $this->signAndDate($dateStart, 'Y-m-d H:i:s', '>=');
				$vSql .= ' AND cp.last_date' . $sign_and_date['sign'] . '?';
				$variables[] = strtotime($sign_and_date['date']);
			}

			if (isset($dateEnd)) {
				$sign_and_date = $this->signAndDate($dateEnd, 'Y-m-d H:i:s', '<=');
				$vSql .= ' AND cp.last_date' . $sign_and_date['sign'] . '?';
				$variables[] = strtotime($sign_and_date['date']);
			}

			$vCount = $this->db
			->query($vSql, $variables)
			->row()
			->count;

// TODO: remove unecessary 2nd query under the right conditions
//echo "2\n";
//echo $this->db->last_query()."<br>\n";

			$ret[$row->merchant_name_id] = array(
				'violations' => $vCount,
				'products' => $count
			);
//print_r($ret); //exit;
		}

		return array('violators' => $ret);
	}

	/**
	 * Retrieve a list of crowl_merchant_name_new ids with "violated" flag by store
	 *
	 * @param int $storeId
	 * @param String $dateStart { default : null }
	 * @param String $dateEnd { default : null }
	 * @param bool $violatedFlag { default : true }
	 * @return array
	 */
	protected function getPriceViolatorsByStoreId($storeId, $dateStart = null, $dateEnd = null, $violatedFlag = true) {
		$sql = "SELECT DISTINCT
					cp.merchant_name_id
				FROM
					{$this->_table_crowl_product_list} cp
				LEFT JOIN
					{$this->_table_products} p ON p.upc_code=cp.upc
				WHERE
					p.store_id IN (" . getStoreIdList($storeId, TRUE) . ")";

		$variables = array();
		if (isset($violatedFlag)) {
			$sql .= " AND cp.violated=?";
			$variables[] = $violatedFlag ? 1 : 0;
		}

		if (isset($dateStart)) {
			$sign_and_date = $this->signAndDate($dateStart, 'Y-m-d H:i:s', '>=');
			$sql .= ' AND cp.last_date' . $sign_and_date['sign'] . '?';
			$variables[] = strtotime($sign_and_date['date']);
		}

		if (isset($dateEnd)) {
			$sign_and_date = $this->signAndDate($dateEnd, 'Y-m-d H:i:s', '<=');
			$sql .= ' AND cp.last_date' . $sign_and_date['sign'] . '?';
			$variables[] = strtotime($sign_and_date['date']);
		}

		$result = $this->db->query($sql, $variables);
		//echo $this->db->last_query();

		$ret = array();
		if ($result->num_rows())
			foreach ($result->result() as $row)
				$ret[] = $row->merchant_name_id;

			return $ret;
	}

	/**
	 * Toggle the violated flag for a crowlMerchantName selling a product
	 *
	 * @param int $crowlMerchantName
	 * @param int $productID
	 * @param bool $violated
	 */
	public function updatePriceViolator($crowlMerchantNameID, $upc, $violated, $date = null) {
		$violated = ($violated ? '1' : '0');
		//$dateTimeStamp = strtotime($date);
		//$date = $dateTimeStamp === false ? time() : $dateTimeStamp;

		// check if a record exists
		$cpl = $this->Crawl_data->crowlProductByMerchant($crowlMerchantNameID, $upc);
		if ( ! empty($cpl)) {
			$cpl->violated = $violated;
			$cpl->last_date = $date;
			$ret = $this->db
			->where('id', $cpl->id)
			->update($this->_table_crowl_product_list, $cpl);
		}
		else {
			$this->db->set(array(
					'merchant_name_id' => (int)$crowlMerchantNameID,
					'upc' => $upc,
					'violated' => $violated,
					'last_date' => $date
				));
			$ret = $this->db->insert($this->_table_crowl_product_list);
		}

		return $ret;
	}

	/**
	 * Generate an array containing a comparator and a date.
	 * The date value is checked for comparators before defaulting to $default
	 *
	 * @param String $date
	 * @param String $format
	 * @param String $default { default : '=' }
	 * @return array
	 */
	private function signAndDate($date, $format = 'Y-m-d H:i:s', $default = '=') {
		$signs = array('<', '>', '=', '!');

		$start_sign_1 = substr($date, 0, 1);
		$start_sign_2 = substr($date, 1, 2);

		if (in_array($start_sign_1, $signs)) {
			$date = substr($date, 1);
			if (in_array($start_sign_2, $signs))
				$date = substr($date, 1);
			else
				$start_sign_2 = '';

			$sign = $start_sign_1 . $start_sign_2;
		}
		else {
			$sign = $default;
		}

		$date = date($format, strtotime($date));

		return (
			array(
				'sign' => $sign,
				'date' => $date
			)
		);
	}
    
    /**
     * Get row from violator_notification_email_settings table by store ID.
     * 
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_notification_email_setting_by_store($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from($this->_table_violator_notification_email_settings);
        $this->db->where('store_id', $store_id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }
    
    /**
     * Get row from violator_notification_email_settings table by primary ID.
     *
     * @author Christophe
     * @param int $id
     * @return array
     */
    public function get_notification_email_setting_by_id($id)
    {
        $id = intval($id);
        
        $this->db->select('*');
        $this->db->from($this->_table_violator_notification_email_settings);
        $this->db->where('id', $id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    } 
    
    /**
     * Get row from violator_notifications_history table.
     * 
     * @author Christophe
     * @param int $notice_id
     * @return array
     */
    public function get_notification_by_id($notice_id)
    {
        $notice_id = intval($notice_id);
        
        $this->db->select('*');
        $this->db->from('violator_notifications_history');
        $this->db->where('id', $notice_id);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }
    
    /**
     * Get a single row from the violator_notifications table by merchant/store ID.
     * 
     * @author Christophe
     * @param int $merchant_id
     * @param int $store_id
     * @return array
     */
    public function get_violator_notification_data($merchant_id, $store_id)
    {
        $merchant_id = intval($merchant_id);
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notifications');
        $this->db->where('store_id', $store_id);
        $this->db->where('crowl_merchant_name_id', $merchant_id);
        
        $query = $this->db->get();
        
        return $query->row_array();        
    }

    /**
     * Get rows from violator_notifications_history table.
     * 
     * @author Christophe
     * @param int $store_id
     * @param string $start
     * @param string $end
     * @return array
     */
    public function get_notifications_by_store($store_id, $start, $end)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notifications_history');
        $this->db->where('store_id', $store_id);
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end);
        $this->db->order_by('date', 'desc');
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    /**
     * Get a single row from the violator_notification_email_settings for a store.
     * 
     * @author Christophe
     * @param int $store_id
     * @return array
     */
    public function get_violator_notification_email_setting_by_store($store_id)
    {
        $store_id = intval($store_id);
        
        $this->db->select('*');
        $this->db->from('violator_notification_email_settings');
        $this->db->where('store_id', $store_id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }
    
    /*Start of Duncan's Funtions */
    //function to get map enforcement settings by store_id
    public function get_map_enforcement_settings($store_id=0,$email_from=''){
        $store_id = strtolower($store_id)=='all'?0:$store_id;
        $this->db->where('store_id',$store_id);
        //$this->db->where('email_from',$email_from);
        $query = $this->db->get($this->_table_violator_notification_email_settings);
        if($query->num_rows()>0){
            //if seetings found return data as object
            return $query->row();
        }
        else{
            //if not found return false
            return false;
        }
    }
    //function to get map enforcement settings by id
    public function get_map_enforcement_settings_by_id($id=0){
        $this->db->where('id',$id);
        $query = $this->db->get($this->_table_violator_notification_email_settings);
        if($query->num_rows()>0){
            //if seetings found return data as object
            return $query->row();
        }
        else{
            //if not found return false
            return false;
        }
    }
    
    //function to get all email templates
    public function get_enforcement_email_templates($setting_id = 0){
        $this->db->where('email_settings_id',$setting_id);
        $this->db->order_by('notification_level','asc');
        $query = $this->db->get($this->_table_violator_notification_email_templates);
        if($query->num_rows()>0){
            //if email templates found return data as object
            return $query;
        }
        else{
            //if not found return false
            return false;
        }
    }
    public function get_email_template_by_id($id=0){
        $this->db->where('id',$id);
        $query = $this->db->get($this->_table_violator_notification_email_templates);
        if($query->num_rows()>0){
            //if seetings found return data as object
            return $query->row();
        }
        else{
            //if not found return false
            return false;
        }
    }
    //function to add or delete notification levels
    public function implement_map_enforcement_notification_levels($id = 0){
        //get saved map settings
        $map_settings = $this->get_map_enforcement_settings_by_id($id);
        
        //get saved notification/warning emails
        $table = $this->_table_violator_notification_email_templates;
        $this->db->where('email_settings_id',$id);
        $this->db->order_by('notification_level','asc');
        $query = $this->db->get($table);
        $saved_no_warning_emails = $query->num_rows();
        
        if($saved_no_warning_emails > $map_settings->notification_levels ){
            //if warning emails are more than notification levels we delete the excess
            $count = 1;
            foreach($query->result() as $email){
                //delete the last of the warning emails and not the first ones
                if($count>$map_settings->notification_levels){
                    $this->db->where('id', $email->id);
                    $this->db->delete($table); 
                }
                $count++;
            }
            
        }
        if($saved_no_warning_emails < $map_settings->notification_levels){
           //if the saved emails are fewer than the notification levels we add the extra
           $known_seller_html_body = $this->load->view('layouts/email/default_html_known_seller_template', array(), TRUE);
           $unknown_seller_html_body = $this->load->view('layouts/email/default_html_unknown_seller_template', array(), TRUE);
           for($count = $saved_no_warning_emails+1; $count<=$map_settings->notification_levels; $count++){

                $data = array(
                   'email_settings_id' => $id ,
                   'known_seller_html_body' => $known_seller_html_body,
                   'unknown_seller_html_body' => $unknown_seller_html_body,
                   'notification_level' => $count
                );
                $this->db->insert($table, $data); 
            }
        }
    }
    
    public function getViolatorNotification($store_id, $merchant_name_id) {
        $result = $this->db
			->where("store_id", $store_id)
			->where("crowl_merchant_name_id", $merchant_name_id)
			->get($this->_table_violator_notifications)
			->result_array();
        return count($result) > 0 ? $result[0] : FALSE;
    }
    
    /**
     * Get first violation notification that was sent to a merchant for a store.
     * 
     * @author Christophe
     * @param int $store_id
     * @param int $merchant_name_id
     * @return array
     */
    public function getViolationStreak($store_id, $merchant_name_id)
    {
        $store_id = intval($store_id);
        $merchant_name_id = intval($merchant_name_id);
        
        $this->db->select('*');
        $this->db->from('violation_streaks');
        $this->db->where('store_id', $store_id);
        $this->db->where('crowl_merchant_name_id', $merchant_name_id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }
    
    public function getLastViolationNotificationHistory($store_id, $merchant_name_id) {
    	$result =  $this->db
				->where("store_id", $store_id)
				->where("crowl_merchant_name_id", $merchant_name_id)
    			->order_by("date", "desc")
				->limit(1)
    			->get($this->_table_violator_notifications_history)
				->result_array();

    	return count($result) > 0 ? $result[0] : FALSE;
    }
	
    public function countViolationNotificationHistory($store_id, $merchant_name_id, $email_level) {
    	$result =  $this->db
				->select("email_level")
				->where("store_id", $store_id)
				->where("crowl_merchant_name_id", $merchant_name_id)
				->where("email_level", $email_level)
    			->get($this->_table_violator_notifications_history);
    	return $this->db->count_all_results();
    }
	
    public function getLastViolationNotificationLevel($store_id, $merchant_name_id) {
    	$result = $this->db
					->select_max("email_level")
					->where("store_id", $store_id)
					->where("crowl_merchant_name_id", $merchant_name_id)
					->get($this->_table_violator_notifications_history)
					->result_array();
    	return $result[0]['email_level'] * 1;
    }
    
    /*End of Duncan's Funtions */
    
    /**
     * Insert a single row into the violator_notifications table.
     * 
     * @author Christophe
     * @param array $insert_data
     * @return int
     */
    public function insert_violator_notification($insert_data)
    {
        $this->db->insert('violator_notifications', $insert_data);
        
        return $this->db->insert_id();        
    }    
    
    /**
     * Update row in violator_notification_email_templates table.
     * 
     * @author Christophe
     * @param int $template_id
     * @param array $update_data
     */
    public function update_email_template($template_id, $update_data)
    {
        $template_id = intval($template_id);
        
        $this->db->where('id', $template_id);
        $this->db->update($this->_table_violator_notification_email_templates, $update_data);        
    }
    
    /**
     * Update a single row in the violator_notifications table.
     * 
     * @author Christophe
     * @param int $row_id
     * @param array $update_data
     */
    public function update_violator_notification($row_id, $update_data)
    {
        $row_id = intval($row_id);
        
        $this->db->where('id', $row_id);
        $this->db->update('violator_notifications', $update_data);        
    }
}
