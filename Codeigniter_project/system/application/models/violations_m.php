<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Violations_m extends MY_Model {

  function __construct() {
    parent::__construct();
    //$this->ci->load->library('amzdb');
	$this->ci->load->model('products_trends_m', 'ProductsTrends');

    if ( ! isset($this->ci->Crawl_data))
		$this->ci->load->model('crawl_data_m', 'Crawl_data');
	$range = $this->ci->Crawl_data->last_crawl_range();
    $this->crawlStart = $range['from'];
    $this->crawlEnd = $range['to'];
  }


  function searchProductPricingViolations($request_info=array(), $skip_product_id = false){
		//create range key from cron log
		$from = $request_info['date_from'];
		$to = $request_info['date_to'];
		$crons = getLast24HoursCronIds($from, $to);
		$last24HoursCronsData = getLast24HoursCronLog($crons);
		foreach($last24HoursCronsData as $l24hCD){
			if(strtotime($l24hCD->start_datetime) < $from){
				$from = strtotime($l24hCD->start_datetime);
			}
			if($l24hCD->end_datetime != '0000-00-00 00:00:00' && strtotime($l24hCD->end_datetime) > $to){
				$to = strtotime($l24hCD->end_datetime);
			}
		}

		// TODO: competitor_map
		if( ! empty($request_info['competitor_map'])){
			// get the intersection of the two sets of merchants
			$sql = "SELECT cmn.id
				FROM " . $this->_table_products . " p
				LEFT JOIN " . $this->_table_crowl_product_list . " cpl ON cpl.upc=p.upc_code
				LEFT JOIN " . $this->_table_crowl_merchant_name . " cmn ON cmn.id=cpl.merchant_name_id
				WHERE p.id = ?";
			$merchant_query = $sql . " AND cmn.id IN ($sql)";
			$merchants_intersection = array();
			$lookup_merchants = array();
			foreach($request_info['competitor_map'] as $prodId => $owner_product){
				$result = $this->db->query($merchant_query, array($prodId, $owner_product['id']))->result_array();
				for($i = 0, $n = count($result); $i < $n; $i++){
					$merchant_id = $result[$i]['id'];
					$merchants_intersection[$merchant_id] = true;
				}
			}
			// get the intersection of merchants filter and the set we just made
			if(isset($request_info['merchants'][0]) AND $request_info['merchants'][0] !== 'all'){
				foreach($request_info['merchants'] as $merchant_id){
					if(isset($merchants_intersection[$merchant_id]))
						$lookup_merchants[$merchant_id] = true;
				}
			}
			else{
				$lookup_merchants = $merchants_intersection;
			}
			$request_info['merchants'] = array_keys($lookup_merchants);
		}

		$where = '1=1';
		$MarketFilter = array();
		$orderBy = '';
		$orderByTmp = 'products.id';

		$whereVals = requestInfoWhereValues($request_info, $MarketFilter, $orderByTmp);
		if( ! $skip_product_id AND isset($whereVals['products'])){
			$request_info['product_ids'] = array_filter($request_info['product_ids']);
			$where .= ' AND products.id ' . $whereVals['products'] ;
			$orderBy = " ORDER BY $orderByTmp ";
		}
		if(isset($whereVals['marketplaces'])){
			$where .= ' AND crowl_merchant_name_new.marketplace ' . $whereVals['marketplaces'];
			$MarketFilter = $whereVals['marketplaces'];
		}
		if(isset($whereVals['merchants']))
			$where .= ' AND crowl_merchant_name_new.id ' . $whereVals['merchants'];
		if(isset($whereVals['store']))
			$where .= ' AND products.store_id ' . $whereVals['store'];

		//TODO: change hashkey to sellerId instead of merchant_name
		$productQuery = "SELECT
				concat(crowl_merchant_name_new.seller_id,'#',crowl_product_list_new.upc) as hashKey,
				crowl_merchant_name_new.seller_id,
				products.upc_code,
				products.id,
				products.store_id,
				crowl_merchant_name_new.merchant_name,
				crowl_merchant_name_new.original_name
			FROM crowl_product_list_new
			INNER JOIN crowl_merchant_name_new ON crowl_merchant_name_new.id = crowl_product_list_new.merchant_name_id
			LEFT JOIN products ON products.upc_code = crowl_product_list_new.upc
			WHERE $where
			GROUP BY hashKey
			$orderBy";
				//products.title,
				//products.price_floor,
				//products.retail_price,
				//products.wholesale_price,
		//echo "<pre>$productQuery<br>\n";exit;
		$products = $this->db->query($productQuery)->result();

		$finalProductsArray = array();
		foreach($products as $product){
			
			//TODO: store these two data inside the product_trends table instead
			$retailPricePoint 	 = getPricePoint($product->upc_code, $product->store_id, 'retail_price');
			$wholesalePricePoint = getPricePoint($product->upc_code, $product->store_id, 'wholesale_price');
			
			// get the "nosql" data
			$priceTrends = $this->ProductsTrends->get_by_hashkey_and_date_range_and_marketplace($product->hashKey, $from, $to, $MarketFilter);
			
			foreach($priceTrends->result_object() as $priceTrend){
				
				//safety hack to not show incorrect violations
				if((float)$priceTrend->mpo >= (float)$priceTrend->ap)
					continue;
				
				$violationTrendArray = array(
					'productId' 	=> (int)   $product->id,
					'upc_code' 		=> (string)$product->upc_code,
					'retail' 		=> (float) $retailPricePoint,
					'wholesale' 	=> (float) $wholesalePricePoint,
					'price' 		=> (float) $priceTrend->mpo,
					'map' 			=> (float) $priceTrend->ap,
					'title' 		=> (string)$priceTrend->t,
					'marketplace' 	=> (string)$priceTrend->ar,
					'url' 			=> (string)$priceTrend->l,
					'timestamp'		=> (int)   $priceTrend->dt,
					'hash_key'		=> (string)$product->hashKey,
					'merchant_id' 	=> (string)$product->seller_id,
					'date' 			=> (string)date('m/d/Y G:i:s', (int)$priceTrend->dt),
					'shot' 			=> (string)$priceTrend->ss
				);
				$finalProductsArray[$product->id][] = $violationTrendArray;
			}
		}
		return $finalProductsArray;
	}

	//--------------------------------------------------------------------------------------
	//  DEPRECATED
	//  function getPricingViolations($storeId, $productInfo, $start = null, $end = null){
	//		$where = '';
	//		$MarketFilter = array();
	//		$whereVals = requestInfoWhereValues($productInfo, $MarketFilter);
	//
	//		if(isset($productInfo['api_type'])){
	//			$where .= ' AND cmn.marketplace ' . $whereVals['marketplaces'];
	//		}
	//		if(isset($productInfo['merchants'])){
	//			$where .= ' AND cmn.id ' . $whereVals['merchants'];
	//		}
	//		if(isset($productInfo['product_ids'])){
	//			$where .= ' AND p.id ' . $whereVals['products'];
	//		}
	//		if($start && $end){
	//			$where .= " AND cpl.last_date>$start and cpl.last_date<$end";
	//		}
	//
	//		//now setup the violation data
	//		$productQuery = "SELECT
	//				concat(cmn.seller_id,'#',cpl.upc) as hashKey,
	//				cmn.seller_id,
	//				p.title,
	//				p.upc_code,
	//				p.id,
	//				p.price_floor,
	//				p.retail_price,
	//				p.wholesale_price,
	//				cmn.merchant_name,
	//				cmn.original_name,
	//				cpl.last_date
	//			FROM
	//				crowl_product_list_new cpl
	//			LEFT JOIN crowl_merchant_name_new cmn ON cmn.id = cpl.merchant_name_id
	//			LEFT JOIN products p ON p.upc_code = cpl.upc
	//			WHERE p.store_id = $storeId AND cpl.violated=1 $where
	//			GROUP BY hashKey";
	//		//echo $productQuery;//exit;
	//		$products = $this->db->query($productQuery)->result();
	//
	//		//we create a new array of products containing only violations
	//		$violationProducts = array();
	//		foreach($products as $product){
	//			if (empty($product->hashKey) OR empty($product->last_date)) {
	//				log_message('error', 'Missing hash or range key in violation reporting: ' . var_export($product, true));
	//				continue;
	//			}
	//			//if we organize these by seller - we could make fewer queries with batching...
	//			$violationArray[] = array(
	//				'HashKeyElement' => $product->hashKey,
	//				'RangeKeyElement' => $product->last_date);
	//			$violationProducts[$product->id][$product->hashKey] = array(
	//				'seller_id' => $product->seller_id,
	//				'dt' => $product->last_date);
	//		}
	//
	//		$violations = array();
	//		if( ! empty($violationArray)) $violations = $this->amzdb->batchGetItem($this->_dynamo_violations, $violationArray);
	//		//var_dump($violations);exit;
	//
	//		$finalProductsArray = $curProduct = array();
	//		for($i=0, $n=sizeof($violations); $i<$n; $i++){
	//			$us = explode("#", $violations[$i]->um->S);
	//			$upc = $us[1];
	//			$pInfo = getProductIdByUPC($us[1], $storeId);
	//			$prodId = $pInfo['id'];
	//			$hashKey = (string)$violations[$i]->um->S;
	//			$stamp = (int)$violations[$i]->dt->N;
	//
	//			$priceTrend = $this->amzdb->executeQuery($this->_dynamo_products_trends, $hashKey, $stamp);
	//			//var_dump($priceTrend);exit;
	//			//hack to not show incorrect violations - will fix itself with crawl fix
	//			if((float)$priceTrend->body->Items->mpo->N >= (float)$priceTrend->body->Items->ap->N) continue;
	//
	//			$violationTrendArray = array(
	//				'productId' => $prodId,
	//				'upc_code' => $upc,
	//				'retail' => getPricePoint($upc, $storeId, 'retail_price', $stamp),
	//				'wholesale' => getPricePoint($upc, $storeId, 'wholesale_price', $stamp),
	//				'price' => (float)$priceTrend->body->Items->mpo->N,
	//				'map' => (float)$priceTrend->body->Items->ap->N,
	//				'title' => (string)$priceTrend->body->Items->t->S,
	//				'marketplace' => (string)$priceTrend->body->Items->ar->S,
	//				'url' => (string)$priceTrend->body->Items->l->S,
	//				'timestamp'=> $stamp,
	//				'hash_key'=> $hashKey,
	//				'merchant_id' => $violationProducts[$prodId][$hashKey]['seller_id'],
	//				'date' => date('m/d/Y G:i:s', $stamp),
	//				'shot' =>(string)$violations[$i]->ss->S);
	//			$curProduct[$prodId][] = $violationTrendArray;
	//
	//			$result = msort($curProduct[$prodId],'dt');
	//			$finalProductsArray[$prodId] = $result;
	//		}
	//log_message('info', "Dynamo conversion, output array of this function:
	//    File: ".__FILE__."
	//    Line: ".__LINE__."
	//    Class: ".__CLASS__."
	//    Function: ".__FUNCTION__."
	//    Method: ".__METHOD__."
	//    " . print_r($finalProductsArray , true)
	//);
	//
	//		return $finalProductsArray;
	//  }

}
